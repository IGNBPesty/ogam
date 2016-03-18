--
-- PostgreSQL database dump
--

-- Dumped from database version 9.4.2
-- Dumped by pg_dump version 9.4.2
-- Started on 2015-09-16 14:30:02

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 8 (class 2615 OID 20301)
-- Name: rawdata; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA rawdata;


ALTER SCHEMA rawdata OWNER TO postgres;

SET search_path = rawdata, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 229 (class 1259 OID 20407)
-- Name: adresse; Type: TABLE; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE TABLE adresse (
    id_contact integer NOT NULL,
    nom text NOT NULL,
    adresse text,
    compl_adresse text,
    lieu_dit text,
    code_postal text
);


ALTER TABLE adresse OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 20308)
-- Name: contact; Type: TABLE; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE TABLE contact (
    id_contact integer NOT NULL,
    type character varying(36)
);


ALTER TABLE contact OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 20421)
-- Name: contact_site; Type: TABLE; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE TABLE contact_site (
    num_site integer NOT NULL,
    id_contact integer NOT NULL,
    type character varying(36)
);


ALTER TABLE contact_site OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 20383)
-- Name: courriel; Type: TABLE; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE TABLE courriel (
    id_contact integer NOT NULL,
    nom text NOT NULL,
    courriel text
);


ALTER TABLE courriel OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 20368)
-- Name: domaine; Type: TABLE; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE TABLE domaine (
    id_contact integer,
    type character varying(36),
    nom text
)
INHERITS (contact);


ALTER TABLE domaine OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 20356)
-- Name: organisme; Type: TABLE; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE TABLE organisme (
    id_contact integer,
    type character varying(36),
    designation text,
    service text,
    type_organisme text
)
INHERITS (contact);


ALTER TABLE organisme OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 20362)
-- Name: personne; Type: TABLE; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE TABLE personne (
    id_contact integer,
    type character varying(36),
    nom text,
    prenom text
)
INHERITS (contact);


ALTER TABLE personne OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 20436)
-- Name: personne_domaine; Type: TABLE; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE TABLE personne_domaine (
    id_domaine integer NOT NULL,
    id_personne integer NOT NULL,
    type character varying(36)
);


ALTER TABLE personne_domaine OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 20455)
-- Name: personne_organisme; Type: TABLE; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE TABLE personne_organisme (
    id_organisme integer NOT NULL,
    id_personne integer NOT NULL
);


ALTER TABLE personne_organisme OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 20302)
-- Name: site; Type: TABLE; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE TABLE site (
    num_site integer NOT NULL,
    orientation integer,
    contexte text,
    commune text,
    cellule16x16 integer
);


ALTER TABLE site OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 20395)
-- Name: telephone; Type: TABLE; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE TABLE telephone (
    id_contact integer NOT NULL,
    nom text NOT NULL,
    numero text
);


ALTER TABLE telephone OWNER TO postgres;

--
-- TOC entry 2308 (class 0 OID 20407)
-- Dependencies: 229
-- Data for Name: adresse; Type: TABLE DATA; Schema: rawdata; Owner: postgres
--

INSERT INTO adresse (id_contact, nom, adresse, compl_adresse, lieu_dit, code_postal) VALUES (1, 'IGN-Nogent', 'Château des barres', '-', '', '45290');
INSERT INTO adresse (id_contact, nom, adresse, compl_adresse, lieu_dit, code_postal) VALUES (3, 'Maison', 'Nogent', '-', '', '45290');
INSERT INTO adresse (id_contact, nom, adresse, compl_adresse, lieu_dit, code_postal) VALUES (2, 'INRA-Orléans', 'St Cyr-en-val', '-', NULL, '45000');
INSERT INTO adresse (id_contact, nom, adresse, compl_adresse, lieu_dit, code_postal) VALUES (4, 'Maison', 'St Cyr-en-val', '-', NULL, '45000');
INSERT INTO adresse (id_contact, nom, adresse, compl_adresse, lieu_dit, code_postal) VALUES (5, 'Nogent', 'Arboretum', '-', NULL, '45290');
INSERT INTO adresse (id_contact, nom, adresse, compl_adresse, lieu_dit, code_postal) VALUES (6, 'INRA-Orléans', 'St Cyr-en-val', '-', NULL, '45000');


--
-- TOC entry 2302 (class 0 OID 20308)
-- Dependencies: 223
-- Data for Name: contact; Type: TABLE DATA; Schema: rawdata; Owner: postgres
--



--
-- TOC entry 2309 (class 0 OID 20421)
-- Dependencies: 230
-- Data for Name: contact_site; Type: TABLE DATA; Schema: rawdata; Owner: postgres
--

INSERT INTO contact_site (num_site, id_contact, type) VALUES (1, 1, 'ORGANISME');
INSERT INTO contact_site (num_site, id_contact, type) VALUES (1, 3, 'INTERVENANT');
INSERT INTO contact_site (num_site, id_contact, type) VALUES (1, 5, 'DOMAINE');
INSERT INTO contact_site (num_site, id_contact, type) VALUES (2, 4, 'INTERVENANT');
INSERT INTO contact_site (num_site, id_contact, type) VALUES (2, 6, 'DOMAINE');
INSERT INTO contact_site (num_site, id_contact, type) VALUES (2, 2, 'ORGANISME');


--
-- TOC entry 2306 (class 0 OID 20383)
-- Dependencies: 227
-- Data for Name: courriel; Type: TABLE DATA; Schema: rawdata; Owner: postgres
--

INSERT INTO courriel (id_contact, nom, courriel) VALUES (1, '1', '1@1');
INSERT INTO courriel (id_contact, nom, courriel) VALUES (2, '2', '2@2');
INSERT INTO courriel (id_contact, nom, courriel) VALUES (3, '3', '3@3');
INSERT INTO courriel (id_contact, nom, courriel) VALUES (4, '4', '4@4');
INSERT INTO courriel (id_contact, nom, courriel) VALUES (5, '5', '5@5');
INSERT INTO courriel (id_contact, nom, courriel) VALUES (6, '6', '6@6');


--
-- TOC entry 2305 (class 0 OID 20368)
-- Dependencies: 226
-- Data for Name: domaine; Type: TABLE DATA; Schema: rawdata; Owner: postgres
--

INSERT INTO domaine (id_contact, type, nom) VALUES (5, 'DOMAINE', 'Arboretum');
INSERT INTO domaine (id_contact, type, nom) VALUES (6, 'DOMAINE', 'Inra');


--
-- TOC entry 2303 (class 0 OID 20356)
-- Dependencies: 224
-- Data for Name: organisme; Type: TABLE DATA; Schema: rawdata; Owner: postgres
--

INSERT INTO organisme (id_contact, type, designation, service, type_organisme) VALUES (1, 'ORGANISME', 'IGN', 'SIDT', 'PUBLIC');
INSERT INTO organisme (id_contact, type, designation, service, type_organisme) VALUES (2, 'ORGANISME', 'INRA', 'INFOSOL', 'PUBLIC');


--
-- TOC entry 2304 (class 0 OID 20362)
-- Dependencies: 225
-- Data for Name: personne; Type: TABLE DATA; Schema: rawdata; Owner: postgres
--

INSERT INTO personne (id_contact, type, nom, prenom) VALUES (3, 'PERSONNE', 'Sylvain', 'Galopin');
INSERT INTO personne (id_contact, type, nom, prenom) VALUES (4, 'PERSONNE', 'Benoit', 'Toutain');


--
-- TOC entry 2310 (class 0 OID 20436)
-- Dependencies: 231
-- Data for Name: personne_domaine; Type: TABLE DATA; Schema: rawdata; Owner: postgres
--

INSERT INTO personne_domaine (id_domaine, id_personne, type) VALUES (5, 3, 'EXPLOITANT');
INSERT INTO personne_domaine (id_domaine, id_personne, type) VALUES (6, 4, 'GESTIONNAIRE');


--
-- TOC entry 2311 (class 0 OID 20455)
-- Dependencies: 232
-- Data for Name: personne_organisme; Type: TABLE DATA; Schema: rawdata; Owner: postgres
--

INSERT INTO personne_organisme (id_organisme, id_personne) VALUES (1, 3);
INSERT INTO personne_organisme (id_organisme, id_personne) VALUES (2, 4);


--
-- TOC entry 2301 (class 0 OID 20302)
-- Dependencies: 222
-- Data for Name: site; Type: TABLE DATA; Schema: rawdata; Owner: postgres
--

INSERT INTO site (num_site, orientation, contexte, commune, cellule16x16) VALUES (1, 1, 'Plaine', 'Nogent-Sur-Vernisson', 46);
INSERT INTO site (num_site, orientation, contexte, commune, cellule16x16) VALUES (2, 2, 'Plaine', 'Orléans', 47);


--
-- TOC entry 2307 (class 0 OID 20395)
-- Dependencies: 228
-- Data for Name: telephone; Type: TABLE DATA; Schema: rawdata; Owner: postgres
--

INSERT INTO telephone (id_contact, nom, numero) VALUES (1, '1', '1111');
INSERT INTO telephone (id_contact, nom, numero) VALUES (2, '2', '2222');
INSERT INTO telephone (id_contact, nom, numero) VALUES (3, '3', '3333');
INSERT INTO telephone (id_contact, nom, numero) VALUES (4, '4', '4444');
INSERT INTO telephone (id_contact, nom, numero) VALUES (5, '5', '5555');
INSERT INTO telephone (id_contact, nom, numero) VALUES (6, '6', '6666');


--
-- TOC entry 2172 (class 2606 OID 20479)
-- Name: pk_adresse; Type: CONSTRAINT; Schema: rawdata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY adresse
    ADD CONSTRAINT pk_adresse PRIMARY KEY (id_contact, nom);


--
-- TOC entry 2157 (class 2606 OID 20376)
-- Name: pk_contact; Type: CONSTRAINT; Schema: rawdata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY contact
    ADD CONSTRAINT pk_contact PRIMARY KEY (id_contact);


--
-- TOC entry 2176 (class 2606 OID 20477)
-- Name: pk_contact_site; Type: CONSTRAINT; Schema: rawdata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY contact_site
    ADD CONSTRAINT pk_contact_site PRIMARY KEY (num_site, id_contact);


--
-- TOC entry 2166 (class 2606 OID 20483)
-- Name: pk_courriel; Type: CONSTRAINT; Schema: rawdata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY courriel
    ADD CONSTRAINT pk_courriel PRIMARY KEY (id_contact, nom);


--
-- TOC entry 2163 (class 2606 OID 20440)
-- Name: pk_domaine; Type: CONSTRAINT; Schema: rawdata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY domaine
    ADD CONSTRAINT pk_domaine PRIMARY KEY (id_contact);


--
-- TOC entry 2159 (class 2606 OID 20459)
-- Name: pk_organisme; Type: CONSTRAINT; Schema: rawdata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY organisme
    ADD CONSTRAINT pk_organisme PRIMARY KEY (id_contact);


--
-- TOC entry 2161 (class 2606 OID 20448)
-- Name: pk_personne; Type: CONSTRAINT; Schema: rawdata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY personne
    ADD CONSTRAINT pk_personne PRIMARY KEY (id_contact);


--
-- TOC entry 2180 (class 2606 OID 20475)
-- Name: pk_personne_domaine; Type: CONSTRAINT; Schema: rawdata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY personne_domaine
    ADD CONSTRAINT pk_personne_domaine PRIMARY KEY (id_domaine, id_personne);


--
-- TOC entry 2184 (class 2606 OID 20473)
-- Name: pk_personne_organisme; Type: CONSTRAINT; Schema: rawdata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY personne_organisme
    ADD CONSTRAINT pk_personne_organisme PRIMARY KEY (id_organisme, id_personne);


--
-- TOC entry 2155 (class 2606 OID 20420)
-- Name: pk_site; Type: CONSTRAINT; Schema: rawdata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY site
    ADD CONSTRAINT pk_site PRIMARY KEY (num_site);


--
-- TOC entry 2169 (class 2606 OID 20481)
-- Name: pk_telephone; Type: CONSTRAINT; Schema: rawdata; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY telephone
    ADD CONSTRAINT pk_telephone PRIMARY KEY (id_contact, nom);


--
-- TOC entry 2164 (class 1259 OID 20394)
-- Name: fki_contact; Type: INDEX; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE INDEX fki_contact ON courriel USING btree (id_contact);


--
-- TOC entry 2170 (class 1259 OID 20418)
-- Name: fki_contact_adresse; Type: INDEX; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE INDEX fki_contact_adresse ON adresse USING btree (id_contact);


--
-- TOC entry 2173 (class 1259 OID 20435)
-- Name: fki_contact_contact_site; Type: INDEX; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE INDEX fki_contact_contact_site ON contact_site USING btree (id_contact);


--
-- TOC entry 2167 (class 1259 OID 20406)
-- Name: fki_contact_tel; Type: INDEX; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE INDEX fki_contact_tel ON telephone USING btree (id_contact);


--
-- TOC entry 2177 (class 1259 OID 20454)
-- Name: fki_domaine_personne; Type: INDEX; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE INDEX fki_domaine_personne ON personne_domaine USING btree (id_personne);


--
-- TOC entry 2178 (class 1259 OID 20446)
-- Name: fki_domaine_personne_domaine; Type: INDEX; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE INDEX fki_domaine_personne_domaine ON personne_domaine USING btree (id_domaine);


--
-- TOC entry 2181 (class 1259 OID 20471)
-- Name: fki_organisme_personne_organisme; Type: INDEX; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE INDEX fki_organisme_personne_organisme ON personne_organisme USING btree (id_organisme);


--
-- TOC entry 2182 (class 1259 OID 20465)
-- Name: fki_personne_personne_organisme; Type: INDEX; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE INDEX fki_personne_personne_organisme ON personne_organisme USING btree (id_personne);


--
-- TOC entry 2174 (class 1259 OID 20429)
-- Name: fki_site_contact_site; Type: INDEX; Schema: rawdata; Owner: postgres; Tablespace: 
--

CREATE INDEX fki_site_contact_site ON contact_site USING btree (num_site);


--
-- TOC entry 2186 (class 2606 OID 20441)
-- Name: fk_domaine_personne_domaine; Type: FK CONSTRAINT; Schema: rawdata; Owner: postgres
--

ALTER TABLE ONLY personne_domaine
    ADD CONSTRAINT fk_domaine_personne_domaine FOREIGN KEY (id_domaine) REFERENCES domaine(id_contact);


--
-- TOC entry 2189 (class 2606 OID 20466)
-- Name: fk_organisme_personne_organisme; Type: FK CONSTRAINT; Schema: rawdata; Owner: postgres
--

ALTER TABLE ONLY personne_organisme
    ADD CONSTRAINT fk_organisme_personne_organisme FOREIGN KEY (id_organisme) REFERENCES organisme(id_contact);


--
-- TOC entry 2187 (class 2606 OID 20449)
-- Name: fk_personne_personne_domaine; Type: FK CONSTRAINT; Schema: rawdata; Owner: postgres
--

ALTER TABLE ONLY personne_domaine
    ADD CONSTRAINT fk_personne_personne_domaine FOREIGN KEY (id_personne) REFERENCES personne(id_contact);


--
-- TOC entry 2188 (class 2606 OID 20460)
-- Name: fk_personne_personne_organisme; Type: FK CONSTRAINT; Schema: rawdata; Owner: postgres
--

ALTER TABLE ONLY personne_organisme
    ADD CONSTRAINT fk_personne_personne_organisme FOREIGN KEY (id_personne) REFERENCES personne(id_contact);


--
-- TOC entry 2185 (class 2606 OID 20424)
-- Name: fk_site_contact_site; Type: FK CONSTRAINT; Schema: rawdata; Owner: postgres
--

ALTER TABLE ONLY contact_site
    ADD CONSTRAINT fk_site_contact_site FOREIGN KEY (num_site) REFERENCES site(num_site);


-- Completed on 2015-09-16 14:30:02

--
-- PostgreSQL database dump complete
--

