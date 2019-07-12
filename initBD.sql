-- creation de la bd et super admin --
--CREATE DATABASE GBoC;
--CREATE ROLE super_admin password 'super_admin' login;
--GRANT ALL ON DATABASE GBoC TO super_admin WITH GRANT OPTION;

DROP TABLE taches;
DROP TABLE evenements;
DROP TABLE commissions;
DROP TABLE benevoles;

-- Enum --

CREATE TYPE role AS ENUM ('BENEVOLE', 'MODERATEUR', 'ADMIN');

-- Tables --

CREATE TABLE IF NOT EXISTS benevoles(
    id		        UUID	PRIMARY KEY,
    nom		        TEXT	NOT NULL,
    prenom	        TEXT	NOT NULL,
    dateNaissance   DATE	NOT NULL,
    numeroTel	    INT,
    mail            TEXT    UNIQUE NOT NULL,
    password        TEXT    NOT NULL,
    role	        role	NOT NULL
);

CREATE TABLE IF NOT EXISTS commissions(
    id		            UUID	PRIMARY KEY,
    nom		            TEXT	UNIQUE NOT NULL,
    moderateur	        UUID	REFERENCES benevoles(id) NOT NULL,
    listBenevoles       UUID[],
    benevoles_attente   UUID[]
);

CREATE TABLE IF NOT EXISTS evenements(
    id		    	    UUID		PRIMARY KEY,
    nom		    	    TEXT		NOT NULL,
    description	        TEXT,
    dateDebut	        timestamptz NOT NULL,
    dateFin	    	    timestamptz NOT NULL,
    lieux	    	    TEXT		DEFAULT 'mission bretonne',
    nbPersAttendu   	INTEGER		DEFAULT 10,
    comsParticipantes   UUID[]
);

CREATE TABLE IF NOT EXISTS taches(
    id		        UUID	    PRIMARY KEY,
    evenement	    UUID	    REFERENCES evenements(id) NOT NULL,
    commission	    UUID 	    REFERENCES commissions(id) NOT NULL,
    nom		        TEXT	    NOT NULL,
    description	    TEXT,
    dateDebut	    timestamptz NOT NULL,
    dateFin	        timestamptz NOT NULL,
    lieux	        TEXT	    DEFAULT 'mission bretonne',
    nbBeneMax	    INTEGER	    NOT NULL,
    nbBeneAttente   INTEGER	    DEFAULT 3,
    beneInscrit	    UUID[],
    beneAttente	    UUID[]
);