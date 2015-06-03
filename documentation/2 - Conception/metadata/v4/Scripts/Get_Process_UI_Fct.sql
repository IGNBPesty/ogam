set search_path = metadata;
/*
DROP TYPE IF EXISTS json_container;
CREATE TYPE json_container AS (
	id character varying(32),
	type character varying(32),
	subtype character varying(32),
	label character varying(64),
	definition character varying(256),
	children json[]
);

DROP TYPE IF EXISTS json_ui_form_field;
CREATE TYPE json_ui_form_field AS (
	id character varying(32),
	type character varying(32),
	label character varying(64),
	definition character varying(256),
	position smallint,
	field character varying(32),
	subtype character varying(32),
	field_type character varying(32),
	default_value character varying(256),
	default_text character varying(256),
	decimals smallint,
	mask character varying(128),
	subposition smallint,
	width smallint,
	height smallint,
	x smallint,
	y smallint,
	is_mandatory boolean,
	is_disabled boolean,
	is_hidden boolean
);

DROP TYPE IF EXISTS json_ui_request_field;
CREATE TYPE json_ui_request_field AS (
	id character varying(32),
	type character varying(32),
	label character varying(64),
	definition character varying(256),
	position smallint,
	field character varying(32),
	subtype character varying(32),
	field_type character varying(32),
	default_value character varying(256),
	default_text character varying(256),
	decimals smallint,
	mask character varying(128),
	is_criteria boolean,
	is_column boolean,
	is_default_criteria boolean,
	is_default_column boolean
);
*/
WITH RECURSIVE object_tree AS (
	WITH RECURSIVE container_tree AS (
		-- *** 1. Récupération des objets déclarés dans l'arbre des objets du processus
		WITH process_objects AS (
			SELECT DISTINCT relation_set, unnest(regexp_split_to_array(objects_types, ',')) as container
			FROM process_relation_set prs
			JOIN container_relation cr on cr.relation_set = prs.container_relation_set
			JOIN ui_object_tree using(relation_set,container_1,container_2,relation_number)
			WHERE prs.process = 'CONTACT_SITE_EDITION'
		)
		-- *** 2. Récupération de l'arbre d'interface pour chaque objet
		SELECT po.relation_set, c.container as object_ct, c.container, '*'::character varying(32) as parent_ct, 0 as depth, row_to_json(row(c.container, c.type, c.subtype, c.label, c.definition, '{}')::json_container)::jsonb as container_json, false as is_leaf--, '{}'::text[] as path
		FROM process_objects po
		JOIN container c using(container)
		UNION
		(
			-- *** 2.1 Création d'une nouvelle table pour parer la limitation d'usage sur la table récursive (container_tree)
			WITH ct_last_rows AS ( SELECT * FROM container_tree )
			-- *** 2.2 Récupération des containers
			SELECT ct.relation_set, ct.object_ct, c2.container, ct.container as parent_ct, ct.depth + 1 as depth, row_to_json(row(c2.container, c2.type, c2.subtype, c2.label, c2.definition, '{}')::json_container)::jsonb as container_json, ((SELECT count(*) FROM (SELECT container_1 FROM ui_container_tree count_uict WHERE count_uict.container_1 = c2.container and count_uict.relation_set = ct.relation_set UNION SELECT component FROM component count_c WHERE count_c.container = c2.container) as foo)::int = 0)::boolean as is_leaf--, path || ct.container::text
			FROM ct_last_rows as ct
			JOIN ui_container_tree uict on uict.container_1 = ct.container and uict.relation_set = ct.relation_set
			JOIN container AS c2 on c2.container = uict.container_2
			UNION
			-- *** 2.3 Récupération des champs de formulaire
			SELECT ct.relation_set, ct.object_ct, c.component, ct.container as parent_ct, ct.depth + 1 as depth, row_to_json(row(c.component, c.type, c.label, c.definition, c.position, c.field, uif.subtype, uif.field_type, uif.default_value, uif.default_text, uif.decimals, uif.mask, uiff.subposition, uiff.width, uiff.height, uiff.x, uiff.y, uiff.is_mandatory, uiff.is_disabled, uiff.is_hidden)::json_ui_form_field)::jsonb as container_json, true as is_leaf--, path || ct.container::text
			FROM ct_last_rows as ct
			JOIN component c on c.container = ct.container
			JOIN ui_field uif on uif.component = c.component
			INNER JOIN ui_form_field uiff on uiff.component = c.component
			UNION
			-- *** 2.4 Récupération des champs de requête
			SELECT ct.relation_set, ct.object_ct, c.component, ct.container as parent_ct, ct.depth + 1 as depth, row_to_json(row(c.component, c.type, c.label, c.definition, c.position, c.field, uif.subtype, uif.field_type, uif.default_value, uif.default_text, uif.decimals, uif.mask, uirf.is_criteria, uirf.is_column, uirf.is_default_criteria, uirf.is_default_column)::json_ui_request_field)::jsonb as container_json, true as is_leaf--, path || ct.container::text
			FROM ct_last_rows as ct
			JOIN component c on c.container = ct.container
			JOIN ui_field uif on uif.component = c.component
			INNER JOIN ui_request_field uirf on uirf.component = c.component
		)
	) -- SELECT * FROM container_tree order by object_ct, depth ASC
	-- *** 3. Transformation en JSON de l'arbre d'interface pour chaque objet
	SELECT object_ct, container, parent_ct, container_json::jsonb, depth as ct_depth, (SELECT MAX(depth) FROM container_tree) as depth, '' as debug
	FROM container_tree
	WHERE is_leaf
	UNION
	(
		-- *** 3.1 Création d'une nouvelle table pour parer la limitation d'usage sur la table récursive (object_tree)
		WITH ot_last_rows AS ( SELECT * FROM object_tree )
		-- *** 3.2 Agrégation et incorporation des JSON pour la profondeur en cours
		SELECT ct.object_ct, ct.container, ct.parent_ct, (json_merge(ct.container_json::json, json_build_object('children',(json_agg(otlr.container_json) OVER(PARTITION BY ct.object_ct, ct.container, ct.depth order by ct.depth DESC)))))::jsonb as container_json, ct.depth as ct_depth, otlr.ct_depth - 1 as depth, ''
		FROM ot_last_rows otlr
		JOIN container_tree ct on ct.container = otlr.parent_ct and ct.object_ct = otlr.object_ct
		WHERE otlr.ct_depth = otlr.depth
		UNION
		-- *** 3.3 Récupération des containers feuilles encore non traités
		SELECT ct.object_ct, ct.container, ct.parent_ct, ct.container_json::jsonb, ct.depth as ct_depth,  otlr.depth - 1 as depth, 'Récupération'
		FROM ot_last_rows otlr
		JOIN container_tree ct on ct.container = otlr.container and ct.object_ct = otlr.object_ct
		WHERE ct.is_leaf and otlr.ct_depth != otlr.depth
	)	
)
-- SELECT * FROM object_tree ORDER BY object_ct, depth DESC, ct_depth DESC, debug ASC;
SELECT parent_ct as object, container_json as ui_json FROM object_tree WHERE ct_depth = 1;

/* https://gist.github.com/matheusoliveira/9488951 */
