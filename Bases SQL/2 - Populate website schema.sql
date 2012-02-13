SET SEARCH_PATH = website;

-- TEST DATABASE Parameters
INSERT INTO application_parameters (name, value, description) values ('UploadDirectory','/var/tmp/ogam_upload','Directory where the CSV files are uploaded');
INSERT INTO application_parameters (name, value, description) values ('InterpolationResultDirectory','C:/workspace/OGAM/Mapserv/generated_content/','Directory where the ESRI ASCII files are generated');
INSERT INTO application_parameters (name, value, description) values ('RInstallDirectory','C:/Program Files/R/R-2.10.0/bin/','Directory of installation of R');
INSERT INTO application_parameters (name, value, description) values ('Test','OK','For test purposes');
INSERT INTO application_parameters (name, value, description) values ('fromMail','OGAM@ifn.fr','The application email');
INSERT INTO application_parameters (name, value, description) values ('toMail','benoit.pesty@ifn.fr','The destination email');

-- Create some roles
INSERT INTO role(role_code, role_label, role_definition) VALUES ('ADMIN','Administrator', 'Manages the web site');

-- Create some users
INSERT INTO users(user_login, user_password, user_name, provider_id, active, email) VALUES ('admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin user', '1', '1', null); 

-- Link the users to their roles
INSERT INTO role_to_user(user_login, role_code) VALUES ('admin', 'ADMIN');


INSERT INTO ROLE_TO_SCHEMA(ROLE_CODE, SCHEMA_CODE) VALUES ('ADMIN', 'RAW_DATA');
INSERT INTO ROLE_TO_SCHEMA(ROLE_CODE, SCHEMA_CODE) VALUES ('ADMIN', 'HARMONIZED_DATA');



-- List the permissions of the web site
INSERT INTO permission(permission_code, permission_label) VALUES ('MANAGE_USERS', 'Manage users');
INSERT INTO permission(permission_code, permission_label) VALUES ('DATA_INTEGRATION', 'Provide data');
--INSERT INTO permission(permission_code, permission_label) VALUES ('OVERVIEW', 'See an overview board');
INSERT INTO permission(permission_code, permission_label) VALUES ('DATA_QUERY', 'Visualise data');
INSERT INTO permission(permission_code, permission_label) VALUES ('DATA_QUERY_OTHER_PROVIDER', 'Visualise data from other provider');
INSERT INTO permission(permission_code, permission_label) VALUES ('DATA_HARMONIZATION', 'Launch the harmonization process');
--INSERT INTO permission(permission_code, permission_label) VALUES ('DATA_AGGREGATION', 'Launch the aggregation process');
--INSERT INTO permission(permission_code, permission_label) VALUES ('DATA_QUERY_AGGREGATED', 'Visualise aggregated data');
--INSERT INTO permission(permission_code, permission_label) VALUES ('DATA_INTERPOLATION', 'Launch the interpolation process');
--INSERT INTO permission(permission_code, permission_label) VALUES ('DOCUMENTATION', 'Visualise the project public documentation');
--INSERT INTO permission(permission_code, permission_label) VALUES ('PRIVATE_DOCUMENTATION', 'Visualise the project private documentation');
INSERT INTO permission(permission_code, permission_label) VALUES ('EXPORT_RAW_DATA', 'Export the raw data as a CSV file');
INSERT INTO permission(permission_code, permission_label) VALUES ('DATA_EDITION', 'Add / Update / Delete data');
INSERT INTO permission(permission_code, permission_label) VALUES ('DATA_EDITION_OTHER_PROVIDER', 'Add / Update / Delete data from another provider');
INSERT INTO permission(permission_code, permission_label) VALUES ('CHECK_CONF', 'Check the configuration');
INSERT INTO permission(permission_code, permission_label) VALUES ('CANCEL_VALIDATED_SUBMISSION', 'Cancel validated submission');
INSERT INTO permission(permission_code, permission_label) VALUES ('CANCEL_OTHER_PROVIDER_SUBMISSION', 'Cancel other provider submission');

-- Add the permissions per role
INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'MANAGE_USERS');
INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'DATA_INTEGRATION');
INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'DATA_QUERY');
INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'DATA_QUERY_OTHER_PROVIDER');
INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'DATA_HARMONIZATION');
--INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'DATA_QUERY_HARMONIZED');
--INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'DATA_AGGREGATION');
--INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'DATA_QUERY_AGGREGATED');
--INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'DATA_INTERPOLATION');
--INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'OVERVIEW');
--INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'DOCUMENTATION');
--INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'PRIVATE_DOCUMENTATION');
INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'EXPORT_RAW_DATA');
INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'DATA_EDITION');
INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'DATA_EDITION_OTHER_PROVIDER');
INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'CHECK_CONF');
INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'CANCEL_VALIDATED_SUBMISSION');
INSERT INTO permission_per_role(role_code, permission_code) VALUES ('ADMIN', 'CANCEL_OTHER_PROVIDER_SUBMISSION');



--
-- Définition des requêtes prédéfinies
--

set search_path = website;

DELETE FROM predefined_request_group_asso;
DELETE FROM predefined_request_group;
DELETE FROM predefined_request_criteria;
DELETE FROM predefined_request_result;
DELETE FROM predefined_request;

-- Création d'un thème (groupe de requêtes)
INSERT INTO predefined_request_group(group_name, label, definition, position) VALUES ('SPECIES', 'Distribution par espèce', 'Distribution par espèce', 1);

-- Création d'une requête prédéfinie
INSERT INTO predefined_request (request_name, schema_code, dataset_id, label, definition, date) VALUES ('SPECIES', 'RAW_DATA', 'SPECIES', 'Distribution par espèce', 'Distribution par espèce en forêt', now());
INSERT INTO predefined_request (request_name, schema_code, dataset_id, label, definition, date) VALUES ('DEP', 'RAW_DATA', 'SPECIES', 'Espèces par département', 'Espèces par département', now());


-- Configuration des requêtes prédéfinies
INSERT INTO predefined_request_criteria (request_name, format, data, value, fixed) VALUES ('SPECIES', 'SPECIES_FORM', 'SPECIES_CODE', '026.001.006', NULL);
INSERT INTO predefined_request_result (request_name, format, data) VALUES ('SPECIES', 'PLOT_FORM', 'PLOT_CODE');
INSERT INTO predefined_request_result (request_name, format, data) VALUES ('SPECIES', 'PLOT_FORM', 'CYCLE');
INSERT INTO predefined_request_result (request_name, format, data) VALUES ('SPECIES', 'PLOT_FORM', 'INV_DATE');
INSERT INTO predefined_request_result (request_name, format, data) VALUES ('SPECIES', 'PLOT_FORM', 'IS_FOREST_PLOT');
INSERT INTO predefined_request_result (request_name, format, data) VALUES ('SPECIES', 'SPECIES_FORM', 'SPECIES_CODE');
INSERT INTO predefined_request_result (request_name, format, data) VALUES ('SPECIES', 'SPECIES_FORM', 'BASAL_AREA');

INSERT INTO predefined_request_criteria (request_name, format, data, value, fixed) VALUES ('DEP', 'LOCATION_FORM', 'DEPARTEMENT', '45', NULL);
INSERT INTO predefined_request_result (request_name, format, data) VALUES ('DEP', 'PLOT_FORM', 'PLOT_CODE');
INSERT INTO predefined_request_result (request_name, format, data) VALUES ('DEP', 'PLOT_FORM', 'CYCLE');
INSERT INTO predefined_request_result (request_name, format, data) VALUES ('DEP', 'PLOT_FORM', 'INV_DATE');
INSERT INTO predefined_request_result (request_name, format, data) VALUES ('DEP', 'PLOT_FORM', 'IS_FOREST_PLOT');
INSERT INTO predefined_request_result (request_name, format, data) VALUES ('DEP', 'SPECIES_FORM', 'SPECIES_CODE');
INSERT INTO predefined_request_result (request_name, format, data) VALUES ('DEP', 'SPECIES_FORM', 'BASAL_AREA');


-- Rattachement de la requête prédéfinies au thème
INSERT INTO website.predefined_request_group_asso(group_name, request_name, position) VALUES ('SPECIES', 'SPECIES', 1);
INSERT INTO website.predefined_request_group_asso(group_name, request_name, position) VALUES ('SPECIES', 'DEP', 2);

