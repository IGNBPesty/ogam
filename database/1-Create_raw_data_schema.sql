SET client_encoding TO 'UTF8';
SET client_min_messages TO WARNING;

CREATE SCHEMA raw_data;
SET SEARCH_PATH = raw_data, public;



--
-- WARNING: The DATASET_ID, PROVIDER_ID and PLOT_CODE columns are used by the system and should keep their names.
--



/*==============================================================*/
/* Sequence : SUBMISSION_ID                                     */
/*==============================================================*/
CREATE SEQUENCE submission_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
  
  
/*==============================================================*/
/* Table : SUBMISSION                                           */
/*==============================================================*/
create table SUBMISSION (
SUBMISSION_ID        INT4                 not null default nextval('submission_id_seq'),
STEP		 		 VARCHAR(36)          null,
STATUS    			 VARCHAR(36)          null,
PROVIDER_ID          VARCHAR(36)          not null,
DATASET_ID           VARCHAR(36)          not null,
USER_LOGIN           VARCHAR(50)          not null,
_CREATIONDT          DATE                 null DEFAULT current_timestamp,
_VALIDATIONDT        DATE                 null DEFAULT current_timestamp,
constraint PK_SUBMISSION primary key (SUBMISSION_ID)
);


COMMENT ON COLUMN SUBMISSION.SUBMISSION_ID IS 'The identifier of the submission';
COMMENT ON COLUMN SUBMISSION.STEP IS 'The submission step (INIT, INSERT, CHECK, VALIDATE, CANCEL)';
COMMENT ON COLUMN SUBMISSION.STATUS IS 'The submission status (OK, WARNING, ERROR, CRASH)';
COMMENT ON COLUMN SUBMISSION.PROVIDER_ID IS 'The data provider identifier (country code or organisation name)';
COMMENT ON COLUMN SUBMISSION.DATASET_ID IS 'The dataset identifier';
COMMENT ON COLUMN SUBMISSION.USER_LOGIN IS 'The login of the user doing the submission';
COMMENT ON COLUMN SUBMISSION._CREATIONDT IS 'The date of submission';
COMMENT ON COLUMN SUBMISSION._VALIDATIONDT IS 'The date of validation';



/*==============================================================*/
/* Table : SUBMISSION_FILE                                      */
/*==============================================================*/
create table SUBMISSION_FILE (
SUBMISSION_ID        INT4                 not null,
FILE_TYPE            VARCHAR(36)          not null,
FILE_NAME            VARCHAR(4000)         not null,
NB_LINE              INT4                 null,
constraint PK_SUBMISSION_FILE primary key (SUBMISSION_ID, FILE_TYPE)
);

COMMENT ON COLUMN SUBMISSION_FILE.SUBMISSION_ID IS 'The identifier of the submission';
COMMENT ON COLUMN SUBMISSION_FILE.FILE_TYPE IS 'The type of file (reference a DATASET_FILES.FORMAT)';
COMMENT ON COLUMN SUBMISSION_FILE.FILE_NAME IS 'The name of the file';
COMMENT ON COLUMN SUBMISSION_FILE.NB_LINE IS 'The number of lines of data in the file (exluding comment and empty lines)';



/*==============================================================*/
/* Table : LOCATION                                             */
/*==============================================================*/
create table LOCATION (
SUBMISSION_ID        INT4                 null,
PROVIDER_ID          VARCHAR(36)          not null,
PLOT_CODE            VARCHAR(36)          not null,
LAT                  FLOAT8               null,
LONG                 FLOAT8               null,
COMMUNES			 TEXT[] 			  null,
DEPARTEMENT			 VARCHAR(36)      	  null,
COMMENT              VARCHAR(255)         null,
LINE_NUMBER			 INTEGER			  null,
constraint PK_LOCATION primary key (PROVIDER_ID, PLOT_CODE),
unique (PROVIDER_ID, PLOT_CODE)
);

-- Ajout de la colonne point PostGIS
SELECT AddGeometryColumn('raw_data','location','the_geom',4326,'POINT',2);


COMMENT ON COLUMN LOCATION.SUBMISSION_ID IS 'The identifier of the submission';
COMMENT ON COLUMN LOCATION.PROVIDER_ID IS 'The identifier of the data provider';
COMMENT ON COLUMN LOCATION.PLOT_CODE IS 'The identifier of the plot';
COMMENT ON COLUMN LOCATION.LAT IS 'The latitude (in decimal degrees)';
COMMENT ON COLUMN LOCATION.LONG IS 'The longitude (in decimal degrees)';
COMMENT ON COLUMN LOCATION.COMMUNES IS 'Communes concerned by the location';
COMMENT ON COLUMN LOCATION.DEPARTEMENT IS 'Département';
COMMENT ON COLUMN LOCATION.COMMENT IS 'A comment about the plot location';
COMMENT ON COLUMN LOCATION.LINE_NUMBER IS 'The position of the line of data in the original CSV file';
COMMENT ON COLUMN LOCATION.THE_GEOM IS 'The geometry of the location';


-- Spatial Index on the_geom 
CREATE INDEX IX_LOCATION_SPATIAL_INDEX ON raw_data.location USING GIST
            ( the_geom  );

ALTER TABLE raw_data.location 
  ADD CONSTRAINT fk_location_submission_id FOREIGN KEY (submission_id) 
      REFERENCES raw_data.submission (submission_id)
      ON UPDATE RESTRICT ON DELETE RESTRICT;
CREATE INDEX fki_location_submission_id ON raw_data.location(submission_id);


/*==========================================================================*/
/* Add a trigger to fill the the_geom column of the location table         */
/*==========================================================================*/
CREATE OR REPLACE FUNCTION raw_data.a_geomfromcoordinate() RETURNS "trigger" AS
$BODY$
BEGIN
    BEGIN
    IF NEW.the_geom IS NULL THEN
		NEW.the_geom = public.ST_GeometryFromText('POINT(' || NEW.LONG || ' ' || NEW.LAT || ')', 4326);	
    END IF;   
    EXCEPTION
    WHEN internal_error THEN
        IF SQLERRM = 'parse error - invalid geometry' THEN
            RAISE EXCEPTION USING ERRCODE = '09001', MESSAGE = SQLERRM;
        ELSIF SQLERRM = 'geometry requires more points' THEN
            RAISE EXCEPTION USING ERRCODE = '09002', MESSAGE = SQLERRM;
        END IF;
    END;
    RETURN NEW;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

DROP TRIGGER IF EXISTS a_geom_trigger ON raw_data.LOCATION;
CREATE TRIGGER a_geom_trigger
  BEFORE INSERT OR UPDATE
  ON raw_data.LOCATION
  FOR EACH ROW
  EXECUTE PROCEDURE raw_data.a_geomfromcoordinate();



  
/*========================================================================*/
/*	Add a trigger to fill the departements column of the location table   */
/*========================================================================*/
CREATE OR REPLACE FUNCTION raw_data.b_departementsfromgeom() RETURNS "trigger" AS
$BODY$
BEGIN

    NEW.departement = max(dp) FROM "mapping".departements z WHERE st_intersects(z.the_geom, ST_Transform(NEW.the_geom, 3857));    
    RETURN NEW;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

DROP TRIGGER IF EXISTS b_departements_geom_trigger ON raw_data.LOCATION;
CREATE TRIGGER b_departements_geom_trigger
  BEFORE INSERT ON raw_data.LOCATION
  FOR EACH ROW
  EXECUTE PROCEDURE raw_data.b_departementsfromgeom();
  
  
  
/*========================================================================*/
/*	Add a trigger to fill the communes column of the location table    */
/*========================================================================*/
CREATE OR REPLACE FUNCTION raw_data.c_communesfromgeom() RETURNS "trigger" AS
$BODY$
BEGIN

    NEW.communes = (SELECT array_agg(code) FROM (SELECT code FROM "mapping".communes z WHERE st_intersects(z.the_geom, ST_Transform(NEW.the_geom, 3857)) LIMIT 20) as foo);    
    RETURN NEW;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

DROP TRIGGER IF EXISTS c_communes_geom_trigger ON raw_data.LOCATION;
CREATE TRIGGER c_communes_geom_trigger
  BEFORE INSERT ON raw_data.LOCATION
  FOR EACH ROW
  EXECUTE PROCEDURE raw_data.c_communesfromgeom();
  
  
  
/*========================================================================*/
/*	Add a trigger to fill the communes column of the location table    */
/*========================================================================*/
CREATE OR REPLACE FUNCTION raw_data.d_latlonfromgeom() RETURNS "trigger" AS
$BODY$
BEGIN

    NEW.long = ST_X(ST_Transform(NEW.the_geom, 4326));    
    NEW.lat = ST_Y(ST_Transform(NEW.the_geom, 4326));
    RETURN NEW;
END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

DROP TRIGGER IF EXISTS d_latlon_geom_trigger ON raw_data.LOCATION;
CREATE TRIGGER d_latlon_geom_trigger
  BEFORE INSERT ON raw_data.LOCATION
  FOR EACH ROW
  EXECUTE PROCEDURE raw_data.d_latlonfromgeom();
            

/*==============================================================*/
/* Table : PLOT_DATA                                            */
/*==============================================================*/
create table PLOT_DATA (
SUBMISSION_ID        INT4                 null,
PROVIDER_ID          VARCHAR(36)          not null,
PLOT_CODE            VARCHAR(36)          not null,
CYCLE	             VARCHAR(36)          not null,
INV_DATE             DATE                 null,
IS_FOREST_PLOT		 CHAR(1)	          null,
CORINE_BIOTOPE 		 character varying(36)[]     null,
FICHE_PLACETTE       VARCHAR(1000)        null,
COMMENT              VARCHAR(1000)        null,
LINE_NUMBER			 INTEGER			  null,
INV_TIME             TIME                 null,
constraint PK_PLOT_DATA primary key (PROVIDER_ID, PLOT_CODE, CYCLE),
constraint FK_PLOT_DATA_ASSOCIATE_LOCATION foreign key (PROVIDER_ID, PLOT_CODE) references LOCATION (PROVIDER_ID, PLOT_CODE) on delete restrict on update restrict,
unique (PROVIDER_ID, PLOT_CODE, CYCLE)
);

COMMENT ON COLUMN PLOT_DATA.SUBMISSION_ID IS 'The identifier of the submission';
COMMENT ON COLUMN PLOT_DATA.PROVIDER_ID IS 'The identifier of the data provider';
COMMENT ON COLUMN PLOT_DATA.PLOT_CODE IS 'The identifier of the plot';
COMMENT ON COLUMN PLOT_DATA.CYCLE IS 'The cycle of inventory';
COMMENT ON COLUMN PLOT_DATA.INV_DATE IS 'The date of inventory';
COMMENT ON COLUMN PLOT_DATA.INV_TIME IS 'The Time of inventory';
COMMENT ON COLUMN PLOT_DATA.IS_FOREST_PLOT IS 'Is the plot a forest plot ?';
COMMENT ON COLUMN PLOT_DATA.CORINE_BIOTOPE IS 'The biotope of the plot';
COMMENT ON COLUMN PLOT_DATA.FICHE_PLACETTE IS 'URL to a PDF document';
COMMENT ON COLUMN PLOT_DATA.COMMENT IS 'A comment about the plot';
COMMENT ON COLUMN PLOT_DATA.LINE_NUMBER IS 'The position of the line of data in the original CSV file';


/*==============================================================*/
/* Table : SPECIES_DATA                                         */
/*==============================================================*/
create table SPECIES_DATA (
SUBMISSION_ID        INT4                 null,
PROVIDER_ID          VARCHAR(36)          not null,
PLOT_CODE            VARCHAR(36)          not null,
CYCLE	             VARCHAR(36)          not null,
ID_TAXON             VARCHAR(36)          not null,
BASAL_AREA			 FLOAT8	              null,
COMMENT              VARCHAR(255)         null,
LINE_NUMBER			 INTEGER			  null,
constraint PK_SPECIES_DATA primary key (PROVIDER_ID, PLOT_CODE, CYCLE, ID_TAXON),
constraint FK_SPECIES_ASSOCIATE_PLOT_DAT foreign key (PROVIDER_ID, PLOT_CODE, CYCLE) references PLOT_DATA (PROVIDER_ID, PLOT_CODE, CYCLE) on delete restrict on update restrict,
unique (PROVIDER_ID, PLOT_CODE, CYCLE, ID_TAXON)   
);

COMMENT ON COLUMN SPECIES_DATA.SUBMISSION_ID IS 'The identifier of the submission';
COMMENT ON COLUMN SPECIES_DATA.PROVIDER_ID IS 'The identifier of the data provider';
COMMENT ON COLUMN SPECIES_DATA.PLOT_CODE IS 'The identifier of the plot';
COMMENT ON COLUMN SPECIES_DATA.CYCLE IS 'The cycle of inventory';
COMMENT ON COLUMN SPECIES_DATA.ID_TAXON IS 'Identifiant de taxon';
COMMENT ON COLUMN SPECIES_DATA.BASAL_AREA IS 'The proportion of surface covered by this specie on the plot (in m2/ha)';
COMMENT ON COLUMN SPECIES_DATA.COMMENT IS 'A comment about the species';
COMMENT ON COLUMN SPECIES_DATA.LINE_NUMBER IS 'The position of the line of data in the original CSV file';


  
/*==============================================================*/
/* Sequence : TREE_ID                                           */
/*==============================================================*/
CREATE SEQUENCE tree_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
  

/*==============================================================*/
/* Table : TREE_DATA                                            */
/*==============================================================*/
create table TREE_DATA (
SUBMISSION_ID        INT4                 null,
PROVIDER_ID          VARCHAR(36)          not null,
PLOT_CODE            VARCHAR(36)          not null,
CYCLE	             VARCHAR(36)          not null,
TREE_ID              INT4                 not null default nextval('tree_id_seq'),
SPECIES_CODE		 VARCHAR(36)          null,
DBH					 FLOAT8	              null,
HEIGHT	 			 FLOAT8	              null,
PHOTO	 			 VARCHAR(255)         null,
COMMENT              VARCHAR(255)         null,
LINE_NUMBER			 INTEGER			  null,
constraint PK_TREE_DATA primary key (PROVIDER_ID, PLOT_CODE, CYCLE, TREE_ID),
constraint FK_TREE_ASSOCIATE_PLOT_DAT foreign key (PROVIDER_ID, PLOT_CODE, CYCLE) references PLOT_DATA (PROVIDER_ID, PLOT_CODE, CYCLE) on delete restrict on update restrict,
unique (PROVIDER_ID, PLOT_CODE, CYCLE, TREE_ID)   
);

COMMENT ON COLUMN TREE_DATA.SUBMISSION_ID IS 'The identifier of the submission';
COMMENT ON COLUMN TREE_DATA.PROVIDER_ID IS 'The identifier of the data provider';
COMMENT ON COLUMN TREE_DATA.PLOT_CODE IS 'The identifier of the plot';
COMMENT ON COLUMN TREE_DATA.CYCLE IS 'The cycle of inventory';
COMMENT ON COLUMN TREE_DATA.TREE_ID IS 'The identifier of the tree';
COMMENT ON COLUMN TREE_DATA.SPECIES_CODE IS 'The code of the specie of the tree';
COMMENT ON COLUMN TREE_DATA.DBH IS 'The diameter at breast height (in m)';
COMMENT ON COLUMN TREE_DATA.HEIGHT IS 'The tree height (in m)';
COMMENT ON COLUMN TREE_DATA.PHOTO IS 'A picture of the tree';
COMMENT ON COLUMN TREE_DATA.COMMENT IS 'A comment about the species';
COMMENT ON COLUMN TREE_DATA.LINE_NUMBER IS 'The position of the line of data in the original CSV file';


-- Ajout de la colonne point PostGIS
SELECT AddGeometryColumn('raw_data','tree_data','the_geom',4326,'POINT',2);

COMMENT ON COLUMN TREE_DATA.the_geom IS 'geometry of the tree location';


/*==============================================================*/
/* Table : CHECK_ERROR                                          */
/*==============================================================*/
create table CHECK_ERROR (
CHECK_ERROR_ID       serial               not null,
CHECK_ID             INT4                 not null,
SUBMISSION_ID        INT4                 not null,
LINE_NUMBER          INT4                 not null,
SRC_FORMAT           VARCHAR(36)          null,
SRC_DATA             VARCHAR(36)          null,
PROVIDER_ID          VARCHAR(36)          null,
PLOT_CODE            VARCHAR(36)          null,
FOUND_VALUE          VARCHAR(255)         null,
EXPECTED_VALUE       VARCHAR(255)         null,
ERROR_MESSAGE        VARCHAR(4000)        null,
_CREATIONDT          DATE                 null  DEFAULT current_timestamp,
constraint PK_CHECK_ERROR primary key (CHECK_ID, SUBMISSION_ID, CHECK_ERROR_ID)
);

COMMENT ON COLUMN CHECK_ERROR.CHECK_ERROR_ID IS 'The identifier of the error (autoincrement)';
COMMENT ON COLUMN CHECK_ERROR.CHECK_ID IS 'The identifier of the check';
COMMENT ON COLUMN CHECK_ERROR.SUBMISSION_ID IS 'The identifier of the submission checked';
COMMENT ON COLUMN CHECK_ERROR.LINE_NUMBER IS 'The line number of the data in the original CSV file';
COMMENT ON COLUMN CHECK_ERROR.SRC_FORMAT IS 'The logical name the data source (CSV file or table name)';
COMMENT ON COLUMN CHECK_ERROR.SRC_DATA IS 'The logical name of the data (column)';
COMMENT ON COLUMN CHECK_ERROR.PROVIDER_ID IS 'The identifier of the data provider';
COMMENT ON COLUMN CHECK_ERROR.PLOT_CODE IS 'The identifier of the plot';
COMMENT ON COLUMN CHECK_ERROR.FOUND_VALUE IS 'The erroreous value (if available)';
COMMENT ON COLUMN CHECK_ERROR.EXPECTED_VALUE IS 'The expected value (if available)';
COMMENT ON COLUMN CHECK_ERROR.ERROR_MESSAGE IS 'The error message';
COMMENT ON COLUMN CHECK_ERROR._CREATIONDT IS 'The creation date';



/*****************************************************************/
/*/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\*/
/**             TESTS TABLES                                     */
/*/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\/!\*/
/*****************************************************************/
/**====================================================*/
/*  TABLE : TEST_1                                     */
/**====================================================*/

CREATE TABLE TEST_1(
ID int not null,
TEXT_STRING_STRING varchar(64),
TEXT_STRING_LINK varchar(255),
TEXT_NUMERIC_INTEGER int,
TEXT_NUMERIC_NUMERIC numeric(8,2),
TEXT_NUMERIC_RANGE numeric(5,2),
NUMERIC_NUMERIC_NUMERIC numeric,
NUMERIC_NUMERIC_RANGE real,
NUMERIC_NUMERIC_INTEGER int,
NUMERIC_NUMERIC_COORDINATE real,
DATE_DATE date,
TIME_TIME time,
DATETIME_DATETIME timestamp,
GEOM_GEOM_POLYGON geometry(Polygon, 4326),
SUBMISSION_ID int,
CONSTRAINT pk_TEST_1 PRIMARY KEY(ID)
);


/**====================================================*/
/*  TABLE : TEST_2                                     */
/**====================================================*/
CREATE TABLE TEST_2(
ID int not null,
PARENT_ID int not null,
SELECT_CODE_CODE_3 varchar(36),
SELECT_CODE_CODE_10 varchar(36),
SELECT_CODE_CODE_100 varchar(36),
SELECT_CODE_DYNAMIC_3 varchar(36),
SELECT_CODE_DYNAMIC_10 varchar(36),
SELECT_CODE_DYNAMIC_100 varchar(36),
SELECT_ARRAY_CODE_3 varchar(36)[],
SELECT_ARRAY_CODE_10 varchar(36)[],
SELECT_ARRAY_CODE_100 varchar(36)[],
SELECT_ARRAY_DYNAMIC_3 varchar(36)[],
SELECT_ARRAY_DYNAMIC_10 varchar(36)[],
SELECT_ARRAY_DYNAMIC_100 varchar(36)[],
GEOM_GEOM_LINESTRING geometry(LINESTRING, 4326),
CONSTRAINT pk_TEST_2 PRIMARY KEY(ID),
CONSTRAINT fk_TEST_2_PARENT_ID FOREIGN KEY (PARENT_ID)
REFERENCES TEST_1 (ID)MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
);


/**====================================================*/
/*  TABLE : TEST_3                                     */
/**====================================================*/
CREATE TABLE TEST_3(
ID int not null,
PARENT_ID int not null,
CHECKBOX_BOOLEAN char(1),
RADIO_BOOLEAN char(1),
RADIO_CODE_CODE_3 varchar(36),
RADIO_CODE_CODE_10 varchar(36),
RADIO_CODE_CODE_100 varchar(36),
RADIO_CODE_DYNAMIC_3 varchar(36),
RADIO_CODE_DYNAMIC_10 varchar(36),
RADIO_CODE_DYNAMIC_100 varchar(36),
TREE_CODE_TREE varchar(36),
TREE_ARRAY_TREE varchar(36)[],
TAXREF_CODE_TAXREF varchar(36),
TAXREF_ARRAY_TAXREF varchar(36)[],
IMAGE_IMAGE TEXT,
GEOM_GEOM_POINT Geometry(Point, 4326),
CONSTRAINT pk_TEST_3 PRIMARY KEY(ID),
CONSTRAINT fk_TEST_3_PARENT_ID FOREIGN KEY (PARENT_ID)
REFERENCES TEST_2 (ID)MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
)
;
