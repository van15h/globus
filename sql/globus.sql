CREATE TABLE Reisebuero (
  id                CHAR (10) primary key NOT NULL,
  name              VARCHAR(40),
  plz               CHAR(4),
  ort               VARCHAR(25),
  strasse           VARCHAR(25),
  kontodaten        VARCHAR(16) UNIQUE
);

CREATE TABLE Person (
  id                INTEGER primary key NOT NULL,
  name              VARCHAR(40) NOT NULL,
  SVNummer          CHAR(8) UNIQUE,
  geburtsdatum      DATE,
  email             VARCHAR(30),
  plz               CHAR(4),
  ort               VARCHAR(25),
  strasse           VARCHAR(25)
);

CREATE TABLE Mitarbeiter (
  personid             INTEGER NOT NULL,
  steuernummer         NUMERIC(12) primary key NOT NULL,
  gehalt               DOUBLE PRECISION,
  beschaeftigungRBid   CHAR (10) NOT NULL,
  CHECK  (gehalt > 0),
  FOREIGN KEY (personid) REFERENCES Person (id) ON DELETE CASCADE,
  FOREIGN KEY (beschaeftigungRBid) REFERENCES Reisebuero (id) ON DELETE CASCADE
);

CREATE TABLE Kunde (
  personid          INTEGER NOT NULL,
  kundenummer       NUMERIC(8) primary key NOT NULL,
  telefonnummer     NUMERIC(15), # original was NUMBER
  kontodaten        VARCHAR(16) UNIQUE,
  FOREIGN KEY (personid) REFERENCES Person (id) ON DELETE CASCADE
);

CREATE TABLE Reise (
  id                INTEGER primary key NOT NULL,
  name              VARCHAR(20),
  einreisedatum     DATE,
  reisedauer        CHAR(3),
  preis             DOUBLE PRECISION
);

CREATE TABLE Hotel (
  id               INTEGER  primary key NOT NULL,
  name             VARCHAR(40),
  sterne           CHAR(1),
  verpflegung      VARCHAR(3),
  plz              CHAR(4),
  ort              VARCHAR(25),
  strasse          VARCHAR(25),
  CHECK (verpflegung IN ('BB', 'HP', 'All'))
);

CREATE TABLE Zimmer (
  id             INTEGER NOT NULL,
  nummer         NUMERIC(5) NOT NULL,
  variation      VARCHAR(10),
  hotelid        INTEGER,
  CHECK (variation IN ('EZ', 'DZ', 'Suit', 'DeLUX')),
  PRIMARY KEY (id, hotelid),
  FOREIGN KEY (hotelid) REFERENCES Hotel (id) ON DELETE CASCADE
);

CREATE TABLE Beratung (
  steuernummer  NUMERIC(12) NOT NULL,
  kundenummer   NUMERIC(8)  NOT NULL,
  reiseid       INTEGER NOT NULL,

  PRIMARY KEY (steuernummer, kundenummer, reiseid),
  FOREIGN KEY (steuernummer) REFERENCES Mitarbeiter (steuernummer) ON DELETE CASCADE,
  FOREIGN KEY (kundenummer) REFERENCES Kunde (kundenummer) ON DELETE CASCADE,
  FOREIGN KEY (reiseid) REFERENCES Reise (id) ON DELETE CASCADE
);

CREATE TABLE Buchung (
  kundenummer   NUMERIC(8)  NOT NULL,
  reiseid       INTEGER NOT NULL,
  reisebueroid  CHAR (10) NOT NULL,

  PRIMARY KEY (kundenummer, reiseid),
  FOREIGN KEY (kundenummer) REFERENCES Kunde (kundenummer) ON DELETE CASCADE,
  FOREIGN KEY (reiseid) REFERENCES Reise (id) ON DELETE CASCADE,
  FOREIGN KEY (reisebueroid) REFERENCES Reisebuero (id) ON DELETE CASCADE
);

CREATE TABLE Platzierung (
  hotelid      INTEGER NOT NULL,
  reiseid      INTEGER NOT NULL,

  PRIMARY KEY (hotelid, reiseid),
  FOREIGN KEY (hotelid) REFERENCES Hotel (id) ON DELETE CASCADE,
  FOREIGN KEY (reiseid) REFERENCES Reise (id) ON DELETE CASCADE
);