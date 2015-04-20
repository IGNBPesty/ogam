***********************************************************
    Génération
    Plugiciel : Moteur pour la génération de DDL ANSI (3.2.0 - 05-12-2009), Grandite

    Nom de la base de données : "Base de données"
    Fichier source : "D:\DONNEES\Workspace\OGAM\documentation\2 - Conception\Schémas relationnels\MD OGAM.sms"

    Généré le : 20 avr. 2015 17:22:23
    Généré par Open ModelSphere Version 3.2"
***********************************************************


***********************************************************
    CREATE STATEMENTS
***********************************************************


CREATE TABLE "attribute" 
(
    "attribute" CHARACTER VARYING(32) NOT NULL,
    "class" CHARACTER VARYING(32) NOT NULL,
    si_unit CHARACTER VARYING(32) NULL,
    label CHARACTER VARYING(64) NULL,
    definition CHARACTER VARYING(256) NULL,
    comment CHARACTER VARYING(256) NULL
);


CREATE TABLE "check" 
(
    "check" CHARACTER VARYING NOT NULL,
    step CHARACTER VARYING(32) NOT NULL,
    name CHARACTER VARYING(64) NOT NULL,
    label CHARACTER VARYING(64) NOT NULL,
    definition CHARACTER VARYING(256) NOT NULL,
    importance CHARACTER VARYING(32) NOT NULL,
    statement CHARACTER VARYING(4000) NULL
);


CREATE TABLE "class" 
(
    "class" CHARACTER VARYING(32) NOT NULL,
    label CHARACTER VARYING(64) NOT NULL,
    definition CHARACTER VARYING(256) NOT NULL,
    comment CHARACTER VARYING(256) NOT NULL
);


CREATE TABLE application 
(
    application CHARACTER VARYING(32) NOT NULL,
    name CHARACTER VARYING(64) NOT NULL,
    label CHARACTER VARYING(64) NOT NULL,
    definition CHARACTER VARYING(256) NOT NULL
);


CREATE TABLE application_process 
(
    application CHARACTER VARYING(32) NOT NULL,
    process CHARACTER VARYING(32) NOT NULL,
    is_default_ui_process BOOLEAN NOT NULL
);


CREATE TABLE class_tree 
(
    class_1 CHARACTER VARYING(32) NOT NULL,
    class_2 CHARACTER VARYING(32) NOT NULL
);


CREATE TABLE code 
(
    code CHARACTER VARYING(32) NOT NULL,
    unit CHARACTER VARYING(32) NOT NULL,
    position SMALLINT NOT NULL,
    label CHARACTER VARYING(64) NOT NULL,
    definition CHARACTER VARYING(256) NOT NULL
);


CREATE TABLE code_composition 
(
    unit_1 CHARACTER VARYING(32) NOT NULL,
    code_1 CHARACTER VARYING(32) NOT NULL,
    unit_2 CHARACTER VARYING(32) NOT NULL,
    code_2 CHARACTER VARYING(32) NOT NULL,
    unit_3 CHARACTER VARYING(32) NOT NULL,
    code_3 CHARACTER VARYING(32) NOT NULL
);


CREATE TABLE code_set 
(
    unit_1 CHARACTER VARYING(32) NOT NULL,
    code_1 CHARACTER VARYING(32) NOT NULL,
    unit_2 CHARACTER VARYING(32) NOT NULL,
    code_2 CHARACTER VARYING(32) NOT NULL,
    comment CHARACTER VARYING(256) NULL
);


CREATE TABLE code_sql 
(
    unit CHARACTER VARYING(32) NOT NULL,
    sql CHARACTER VARYING(500) NOT NULL
);


CREATE TABLE code_taxref 
(
    unit CHARACTER VARYING(32) NOT NULL,
    code CHARACTER VARYING(32) NOT NULL,
    name CHARACTER VARYING(512) NOT NULL,
    complete_name CHARACTER VARYING(512) NOT NULL,
    vernacular_name CHARACTER VARYING(1000) NULL,
    is_reference BOOLEAN NULL
);


CREATE TABLE code_tree 
(
    unit CHARACTER VARYING(32) NOT NULL,
    code CHARACTER VARYING(32) NOT NULL,
    parent_code CHARACTER VARYING(32) NOT NULL,
    is_leaf BOOLEAN NULL
);


CREATE TABLE component 
(
    component CHARACTER VARYING(32) NOT NULL,
    type CHARACTER VARYING(32) NOT NULL,
    label CHARACTER VARYING(64) NULL,
    definition CHARACTER VARYING(256) NULL,
    position SMALLINT NOT NULL,
    comment CHARACTER VARYING(256) NULL,
    container CHARACTER VARYING(32) NOT NULL
);


CREATE TABLE component_mapping_method 
(
    component CHARACTER VARYING(32) NOT NULL,
    method CHARACTER VARYING(32) NOT NULL,
    position SMALLINT NOT NULL
);


CREATE TABLE container 
(
    container CHARACTER VARYING(32) NOT NULL,
    type CHARACTER VARYING(32) NOT NULL,
    subtype CHARACTER VARYING(32) NOT NULL,
    label CHARACTER VARYING(64) NOT NULL,
    definition CHARACTER VARYING(256) NOT NULL,
    comment CHARACTER VARYING(256) NULL
);


CREATE TABLE container_relation 
(
    container_1 CHARACTER VARYING(32) NOT NULL,
    container_2 CHARACTER VARYING(32) NOT NULL,
    type CHARACTER VARYING(32) NOT NULL,
    position SMALLINT NOT NULL,
    is_leaf BOOLEAN NOT NULL,
    multiplicity_1 CHAR(4) NULL,
    multiplicity_2 CHAR(4) NULL,
    comment CHARACTER VARYING(256) NULL
);


CREATE TABLE context 
(
    context CHARACTER VARYING(32) NOT NULL,
    type CHARACTER VARYING NOT NULL,
    subtype CHARACTER VARYING(32) NOT NULL
);


CREATE TABLE context_code 
(
    context CHARACTER VARYING(32) NOT NULL,
    unit CHARACTER VARYING(32) NOT NULL,
    code CHARACTER VARYING(32) NOT NULL
);


CREATE TABLE context_value 
(
    context CHARACTER VARYING(32) NOT NULL,
    value CHARACTER VARYING(512) NOT NULL
);


CREATE TABLE db_container 
(
    container CHARACTER VARYING(32) NOT NULL,
    name CHARACTER VARYING(64) NOT NULL
);


CREATE TABLE db_table 
(
    container CHARACTER VARYING(32) NOT NULL,
    primary_key CHARACTER VARYING(128) NOT NULL,
    is_eav BOOLEAN NOT NULL
);


CREATE TABLE db_table_column 
(
    component CHARACTER VARYING(32) NOT NULL,
    name CHARACTER VARYING(64) NOT NULL,
    is_calculated BOOLEAN NULL,
    is_editable BOOLEAN NULL,
    is_insertable BOOLEAN NULL,
    is_readonly BOOLEAN NULL,
    field CHARACTER VARYING(32) NOT NULL
);


CREATE TABLE db_table_relation 
(
    container_1 CHARACTER VARYING(32) NOT NULL,
    container_2 CHARACTER VARYING(32) NOT NULL,
    join_key CHARACTER VARYING(128) NOT NULL
);


CREATE TABLE field 
(
    field CHARACTER VARYING(32) NOT NULL,
    unit CHARACTER VARYING(32) NOT NULL,
    lot CHARACTER VARYING(32) NOT NULL,
    "attribute" CHARACTER VARYING(32) NOT NULL,
    is_measure BOOLEAN NOT NULL,
    is_contexted BOOLEAN NOT NULL
);


CREATE TABLE field_context 
(
    context CHARACTER VARYING(32) NOT NULL,
    field CHARACTER VARYING(32) NOT NULL
);


CREATE TABLE file_column 
(
    component CHARACTER VARYING(32) NOT NULL,
    mask CHARACTER VARYING(128) NULL,
    is_mandatory BOOLEAN NOT NULL,
    db_table_column CHARACTER VARYING(32) NOT NULL
);


CREATE TABLE file_container 
(
    container CHARACTER VARYING(32) NOT NULL,
    name CHARACTER VARYING(256) NOT NULL,
    extension CHARACTER VARYING(8) NOT NULL,
    file_type CHARACTER VARYING(32) NOT NULL
);


CREATE TABLE lot 
(
    lot CHARACTER VARYING(32) NOT NULL,
    doc CHARACTER VARYING(32) NULL,
    is_contexted BOOLEAN NOT NULL
);


CREATE TABLE lot_context 
(
    context CHARACTER VARYING(32) NOT NULL,
    lot CHARACTER VARYING(32) NOT NULL
);


CREATE TABLE lot_set 
(
    lot_1 CHARACTER VARYING(32) NOT NULL,
    lot_2 CHARACTER VARYING(32) NOT NULL
);


CREATE TABLE mapping_method 
(
    method CHARACTER VARYING(32) NOT NULL,
    type CHARACTER VARYING(32) NOT NULL,
    name CHARACTER VARYING(128) NOT NULL,
    label CHARACTER VARYING(64) NOT NULL,
    definition CHARACTER VARYING(256) NOT NULL
);


CREATE TABLE process 
(
    process CHARACTER VARYING(32) NOT NULL,
    label CHARACTER VARYING(64) NOT NULL,
    definition CHARACTER VARYING(256) NOT NULL,
    lot CHARACTER VARYING(32) NOT NULL
);


CREATE TABLE process_check 
(
    process CHARACTER VARYING(32) NOT NULL,
    "check" CHARACTER VARYING NOT NULL
);


CREATE TABLE process_container 
(
    process CHARACTER VARYING(32) NOT NULL,
    container CHARACTER VARYING(32) NOT NULL,
    type CHARACTER VARYING(32) NOT NULL
);


CREATE TABLE translation 
(
    db_table CHARACTER VARYING(32) NOT NULL,
    row_pk CHARACTER VARYING(64) NOT NULL,
    lang CHARACTER VARYING(2) NOT NULL,
    label CHARACTER VARYING(64) NOT NULL,
    definition CHARACTER VARYING(256) NOT NULL
);


CREATE TABLE ui_container_tree 
(
    container_1 CHARACTER VARYING(32) NOT NULL,
    container_2 CHARACTER VARYING(32) NOT NULL,
    is_opened BOOLEAN NOT NULL,
    is_default BOOLEAN NOT NULL,
    width SMALLINT NULL,
    height SMALLINT NULL,
    x SMALLINT NULL,
    y SMALLINT NULL
);


CREATE TABLE ui_field 
(
    component CHARACTER VARYING(32) NOT NULL,
    subtype CHARACTER VARYING(32) NOT NULL,
    field_type CHARACTER VARYING(32) NOT NULL,
    default_value CHARACTER VARYING(256) NULL,
    default_text CHARACTER VARYING(256) NULL,
    decimals SMALLINT NULL,
    mask CHARACTER VARYING(128) NULL,
    db_table_column CHARACTER VARYING(32) NULL
);


CREATE TABLE ui_form_field 
(
    component CHARACTER VARYING(32) NOT NULL,
    subposition SMALLINT NULL,
    width SMALLINT NULL,
    height SMALLINT NULL,
    x SMALLINT NULL,
    y SMALLINT NULL,
    is_mandatory BOOLEAN NOT NULL,
    is_disabled BOOLEAN NOT NULL
);


CREATE TABLE ui_object 
(
    container CHARACTER VARYING(32) NOT NULL,
    name CHARACTER VARYING(64) NOT NULL,
    model CHARACTER VARYING(64) NOT NULL
);


CREATE TABLE ui_request_field 
(
    component CHARACTER VARYING(32) NOT NULL,
    is_criteria BOOLEAN NOT NULL,
    is_column BOOLEAN NOT NULL,
    is_default_criteria BOOLEAN NOT NULL,
    is_default_column BOOLEAN NOT NULL
);


CREATE TABLE unit 
(
    unit CHARACTER VARYING(32) NOT NULL,
    type CHARACTER VARYING(32) NOT NULL,
    subtype CHARACTER VARYING(32) NULL,
    label CHARACTER VARYING(64) NOT NULL,
    definition CHARACTER VARYING(256) NOT NULL,
    is_constrained BOOLEAN NOT NULL
);


CREATE TABLE unit_constraint 
(
    unit CHARACTER VARYING(32) NOT NULL,
    type CHARACTER VARYING(32) NULL,
    value CHARACTER VARYING(32) NULL
);


ALTER TABLE application 
  ADD CONSTRAINT application_pk PRIMARY KEY (
    application)  ;
ALTER TABLE application_process 
  ADD CONSTRAINT application_process_pk PRIMARY KEY (
    application, process)  ;
ALTER TABLE "attribute" 
  ADD CONSTRAINT attribute_pk PRIMARY KEY (
    "attribute")  ;
ALTER TABLE "check" 
  ADD CONSTRAINT check_pk PRIMARY KEY (
    "check")  ;
ALTER TABLE "class" 
  ADD CONSTRAINT class_pk PRIMARY KEY (
    "class")  ;
ALTER TABLE class_tree 
  ADD CONSTRAINT class_tree_pk PRIMARY KEY (
    class_1, class_2)  ;
ALTER TABLE code 
  ADD CONSTRAINT code_pk PRIMARY KEY (
    unit, code)  ;
ALTER TABLE code_sql 
  ADD CONSTRAINT code_sql_pk PRIMARY KEY (
    unit)  ;
ALTER TABLE code_taxref 
  ADD CONSTRAINT code_taxref_pk PRIMARY KEY (
    unit, code)  ;
ALTER TABLE code_tree 
  ADD CONSTRAINT code_tree_pk PRIMARY KEY (
    unit, code)  ;
ALTER TABLE component_mapping_method 
  ADD CONSTRAINT component_mapping_method_pk PRIMARY KEY (
    component, method)  ;
ALTER TABLE component 
  ADD CONSTRAINT component_pk PRIMARY KEY (
    component)  ;
ALTER TABLE container 
  ADD CONSTRAINT container_pk PRIMARY KEY (
    container)  ;
ALTER TABLE container_relation 
  ADD CONSTRAINT container_relation_pk PRIMARY KEY (
    container_1, container_2)  ;
ALTER TABLE context_code 
  ADD CONSTRAINT context_code_pk PRIMARY KEY (
    context)  ;
ALTER TABLE context 
  ADD CONSTRAINT context_pk PRIMARY KEY (
    context)  ;
ALTER TABLE context_value 
  ADD CONSTRAINT context_value_pk PRIMARY KEY (
    context)  ;
ALTER TABLE db_container 
  ADD CONSTRAINT db_container_pk PRIMARY KEY (
    container)  ;
ALTER TABLE db_table_column 
  ADD CONSTRAINT db_table_column_pk PRIMARY KEY (
    component)  ;
ALTER TABLE db_table 
  ADD CONSTRAINT db_table_pk PRIMARY KEY (
    container)  ;
ALTER TABLE db_table_relation 
  ADD CONSTRAINT db_table_relation_pk PRIMARY KEY (
    container_1, container_2)  ;
ALTER TABLE field_context 
  ADD CONSTRAINT field_context_pk PRIMARY KEY (
    context, field)  ;
ALTER TABLE field 
  ADD CONSTRAINT field_pk PRIMARY KEY (
    field)  ;
ALTER TABLE file_column 
  ADD CONSTRAINT file_component_pk PRIMARY KEY (
    component)  ;
ALTER TABLE file_container 
  ADD CONSTRAINT file_container_pk PRIMARY KEY (
    container)  ;
ALTER TABLE lot_context 
  ADD CONSTRAINT lot_context_pk PRIMARY KEY (
    context, lot)  ;
ALTER TABLE lot 
  ADD CONSTRAINT lot_pk PRIMARY KEY (
    lot)  ;
ALTER TABLE lot_set 
  ADD CONSTRAINT lot_set_pk PRIMARY KEY (
    lot_1, lot_2)  ;
ALTER TABLE mapping_method 
  ADD CONSTRAINT mapping_method_pk PRIMARY KEY (
    method)  ;
ALTER TABLE process_check 
  ADD CONSTRAINT process_check_pk PRIMARY KEY (
    process, "check")  ;
ALTER TABLE process_container 
  ADD CONSTRAINT process_container_pk PRIMARY KEY (
    process, container)  ;
ALTER TABLE process 
  ADD CONSTRAINT process_pk PRIMARY KEY (
    process)  ;
ALTER TABLE translation 
  ADD CONSTRAINT translation_pk PRIMARY KEY (
    db_table, lang, row_pk)  ;
ALTER TABLE ui_container_tree 
  ADD CONSTRAINT ui_container_tree_pk PRIMARY KEY (
    container_1, container_2)  ;
ALTER TABLE ui_field 
  ADD CONSTRAINT ui_field_pk PRIMARY KEY (
    component)  ;
ALTER TABLE ui_form_field 
  ADD CONSTRAINT ui_form_field_pk PRIMARY KEY (
    component)  ;
ALTER TABLE ui_object 
  ADD CONSTRAINT ui_object_pk PRIMARY KEY (
    container)  ;
ALTER TABLE ui_request_field 
  ADD CONSTRAINT ui_request_field_pk PRIMARY KEY (
    component)  ;
ALTER TABLE unit_constraint 
  ADD CONSTRAINT unit_constraint_pk PRIMARY KEY (
    unit, type)  ;
ALTER TABLE unit 
  ADD CONSTRAINT unit_pk PRIMARY KEY (
    unit)  ;
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



    END

