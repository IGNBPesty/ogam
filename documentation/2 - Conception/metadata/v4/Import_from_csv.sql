SET client_encoding TO 'UTF8';
set search_path = metadata;

--
-- Remove integrity contraints
--
ALTER TABLE ui_container_tree DROP CONSTRAINT FK_container_relation1;
ALTER TABLE ui_object DROP CONSTRAINT FK_container3;
ALTER TABLE application_process DROP CONSTRAINT FK_application;
ALTER TABLE field DROP CONSTRAINT FK_attribute;
ALTER TABLE process_check DROP CONSTRAINT FK_check;
ALTER TABLE "attribute" DROP CONSTRAINT FK_class;
ALTER TABLE class_tree DROP CONSTRAINT FK_class1;
ALTER TABLE class_tree DROP CONSTRAINT FK_class2;
ALTER TABLE code_tree DROP CONSTRAINT FK_code;
ALTER TABLE code_taxref DROP CONSTRAINT FK_code_tree;
ALTER TABLE code_set DROP CONSTRAINT FK_code1;
ALTER TABLE code_set DROP CONSTRAINT FK_code2;
ALTER TABLE code_composition DROP CONSTRAINT FK_code3;
ALTER TABLE code_composition DROP CONSTRAINT FK_code4;
ALTER TABLE code_composition DROP CONSTRAINT FK_code5;
ALTER TABLE db_table_column DROP CONSTRAINT FK_component;
ALTER TABLE file_column DROP CONSTRAINT FK_component1;
ALTER TABLE ui_field DROP CONSTRAINT FK_component2;
ALTER TABLE component_mapping_method DROP CONSTRAINT FK_component3;
ALTER TABLE component DROP CONSTRAINT FK_container;
ALTER TABLE db_table_relation DROP CONSTRAINT FK_container_relation;
ALTER TABLE file_container DROP CONSTRAINT FK_container1;
ALTER TABLE db_container DROP CONSTRAINT FK_container2;
ALTER TABLE container_relation DROP CONSTRAINT FK_container4;
ALTER TABLE container_relation DROP CONSTRAINT FK_container5;
ALTER TABLE process_container DROP CONSTRAINT FK_container6;
ALTER TABLE field_context DROP CONSTRAINT FK_context;
ALTER TABLE lot_context DROP CONSTRAINT FK_context1;
ALTER TABLE context_value DROP CONSTRAINT FK_context2;
ALTER TABLE context_code DROP CONSTRAINT FK_context3;
ALTER TABLE db_table DROP CONSTRAINT FK_db_container;
ALTER TABLE db_table_relation DROP CONSTRAINT FK_db_table;
ALTER TABLE file_column DROP CONSTRAINT FK_db_table_column;
ALTER TABLE ui_field DROP CONSTRAINT FK_db_table_column1;
ALTER TABLE db_table_relation DROP CONSTRAINT FK_db_table1;
ALTER TABLE db_table_column DROP CONSTRAINT FK_field;
ALTER TABLE field_context DROP CONSTRAINT FK_field1;
ALTER TABLE process DROP CONSTRAINT FK_lot;
ALTER TABLE field DROP CONSTRAINT FK_lot1;
ALTER TABLE lot_context DROP CONSTRAINT FK_lot2;
ALTER TABLE lot_set DROP CONSTRAINT FK_lot3;
ALTER TABLE lot_set DROP CONSTRAINT FK_lot4;
ALTER TABLE component_mapping_method DROP CONSTRAINT FK_mapping_method;
ALTER TABLE context_code DROP CONSTRAINT FK_mode;
ALTER TABLE application_process DROP CONSTRAINT FK_process;
ALTER TABLE process_check DROP CONSTRAINT FK_process1;
ALTER TABLE process_container DROP CONSTRAINT FK_process2;
ALTER TABLE ui_form_field DROP CONSTRAINT FK_ui_field;
ALTER TABLE ui_request_field DROP CONSTRAINT FK_ui_field1;
ALTER TABLE field DROP CONSTRAINT FK_unit;
ALTER TABLE code DROP CONSTRAINT FK_unit1;
ALTER TABLE code_sql DROP CONSTRAINT FK_unit2;
ALTER TABLE unit_constraint DROP CONSTRAINT FK_unit3;

--
-- Remove old data
--
DELETE FROM application;
DELETE FROM application_process;
DELETE FROM attribute;
DELETE FROM "check" where check_id <= 1200;
DELETE FROM "class";
DELETE FROM class_tree;
DELETE FROM code;
DELETE FROM code_composition;
DELETE FROM code_set;
DELETE FROM code_sql;
DELETE FROM code_taxref;
DELETE FROM code_tree;
DELETE FROM component;
DELETE FROM component_mapping_method;
DELETE FROM container;
DELETE FROM container_relation;
DELETE FROM context;
DELETE FROM context_code;
DELETE FROM context_value;
DELETE FROM db_container;
DELETE FROM db_table;
DELETE FROM db_table_column;
DELETE FROM db_table_relation;
DELETE FROM field;
DELETE FROM field_context;
DELETE FROM file_column;
DELETE FROM file_container;
DELETE FROM lot;
DELETE FROM lot_context;
DELETE FROM lot_set;
DELETE FROM mapping_method;
DELETE FROM process;
DELETE FROM process_check;
DELETE FROM process_container;
DELETE FROM "translation";
DELETE FROM ui_container_tree;
DELETE FROM ui_field;
DELETE FROM ui_form_field;
DELETE FROM ui_object;
DELETE FROM ui_request_field;
DELETE FROM unit;
DELETE FROM unit_constraint;

--
-- Add new data
--
COPY application from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/application.csv' with delimiter ';' null '';
COPY application_process from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/application_process.csv' with delimiter ';' null '';
COPY attribute from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/attribute.csv' with delimiter ';' null '';
COPY "check" where check_id <= 1200 from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/check where check_id <= 1200.csv' with delimiter ';' null '';
COPY "class" from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/class.csv' with delimiter ';' null '';
COPY class_tree from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/class_tree.csv' with delimiter ';' null '';
COPY code from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/code.csv' with delimiter ';' null '';
COPY code_composition from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/code_composition.csv' with delimiter ';' null '';
COPY code_set from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/code_set.csv' with delimiter ';' null '';
COPY code_sql from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/code_sql.csv' with delimiter ';' null '';
COPY code_taxref from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/code_taxref.csv' with delimiter ';' null '';
COPY code_tree from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/code_tree.csv' with delimiter ';' null '';
COPY component from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/component.csv' with delimiter ';' null '';
COPY component_mapping_method from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/component_mapping_method.csv' with delimiter ';' null '';
COPY container from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/container.csv' with delimiter ';' null '';
COPY container_relation from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/container_relation.csv' with delimiter ';' null '';
COPY context from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/context.csv' with delimiter ';' null '';
COPY context_code from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/context_code.csv' with delimiter ';' null '';
COPY context_value from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/context_value.csv' with delimiter ';' null '';
COPY db_container from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/db_container.csv' with delimiter ';' null '';
COPY db_table from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/db_table.csv' with delimiter ';' null '';
COPY db_table_column from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/db_table_column.csv' with delimiter ';' null '';
COPY db_table_relation from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/db_table_relation.csv' with delimiter ';' null '';
COPY field from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/field.csv' with delimiter ';' null '';
COPY field_context from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/field_context.csv' with delimiter ';' null '';
COPY file_column from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/file_column.csv' with delimiter ';' null '';
COPY file_container from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/file_container.csv' with delimiter ';' null '';
COPY lot from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/lot.csv' with delimiter ';' null '';
COPY lot_context from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/lot_context.csv' with delimiter ';' null '';
COPY lot_set from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/lot_set.csv' with delimiter ';' null '';
COPY mapping_method from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/mapping_method.csv' with delimiter ';' null '';
COPY process from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/process.csv' with delimiter ';' null '';
COPY process_check from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/process_check.csv' with delimiter ';' null '';
COPY process_container from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/process_container.csv' with delimiter ';' null '';
COPY "translation" from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/translation.csv' with delimiter ';' null '';
COPY ui_container_tree from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/ui_container_tree.csv' with delimiter ';' null '';
COPY ui_field from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/ui_field.csv' with delimiter ';' null '';
COPY ui_form_field from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/ui_form_field.csv' with delimiter ';' null '';
COPY ui_object from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/ui_object.csv' with delimiter ';' null '';
COPY ui_request_field from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/ui_request_field.csv' with delimiter ';' null '';
COPY unit from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/unit.csv' with delimiter ';' null '';
COPY unit_constraint from 'D:/DONNEES/Workspace/OGAM/documentation/2 - Conception/metadata/v4/unit_constraint.csv' with delimiter ';' null '';

--
-- Fill the empty label and definition for the need of the tests
--
UPDATE metadata.translation t
   SET label= 'EN...' || t2.label
   FROM metadata.translation t2
 WHERE t.table_format = t2.table_format and t.row_pk = t2.row_pk and t.lang = 'EN' and t2.lang = 'FR' and t.label is null;

 UPDATE metadata.translation t
   SET definition= 'EN...' || t2.definition
   FROM metadata.translation t2
 WHERE t.table_format = t2.table_format and t.row_pk = t2.row_pk and t.lang = 'EN' and t2.lang = 'FR' and t.definition is null;

--
-- Restore Integrity contraints
--
ALTER TABLE ui_container_tree
  ADD CONSTRAINT FK_container_relation1 
    FOREIGN KEY (container_1, container_2)
      REFERENCES container_relation;

ALTER TABLE ui_object
  ADD CONSTRAINT FK_container3 
    FOREIGN KEY (container)
      REFERENCES container;

ALTER TABLE application_process
  ADD CONSTRAINT FK_application 
    FOREIGN KEY (application)
      REFERENCES application;

ALTER TABLE field
  ADD CONSTRAINT FK_attribute 
    FOREIGN KEY ("attribute")
      REFERENCES "attribute";

ALTER TABLE process_check
  ADD CONSTRAINT FK_check 
    FOREIGN KEY ("check")
      REFERENCES "check";

ALTER TABLE "attribute"
  ADD CONSTRAINT FK_class 
    FOREIGN KEY ("class")
      REFERENCES "class";

ALTER TABLE class_tree
  ADD CONSTRAINT FK_class1 
    FOREIGN KEY (class_1)
      REFERENCES "class";

ALTER TABLE class_tree
  ADD CONSTRAINT FK_class2 
    FOREIGN KEY (class_2)
      REFERENCES "class";

ALTER TABLE code_tree
  ADD CONSTRAINT FK_code 
    FOREIGN KEY (unit, code)
      REFERENCES code;

ALTER TABLE code_taxref
  ADD CONSTRAINT FK_code_tree 
    FOREIGN KEY (unit, code)
      REFERENCES code_tree;

ALTER TABLE code_set
  ADD CONSTRAINT FK_code1 
    FOREIGN KEY (unit_1, code_1)
      REFERENCES code;

ALTER TABLE code_set
  ADD CONSTRAINT FK_code2 
    FOREIGN KEY (unit_2, code_2)
      REFERENCES code;

ALTER TABLE code_composition
  ADD CONSTRAINT FK_code3 
    FOREIGN KEY (unit_1, code_1)
      REFERENCES code;

ALTER TABLE code_composition
  ADD CONSTRAINT FK_code4 
    FOREIGN KEY (unit_2, code_2)
      REFERENCES code;

ALTER TABLE code_composition
  ADD CONSTRAINT FK_code5 
    FOREIGN KEY (unit_3, code_3)
      REFERENCES code;

ALTER TABLE db_table_column
  ADD CONSTRAINT FK_component 
    FOREIGN KEY (component)
      REFERENCES component;

ALTER TABLE file_column
  ADD CONSTRAINT FK_component1 
    FOREIGN KEY (component)
      REFERENCES component;

ALTER TABLE ui_field
  ADD CONSTRAINT FK_component2 
    FOREIGN KEY (component)
      REFERENCES component;

ALTER TABLE component_mapping_method
  ADD CONSTRAINT FK_component3 
    FOREIGN KEY (component)
      REFERENCES component;

ALTER TABLE component
  ADD CONSTRAINT FK_container 
    FOREIGN KEY (container)
      REFERENCES container
        ON DELETE CASCADE;

ALTER TABLE db_table_relation
  ADD CONSTRAINT FK_container_relation 
    FOREIGN KEY (container_1, container_2)
      REFERENCES container_relation;

ALTER TABLE file_container
  ADD CONSTRAINT FK_container1 
    FOREIGN KEY (container)
      REFERENCES container;

ALTER TABLE db_container
  ADD CONSTRAINT FK_container2 
    FOREIGN KEY (container)
      REFERENCES container;

ALTER TABLE container_relation
  ADD CONSTRAINT FK_container4 
    FOREIGN KEY (container_1)
      REFERENCES container;

ALTER TABLE container_relation
  ADD CONSTRAINT FK_container5 
    FOREIGN KEY (container_2)
      REFERENCES container;

ALTER TABLE process_container
  ADD CONSTRAINT FK_container6 
    FOREIGN KEY (container)
      REFERENCES container;

ALTER TABLE field_context
  ADD CONSTRAINT FK_context 
    FOREIGN KEY (context)
      REFERENCES context;

ALTER TABLE lot_context
  ADD CONSTRAINT FK_context1 
    FOREIGN KEY (context)
      REFERENCES context;

ALTER TABLE context_value
  ADD CONSTRAINT FK_context2 
    FOREIGN KEY (context)
      REFERENCES context;

ALTER TABLE context_code
  ADD CONSTRAINT FK_context3 
    FOREIGN KEY (context)
      REFERENCES context;

ALTER TABLE db_table
  ADD CONSTRAINT FK_db_container 
    FOREIGN KEY (container)
      REFERENCES db_container;

ALTER TABLE db_table_relation
  ADD CONSTRAINT FK_db_table 
    FOREIGN KEY (container_1)
      REFERENCES db_table;

ALTER TABLE file_column
  ADD CONSTRAINT FK_db_table_column 
    FOREIGN KEY (db_table_column)
      REFERENCES db_table_column;

ALTER TABLE ui_field
  ADD CONSTRAINT FK_db_table_column1 
    FOREIGN KEY (db_table_column)
      REFERENCES db_table_column
        ON DELETE SET NULL
        ON UPDATE SET NULL;

ALTER TABLE db_table_relation
  ADD CONSTRAINT FK_db_table1 
    FOREIGN KEY (container_2)
      REFERENCES db_table;

ALTER TABLE db_table_column
  ADD CONSTRAINT FK_field 
    FOREIGN KEY (field)
      REFERENCES field;

ALTER TABLE field_context
  ADD CONSTRAINT FK_field1 
    FOREIGN KEY (field)
      REFERENCES field;

ALTER TABLE process
  ADD CONSTRAINT FK_lot 
    FOREIGN KEY (lot)
      REFERENCES lot;

ALTER TABLE field
  ADD CONSTRAINT FK_lot1 
    FOREIGN KEY (lot)
      REFERENCES lot;

ALTER TABLE lot_context
  ADD CONSTRAINT FK_lot2 
    FOREIGN KEY (lot)
      REFERENCES lot;

ALTER TABLE lot_set
  ADD CONSTRAINT FK_lot3 
    FOREIGN KEY (lot_1)
      REFERENCES lot;

ALTER TABLE lot_set
  ADD CONSTRAINT FK_lot4 
    FOREIGN KEY (lot_2)
      REFERENCES lot;

ALTER TABLE component_mapping_method
  ADD CONSTRAINT FK_mapping_method 
    FOREIGN KEY (method)
      REFERENCES mapping_method;

ALTER TABLE context_code
  ADD CONSTRAINT FK_mode 
    FOREIGN KEY (unit, code)
      REFERENCES code;

ALTER TABLE application_process
  ADD CONSTRAINT FK_process 
    FOREIGN KEY (process)
      REFERENCES process;

ALTER TABLE process_check
  ADD CONSTRAINT FK_process1 
    FOREIGN KEY (process)
      REFERENCES process;

ALTER TABLE process_container
  ADD CONSTRAINT FK_process2 
    FOREIGN KEY (process)
      REFERENCES process;

ALTER TABLE ui_form_field
  ADD CONSTRAINT FK_ui_field 
    FOREIGN KEY (component)
      REFERENCES ui_field;

ALTER TABLE ui_request_field
  ADD CONSTRAINT FK_ui_field1 
    FOREIGN KEY (component)
      REFERENCES ui_field;

ALTER TABLE field
  ADD CONSTRAINT FK_unit 
    FOREIGN KEY (unit)
      REFERENCES unit;

ALTER TABLE code
  ADD CONSTRAINT FK_unit1 
    FOREIGN KEY (unit)
      REFERENCES unit;

ALTER TABLE code_sql
  ADD CONSTRAINT FK_unit2 
    FOREIGN KEY (unit)
      REFERENCES unit;

ALTER TABLE unit_constraint
  ADD CONSTRAINT FK_unit3 
    FOREIGN KEY (unit)
      REFERENCES unit;

--
-- Consistency checks
--
/*
-- Units of type CODE should have an entry in the CODE table
SELECT UNIT, 'This unit of type CODE is not described in the MODE table'
FROM unit
WHERE (type = 'CODE' OR type = 'ARRAY') 
AND subtype = 'MODE'
AND unit not in (SELECT UNIT FROM MODE WHERE MODE.UNIT=UNIT)
UNION
-- Units of type RANGE should have an entry in the RANGE table
SELECT UNIT, 'This unit of type RANGE is not described in the RANGE table'
FROM unit
WHERE TYPE = 'NUMERIC' AND SUBTYPE = 'RANGE'
AND unit not in (SELECT UNIT FROM RANGE WHERE RANGE.UNIT=UNIT)
UNION
-- File fields should have a FILE mapping
SELECT format||'_'||data, 'This file field has no FILE mapping defined'
FROM file_field
WHERE format||'_'||data NOT IN (
	SELECT (src_format||'_'||src_data )
	FROM field_mapping
	WHERE mapping_type = 'FILE'
	)
UNION
-- Form fields should have a FORM mapping
SELECT format||'_'||data, 'This form field has no FORM mapping defined'
FROM form_field
WHERE format||'_'||data NOT IN (
	SELECT (src_format||'_'||src_data )
	FROM field_mapping
	WHERE mapping_type = 'FORM'
	)
UNION
-- Raw data field should be mapped with harmonized fields
SELECT format||'_'||data, 'This raw_data table field is not mapped with an harmonized field'
FROM table_field
JOIN table_format using (format)
WHERE schema_code = 'RAW_DATA'
AND data <> 'SUBMISSION_ID'
AND data <> 'LINE_NUMBER'
AND format||'_'||data NOT IN (
	SELECT (src_format||'_'||src_data )
	FROM field_mapping
	WHERE mapping_type = 'HARMONIZE'
	)
UNION
-- Raw data field should be mapped with harmonized fields
SELECT format||'_'||data, 'This harmonized_data table field is not used by a mapping'
FROM table_field
JOIN table_format using (format)
WHERE schema_code = 'HARMONIZED_DATA'
AND column_name <> 'REQUEST_ID'  -- request ID added automatically
AND is_calculated <> '1'  -- field is not calculated
AND format||'_'||data NOT IN (
	SELECT (dst_format||'_'||dst_data )
	FROM field_mapping
	WHERE mapping_type = 'HARMONIZE'
	)
UNION
-- the SUBMISSION_ID field is mandatory for raw data tables
SELECT format, 'This raw table format is missing the SUBMISSION_ID field'
FROM table_format 
WHERE schema_code = 'RAW_DATA'
AND NOT EXISTS (SELECT * FROM table_field WHERE table_format.format = table_field.format AND table_field.data='SUBMISSION_ID')
UNION
-- the INPUT_TYPE is not in the list
SELECT format||'_'||data, 'The INPUT_TYPE type is not in the list'
FROM form_field 
WHERE input_type NOT IN ('TEXT', 'SELECT', 'DATE', 'GEOM', 'NUMERIC', 'CHECKBOX', 'MULTIPLE', 'TREE', 'TAXREF', 'IMAGE')
UNION
-- the UNIT type is not in the list
SELECT unit||'_'||type, 'The UNIT type is not in the list'
FROM unit 
WHERE type NOT IN ('BOOLEAN', 'CODE', 'ARRAY', 'DATE', 'INTEGER', 'NUMERIC', 'STRING', 'GEOM', 'IMAGE')
UNION
-- the subtype is not consistent with the type
SELECT unit||'_'||type, 'The UNIT subtype is not consistent with the type'
FROM unit 
WHERE (type = 'CODE' AND subtype NOT IN ('MODE', 'TREE', 'DYNAMIC'))
OR    (type = 'ARRAY' AND subtype NOT IN ('MODE', 'TREE', 'DYNAMIC'))
OR    (type = 'NUMERIC' AND subtype NOT IN ('RANGE', 'COORDINATE'))
UNION
-- the unit type is not consistent with the form field input type
SELECT form_field.format || '_' || form_field.data, 'The form field input type (' || input_type || ') is not consistent with the unit type (' || type || ')'
FROM form_field 
LEFT JOIN data using (data)
LEFT JOIN unit using (unit)
WHERE (input_type = 'NUMERIC' AND type NOT IN ('NUMERIC', 'INTEGER'))
OR (input_type = 'DATE' AND type <> 'DATE')
OR (input_type = 'SELECT' AND NOT (type = 'ARRAY' or TYPE = 'CODE') AND (subtype = 'CODE' OR subtype = 'DYNAMIC'))
OR (input_type = 'TEXT' AND type <> 'STRING')
OR (input_type = 'CHECKBOX' AND type <> 'BOOLEAN')
OR (input_type = 'GEOM' AND type <> 'GEOM')
OR (input_type = 'IMAGE' AND type <> 'IMAGE')
OR (input_type = 'TREE' AND NOT ((type = 'ARRAY' or TYPE = 'CODE') AND subtype = 'TREE'))
UNION
-- TREE_MODEs should be defined
SELECT unit, 'The unit should have at least one MODE_TREE defined'
FROM unit 
WHERE (type = 'CODE' OR type = 'ARRAY') 
AND subtype = 'TREE' 
AND (SELECT count(*) FROM mode_tree WHERE mode_tree.unit = unit.unit) = 0
UNION
-- DYNAMODEs should be defined
SELECT unit, 'The unit should have at least one DYNAMODE defined'
FROM unit 
WHERE (type = 'CODE' OR type = 'ARRAY') 
AND subtype = 'DYNAMIC' 
AND (SELECT count(*) FROM dynamode WHERE dynamode.unit = unit.unit) = 0
*/