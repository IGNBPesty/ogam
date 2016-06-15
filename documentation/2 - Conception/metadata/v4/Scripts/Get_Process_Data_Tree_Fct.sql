
-- DROP SEQUENCE ui_object_tree_tmp_id_seq;
CREATE SEQUENCE ui_object_tree_tmp_id_seq
  INCREMENT 1
  MINVALUE 0
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
ALTER TABLE ui_object_tree_tmp_id_seq
  OWNER TO postgres;

-- DROP SEQUENCE ui_object_tree_tmp_order_seq;
CREATE SEQUENCE ui_object_tree_tmp_order_seq
  INCREMENT 1
  MINVALUE 0
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
ALTER TABLE ui_object_tree_tmp_order_seq
  OWNER TO postgres;

-- DROP TYPE IF EXISTS object_node;
CREATE TYPE object_node AS (
	code character varying(32),
	parent_code character varying(32),
	type character varying(32),
	label character varying(64),
	"order" smallint,
	object_type character varying(32),
	object_pk integer
);

-- DROP TYPE IF EXISTS exec_sql_return;
CREATE TYPE exec_sql_return AS (
	label character varying(64),
	pk integer
);

-- DROP TYPE IF EXISTS json_object;
CREATE TYPE json_object AS (
	type character varying(32),
	pk integer
);

-- DROP TYPE IF EXISTS json_node;
CREATE TYPE json_node AS (
	id character varying(32),
	type character varying(32),
	label character varying(64),
	--definition character varying(256),
	object json_object,
	children json[]
);

-- DROP FUNCTION exec_sql(text, integer);
CREATE OR REPLACE FUNCTION exec_sql(request text, pk integer) RETURNS SETOF exec_sql_return
AS $$
DECLARE final_request text;
BEGIN
	final_request := 'SELECT label::character varying(64), pk::integer FROM (' || request || ') as foo;';
	RETURN QUERY EXECUTE final_request USING pk;
END
$$ LANGUAGE plpgsql;

-- DROP FUNCTION get_children_nodes(character varying, integer);
CREATE OR REPLACE FUNCTION get_children_nodes(pcode character varying(32), pobject_pk integer) RETURNS SETOF object_node
AS $$
DECLARE
	vsql_objects_query text;
	vchild_object_node character varying(32);
BEGIN
	-- Récupération de la requête sql du noeud
	SELECT sql_objects_query, child_object_node INTO STRICT vsql_objects_query, vchild_object_node
	FROM metadata.abstract_node_view 
	WHERE code = pcode;
	-- Récupération des enfants du noeud
	PERFORM setval('ui_object_tree_tmp_order_seq',0);
	RETURN QUERY 
	SELECT 	vchild_object_node,
		pcode::character varying(32) as parent_code,
		anv.type,
		obj.label,
		nextval('ui_object_tree_tmp_order_seq')::smallint as "order",
		anv.object_type,
		obj.pk as object_pk
	FROM exec_sql(vsql_objects_query, pobject_pk) as obj
	JOIN metadata.abstract_node_view anv ON anv.code = vchild_object_node
	ORDER BY "order";
	EXCEPTION
	WHEN NO_DATA_FOUND THEN
	    RETURN;
	WHEN TOO_MANY_ROWS THEN
	    RAISE EXCEPTION 'Too many definition for this node';
END
$$ LANGUAGE plpgsql;

SELECT setval('ui_object_tree_tmp_id_seq',0);
WITH RECURSIVE json_node_tree AS ( 
	WITH node_tree AS ( 
		WITH RECURSIVE ui_object_tree_tmp AS (
			-- id, parent_id, code, parent_code, type, label, order, object_type, object_pk, depth
			-- Récupération de la racine
			SELECT 	nextval('ui_object_tree_tmp_id_seq') as id, 
				null::bigint as parent_id,
				code,
				parent_code,
				"type",
				"label", 
				"order", 
				object_type,
				null::integer as object_pk,
				0::integer as depth
			FROM metadata.abstract_node_tree_view
			WHERE code = parent_code -- Select the root
			UNION
			(
			-- Création d'une nouvelle table pour parer la limitation d'usage sur la table récursive
			WITH last_rows AS ( SELECT * FROM ui_object_tree_tmp )
			-- Récupération des branches
			SELECT 	nextval('ui_object_tree_tmp_id_seq') as id, 
				tmp.id,
				antv.code,
				antv.parent_code,
				antv."type",
				antv."label", 
				antv."order", 
				tmp.object_type,
				tmp.object_pk,
				tmp.depth + 1 as depth
			FROM last_rows tmp
			JOIN metadata.abstract_node_tree_view antv ON antv.parent_code = tmp.code AND antv.code != antv.parent_code -- Unselect the root
			WHERE tmp.type != 'GROUP'
			UNION
			-- Récupération des objets d'un groupe
			SELECT	nextval('ui_object_tree_tmp_id_seq') as id, 
				tmp.id,
				cn.code,
				tmp.code as parent_code,
				cn."type",
				cn."label", 
				cn."order", 
				cn.object_type,
				cn.object_pk as object_pk,
				tmp.depth + 1 as depth
			FROM last_rows tmp
			JOIN get_children_nodes(tmp.code, tmp.object_pk) as cn ON cn.parent_code = tmp.code
			WHERE tmp.type = 'GROUP'
			)
		) --select * from ui_object_tree_tmp
		-- /ui_object_tree_tmp
		-- Ajout du json et de is_leaf
		SELECT id, parent_id, row_to_json(row(id, type, label, CASE WHEN tmp.type = 'OBJECT' THEN row(object_type, object_pk)::json_object ELSE null END, null)::json_node)::jsonb as node_json, depth as node_depth, "order" as node_order, (((select count(tmp2.id) from ui_object_tree_tmp tmp2 where tmp2.parent_id = tmp.id) = 0 ) and tmp.type='OBJECT')::boolean as is_leaf
		FROM ui_object_tree_tmp tmp
		ORDER BY parent_id
	)
	-- /node_tree
	-- Récupération des feuilles
	-- id, parent_id, node_json, node_order, node_depth, depth, debug
	SELECT id, parent_id, node_json, node_order, node_depth, (SELECT MAX(node_depth) FROM node_tree) as depth, '' as debug 
	FROM node_tree
	WHERE is_leaf
	UNION
	(
		-- Création d'une nouvelle table pour parer la limitation d'usage sur la table récursive
		WITH last_rows AS ( SELECT * FROM json_node_tree )
		-- Agrégation et incorporation des JSON pour la profondeur en cours
		SELECT parent.id, parent.parent_id, (json_merge(parent.node_json::json, json_build_object('children',(json_agg(child.node_json ) OVER(PARTITION BY child.parent_id ORDER BY child.node_order ASC ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING)))))::jsonb as node_json, parent.node_order, parent.node_depth as node_depth, child.node_depth - 1 as depth, ''
		FROM last_rows child
		JOIN node_tree parent ON parent.id = child.parent_id
		WHERE child.node_depth = child.depth
		UNION
		-- Récupération des containers feuilles encore non traités
		SELECT child2.id, child2.parent_id, child2.node_json::jsonb, child2.node_order, child2.node_depth, child.depth - 1 as depth, 'Récupération'
		FROM last_rows child
		JOIN node_tree child2 ON child2.id = child.id
		WHERE child2.is_leaf and child.node_depth != child.depth
	)
)
-- /json_node_tree
--SELECT * FROM json_node_tree;
SELECT node_json FROM json_node_tree WHERE parent_id is NULL;
