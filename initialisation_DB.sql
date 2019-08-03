-- creation de la bd et super admin --
--CREATE DATABASE GBoC;
--CREATE ROLE super_admin password 'super_admin' login;
--GRANT ALL ON DATABASE GBoC TO super_admin WITH GRANT OPTION;
DROP TABLE messages;
DROP TABLE tasks;
DROP TABLE events;
DROP TABLE commissions;
DROP TABLE volunteers;
DROP TYPE role;

-- Enum --

CREATE TYPE role AS ENUM ('VOLUNTEER', 'MODERATOR', 'ADMIN');

-- Tables --

CREATE TABLE IF NOT EXISTS volunteers(
    id_volunteer		UUID	PRIMARY KEY,
    name_volunteer		TEXT	NOT NULL,
    surname_volunteer   TEXT	NOT NULL,
    birth_date          DATE	NOT NULL,
    number_tel	        TEXT,
    mail                TEXT    UNIQUE NOT NULL,
    password            TEXT    NOT NULL,
    role	            role	NOT NULL DEFAULT 'VOLUNTEER' 
);

CREATE TABLE IF NOT EXISTS commissions(
    id_commission		UUID 	PRIMARY KEY,
    name_commission		TEXT	UNIQUE NOT NULL,
    moderators	        UUID[]	NOT NULL,
    volunteers          UUID[],
    volunteers_waiting  UUID[],
    active              BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS events(
    id_event		    UUID		PRIMARY KEY,
    name_event		    TEXT		NOT NULL,
    info_event	        TEXT,
    begin_time_event    timestamp NOT NULL,
    end_time_event      timestamp NOT NULL,
    places_event	    TEXT,
    expected_people   	INTEGER		DEFAULT 10,
    commissions         UUID[]
);

CREATE TABLE IF NOT EXISTS tasks(
    id_task		            UUID	    PRIMARY KEY,
    event	                UUID	    REFERENCES events(id_event) NOT NULL,
    commission	            UUID 	    REFERENCES commissions(id_commission) NOT NULL,
    name_task		        TEXT	    NOT NULL,
    info_task	            TEXT,
    begin_time_task	        timestamp NOT NULL,
    end_time_task	        timestamp NOT NULL,
    places_task	            TEXT,	    
    max_volunteers	        INTEGER	    NOT NULL,
    registered_volunteers   UUID[]
);

CREATE TABLE IF NOT EXISTS messages(
    id_message      UUID        PRIMARY KEY,
    messenger       UUID        REFERENCES volunteers(id_volunteer) NOT NULL,
    recipient       UUID        REFERENCES tasks(id_task) NOT NULL,
    time_message    timestamp NOT NULL
);
