DROP TABLE Reisebuero      CASCADE CONSTRAINTS;
DROP TABLE Person          CASCADE CONSTRAINTS;
DROP TABLE Mitarbeiter     CASCADE CONSTRAINTS;
DROP TABLE Kunde           CASCADE CONSTRAINTS;
DROP TABLE Reise           CASCADE CONSTRAINTS;
DROP TABLE Hotel           CASCADE CONSTRAINTS;
DROP TABLE Zimmer          CASCADE CONSTRAINTS;
DROP TABLE Beratung        CASCADE CONSTRAINTS;
DROP TABLE Buchung         CASCADE CONSTRAINTS;
DROP TABLE Platzierung     CASCADE CONSTRAINTS;

DROP SEQUENCE reise_key_sq;
DROP PROCEDURE count_reise_gte;