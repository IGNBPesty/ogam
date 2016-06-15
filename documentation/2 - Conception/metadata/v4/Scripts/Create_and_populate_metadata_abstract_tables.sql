--
-- PostgreSQL database dump
--

-- Dumped from database version 9.4.2
-- Dumped by pg_dump version 9.4.2
-- Started on 2015-09-16 14:32:38

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = metadata, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 239 (class 1259 OID 20947)
-- Name: abstract_branch_node; Type: TABLE; Schema: metadata; Owner: postgres; Tablespace: 
--

CREATE TABLE abstract_branch_node (
    code character varying(32) NOT NULL,
    label character varying(64) NOT NULL
);


ALTER TABLE abstract_branch_node OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 20950)
-- Name: abstract_node; Type: TABLE; Schema: metadata; Owner: postgres; Tablespace: 
--

CREATE TABLE abstract_node (
    code character varying(32) NOT NULL,
    type character varying(32)
);


ALTER TABLE abstract_node OWNER TO postgres;

--
-- TOC entry 241 (class 1259 OID 20953)
-- Name: abstract_node_tree; Type: TABLE; Schema: metadata; Owner: postgres; Tablespace: 
--

CREATE TABLE abstract_node_tree (
    code character varying(32) NOT NULL,
    parent_code character varying(32) NOT NULL,
    "order" smallint
);


ALTER TABLE abstract_node_tree OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 20956)
-- Name: abstract_object_group_node; Type: TABLE; Schema: metadata; Owner: postgres; Tablespace: 
--

CREATE TABLE abstract_object_group_node (
    code character varying(32) NOT NULL,
    sql_objects_query text,
    child_object_node character varying(32) NOT NULL
);


ALTER TABLE abstract_object_group_node OWNER TO postgres;

--
-- TOC entry 243 (class 1259 OID 20962)
-- Name: abstract_object_node; Type: TABLE; Schema: metadata; Owner: postgres; Tablespace: 
--

CREATE TABLE abstract_object_node (
    code character varying(32) NOT NULL,
    object_type character varying(32)
);


ALTER TABLE abstract_object_node OWNER TO postgres;

--
-- TOC entry 2270 (class 0 OID 20947)
-- Dependencies: 239
-- Data for Name: abstract_branch_node; Type: TABLE DATA; Schema: metadata; Owner: postgres
--

INSERT INTO abstract_branch_node (code, label) VALUES ('*', 'Root');
INSERT INTO abstract_branch_node (code, label) VALUES ('SITE_DOMAINE', 'Domaine(s)');
INSERT INTO abstract_branch_node (code, label) VALUES ('SITE_INTERVENANT', 'Intervenant(s)');
INSERT INTO abstract_branch_node (code, label) VALUES ('SITE_ORGANISME', 'Organisme(s)');
INSERT INTO abstract_branch_node (code, label) VALUES ('SITES', 'Site(s)');
INSERT INTO abstract_branch_node (code, label) VALUES ('SITE_DOM_DOMAINE', 'Domaine(s)');
INSERT INTO abstract_branch_node (code, label) VALUES ('SITE_DOM_ORGANISME', 'Organisme(s)');
INSERT INTO abstract_branch_node (code, label) VALUES ('SITE_DOM_PERSONNE', 'Personne(s)');
INSERT INTO abstract_branch_node (code, label) VALUES ('SITE_INTER_DOMAINE', 'Domaine(s)');
INSERT INTO abstract_branch_node (code, label) VALUES ('SITE_INTER_ORGANISME', 'Organisme(s)');
INSERT INTO abstract_branch_node (code, label) VALUES ('SITE_INTER_PERSONNE', 'Personne(s)');
INSERT INTO abstract_branch_node (code, label) VALUES ('SITE_ORG_DOMAINE', 'Domaine(s)');
INSERT INTO abstract_branch_node (code, label) VALUES ('SITE_ORG_ORGANISME', 'Organime(s)');
INSERT INTO abstract_branch_node (code, label) VALUES ('SITE_ORG_PERSONNE', 'Personne(s)');
INSERT INTO abstract_branch_node (code, label) VALUES ('DOMAINE_PERSONNE', 'Personne(s)');


--
-- TOC entry 2271 (class 0 OID 20950)
-- Dependencies: 240
-- Data for Name: abstract_node; Type: TABLE DATA; Schema: metadata; Owner: postgres
--

INSERT INTO abstract_node (code, type) VALUES ('*', 'BRANCH');
INSERT INTO abstract_node (code, type) VALUES ('SITES', 'GROUP');
INSERT INTO abstract_node (code, type) VALUES ('SITE', 'OBJECT');
INSERT INTO abstract_node (code, type) VALUES ('SITE_DOMAINE', 'BRANCH');
INSERT INTO abstract_node (code, type) VALUES ('SITE_INTERVENANT', 'BRANCH');
INSERT INTO abstract_node (code, type) VALUES ('SITE_ORGANISME', 'BRANCH');
INSERT INTO abstract_node (code, type) VALUES ('SITE_DOM_DOMAINE', 'GROUP');
INSERT INTO abstract_node (code, type) VALUES ('SITE_DOM_ORGANISME', 'GROUP');
INSERT INTO abstract_node (code, type) VALUES ('SITE_DOM_PERSONNE', 'GROUP');
INSERT INTO abstract_node (code, type) VALUES ('SITE_INTER_ORGANISME', 'GROUP');
INSERT INTO abstract_node (code, type) VALUES ('SITE_INTER_DOMAINE', 'GROUP');
INSERT INTO abstract_node (code, type) VALUES ('SITE_INTER_PERSONNE', 'GROUP');
INSERT INTO abstract_node (code, type) VALUES ('SITE_ORG_DOMAINE', 'GROUP');
INSERT INTO abstract_node (code, type) VALUES ('SITE_ORG_ORGANISME', 'GROUP');
INSERT INTO abstract_node (code, type) VALUES ('SITE_ORG_PERSONNE', 'GROUP');
INSERT INTO abstract_node (code, type) VALUES ('DOMAINE', 'OBJECT');
INSERT INTO abstract_node (code, type) VALUES ('ORGANISME', 'OBJECT');
INSERT INTO abstract_node (code, type) VALUES ('PERSONNE', 'OBJECT');
INSERT INTO abstract_node (code, type) VALUES ('DOMAINE_PERSONNE', 'GROUP');


--
-- TOC entry 2272 (class 0 OID 20953)
-- Dependencies: 241
-- Data for Name: abstract_node_tree; Type: TABLE DATA; Schema: metadata; Owner: postgres
--

INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('*', '*', 1);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('SITES', '*', 1);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('SITE_DOMAINE', 'SITE', 1);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('SITE_INTERVENANT', 'SITE', 2);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('SITE_ORGANISME', 'SITE', 3);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('SITE_DOM_DOMAINE', 'SITE_DOMAINE', 1);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('SITE_DOM_ORGANISME', 'SITE_DOMAINE', 2);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('SITE_DOM_PERSONNE', 'SITE_DOMAINE', 3);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('SITE_INTER_DOMAINE', 'SITE_INTERVENANT', 1);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('SITE_INTER_ORGANISME', 'SITE_INTERVENANT', 2);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('SITE_INTER_PERSONNE', 'SITE_INTERVENANT', 3);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('SITE_ORG_DOMAINE', 'SITE_ORGANISME', 1);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('SITE_ORG_ORGANISME', 'SITE_ORGANISME', 2);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('SITE_ORG_PERSONNE', 'SITE_ORGANISME', 3);
INSERT INTO abstract_node_tree (code, parent_code, "order") VALUES ('DOMAINE_PERSONNE', 'DOMAINE', 1);


--
-- TOC entry 2273 (class 0 OID 20956)
-- Dependencies: 242
-- Data for Name: abstract_object_group_node; Type: TABLE DATA; Schema: metadata; Owner: postgres
--

INSERT INTO abstract_object_group_node (code, sql_objects_query, child_object_node) VALUES ('SITE_ORG_ORGANISME', 'SELECT o.designation as label, o.id_contact as pk
FROM rawdata.site s 
JOIN rawdata.contact_site cs on cs.num_site = s.num_site 
JOIN rawdata.contact c on c.id_contact = cs.id_contact 
JOIN rawdata.organisme o on o.id_contact = c.id_contact 
WHERE s.num_site = $1 and cs.type=''ORGANISME''
ORDER BY o.designation', 'ORGANISME');
INSERT INTO abstract_object_group_node (code, sql_objects_query, child_object_node) VALUES ('SITES', 'SELECT  ''Site '' || num_site as label, num_site as pk FROM rawdata.site ORDER BY num_site', 'SITE');
INSERT INTO abstract_object_group_node (code, sql_objects_query, child_object_node) VALUES ('DOMAINE_PERSONNE', 'SELECT p.nom || '' '' || p.prenom as label, p.id_contact as pk
FROM rawdata.domaine d 
JOIN rawdata.personne_domaine pd on pd.id_domaine = d.id_contact 
JOIN rawdata.personne p on p.id_contact = pd.id_personne
WHERE d.id_contact = $1
ORDER BY p.nom, p.prenom', 'PERSONNE');
INSERT INTO abstract_object_group_node (code, sql_objects_query, child_object_node) VALUES ('SITE_DOM_DOMAINE', 'SELECT d.nom as label, d.id_contact as pk
FROM rawdata.site s 
JOIN rawdata.contact_site cs on cs.num_site = s.num_site 
JOIN rawdata.contact c on c.id_contact = cs.id_contact 
JOIN rawdata.domaine d on d.id_contact = c.id_contact 
WHERE s.num_site = $1 and cs.type = ''DOMAINE''
ORDER BY d.nom', 'DOMAINE');
INSERT INTO abstract_object_group_node (code, sql_objects_query, child_object_node) VALUES ('SITE_DOM_PERSONNE', 'SELECT p.nom || '' '' || p.prenom as label, p.id_contact as pk
FROM rawdata.site s 
JOIN rawdata.contact_site cs on cs.num_site = s.num_site 
JOIN rawdata.contact c on c.id_contact = cs.id_contact 
JOIN rawdata.personne p on p.id_contact = c.id_contact 
WHERE s.num_site = $1 and cs.type=''DOMAINE'' ORDER BY p.nom, p.prenom', 'PERSONNE');
INSERT INTO abstract_object_group_node (code, sql_objects_query, child_object_node) VALUES ('SITE_INTER_DOMAINE', 'SELECT d.nom as label, d.id_contact as pk
FROM rawdata.site s 
JOIN rawdata.contact_site cs on cs.num_site = s.num_site 
JOIN rawdata.contact c on c.id_contact = cs.id_contact 
JOIN rawdata.domaine d on d.id_contact = c.id_contact 
WHERE s.num_site = $1 and cs.type = ''INTERVENANT''
ORDER BY d.nom', 'DOMAINE');
INSERT INTO abstract_object_group_node (code, sql_objects_query, child_object_node) VALUES ('SITE_ORG_DOMAINE', 'SELECT d.nom as label, d.id_contact as pk
FROM rawdata.site s 
JOIN rawdata.contact_site cs on cs.num_site = s.num_site 
JOIN rawdata.contact c on c.id_contact = cs.id_contact 
JOIN rawdata.domaine d on d.id_contact = c.id_contact 
WHERE s.num_site = $1 and cs.type = ''ORGANISME''
ORDER BY d.nom', 'DOMAINE');
INSERT INTO abstract_object_group_node (code, sql_objects_query, child_object_node) VALUES ('SITE_INTER_PERSONNE', 'SELECT p.nom || '' '' || p.prenom as label, p.id_contact as pk
FROM rawdata.site s 
JOIN rawdata.contact_site cs on cs.num_site = s.num_site 
JOIN rawdata.contact c on c.id_contact = cs.id_contact 
JOIN rawdata.personne p on p.id_contact = c.id_contact 
WHERE s.num_site = $1 and cs.type=''INTERVENANT'' ORDER BY p.nom, p.prenom', 'PERSONNE');
INSERT INTO abstract_object_group_node (code, sql_objects_query, child_object_node) VALUES ('SITE_ORG_PERSONNE', 'SELECT p.nom || '' '' || p.prenom as label, p.id_contact as pk
FROM rawdata.site s 
JOIN rawdata.contact_site cs on cs.num_site = s.num_site 
JOIN rawdata.contact c on c.id_contact = cs.id_contact 
JOIN rawdata.personne p on p.id_contact = c.id_contact 
WHERE s.num_site = $1 and cs.type=''ORGANISME'' ORDER BY p.nom, p.prenom', 'PERSONNE');
INSERT INTO abstract_object_group_node (code, sql_objects_query, child_object_node) VALUES ('SITE_DOM_ORGANISME', 'SELECT o.designation as label, o.id_contact as pk
FROM rawdata.site s 
JOIN rawdata.contact_site cs on cs.num_site = s.num_site 
JOIN rawdata.contact c on c.id_contact = cs.id_contact 
JOIN rawdata.organisme o on o.id_contact = c.id_contact 
WHERE s.num_site = $1 and cs.type=''DOMAINE''
ORDER BY o.designation', 'ORGANISME');
INSERT INTO abstract_object_group_node (code, sql_objects_query, child_object_node) VALUES ('SITE_INTER_ORGANISME', 'SELECT o.designation as label, o.id_contact as pk
FROM rawdata.site s 
JOIN rawdata.contact_site cs on cs.num_site = s.num_site 
JOIN rawdata.contact c on c.id_contact = cs.id_contact 
JOIN rawdata.organisme o on o.id_contact = c.id_contact 
WHERE s.num_site = $1 and cs.type=''INTERVENANT''
ORDER BY o.designation', 'ORGANISME');


--
-- TOC entry 2274 (class 0 OID 20962)
-- Dependencies: 243
-- Data for Name: abstract_object_node; Type: TABLE DATA; Schema: metadata; Owner: postgres
--

INSERT INTO abstract_object_node (code, object_type) VALUES ('SITE', 'UI_OBJECT_SITE');
INSERT INTO abstract_object_node (code, object_type) VALUES ('ORGANISME', 'UI_OBJECT_ORGANISME');
INSERT INTO abstract_object_node (code, object_type) VALUES ('PERSONNE', 'UI_OBJECT_PERSONNE');
INSERT INTO abstract_object_node (code, object_type) VALUES ('DOMAINE', 'UI_OBJECT_DOMAINE');


--
-- TOC entry 2144 (class 2606 OID 20966)
-- Name: abstract_branch_node_pk; Type: CONSTRAINT; Schema: metadata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY abstract_branch_node
    ADD CONSTRAINT abstract_branch_node_pk PRIMARY KEY (code);


--
-- TOC entry 2146 (class 2606 OID 20968)
-- Name: abstract_node_pk; Type: CONSTRAINT; Schema: metadata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY abstract_node
    ADD CONSTRAINT abstract_node_pk PRIMARY KEY (code);


--
-- TOC entry 2148 (class 2606 OID 20970)
-- Name: abstract_node_tree_pk; Type: CONSTRAINT; Schema: metadata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY abstract_node_tree
    ADD CONSTRAINT abstract_node_tree_pk PRIMARY KEY (code, parent_code);


--
-- TOC entry 2150 (class 2606 OID 20972)
-- Name: abstract_object_group_node_pk; Type: CONSTRAINT; Schema: metadata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY abstract_object_group_node
    ADD CONSTRAINT abstract_object_group_node_pk PRIMARY KEY (code);


--
-- TOC entry 2152 (class 2606 OID 20974)
-- Name: abstract_object_node_pk; Type: CONSTRAINT; Schema: metadata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY abstract_object_node
    ADD CONSTRAINT abstract_object_node_pk PRIMARY KEY (code);


--
-- TOC entry 2153 (class 2606 OID 20990)
-- Name: abstract_branch_node_code_fkey; Type: FK CONSTRAINT; Schema: metadata; Owner: postgres
--

ALTER TABLE ONLY abstract_branch_node
    ADD CONSTRAINT abstract_branch_node_code_fkey FOREIGN KEY (code) REFERENCES abstract_node(code);


--
-- TOC entry 2154 (class 2606 OID 20995)
-- Name: abstract_node_tree_code_fkey; Type: FK CONSTRAINT; Schema: metadata; Owner: postgres
--

ALTER TABLE ONLY abstract_node_tree
    ADD CONSTRAINT abstract_node_tree_code_fkey FOREIGN KEY (code) REFERENCES abstract_node(code);


--
-- TOC entry 2155 (class 2606 OID 21000)
-- Name: abstract_node_tree_parent_code_fkey; Type: FK CONSTRAINT; Schema: metadata; Owner: postgres
--

ALTER TABLE ONLY abstract_node_tree
    ADD CONSTRAINT abstract_node_tree_parent_code_fkey FOREIGN KEY (parent_code) REFERENCES abstract_node(code);


--
-- TOC entry 2157 (class 2606 OID 20985)
-- Name: abstract_object_group_node_child_object_node_fkey; Type: FK CONSTRAINT; Schema: metadata; Owner: postgres
--

ALTER TABLE ONLY abstract_object_group_node
    ADD CONSTRAINT abstract_object_group_node_child_object_node_fkey FOREIGN KEY (child_object_node) REFERENCES abstract_object_node(code);


--
-- TOC entry 2156 (class 2606 OID 20980)
-- Name: abstract_object_group_node_code_fkey; Type: FK CONSTRAINT; Schema: metadata; Owner: postgres
--

ALTER TABLE ONLY abstract_object_group_node
    ADD CONSTRAINT abstract_object_group_node_code_fkey FOREIGN KEY (code) REFERENCES abstract_branch_node(code);


--
-- TOC entry 2158 (class 2606 OID 20975)
-- Name: abstract_object_node_code_fkey; Type: FK CONSTRAINT; Schema: metadata; Owner: postgres
--

ALTER TABLE ONLY abstract_object_node
    ADD CONSTRAINT abstract_object_node_code_fkey FOREIGN KEY (code) REFERENCES abstract_node(code);


-- Completed on 2015-09-16 14:32:38

--
-- PostgreSQL database dump complete
--

