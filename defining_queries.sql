create table Ruolo(
   nome VARCHAR(10),
   PRIMARY KEY(nome)
);

create table Account(
   ID INT AUTO_INCREMENT,
   nickname VARCHAR(15) NOT NULL,
   email VARCHAR(320) NOT NULL,
   password VARCHAR(256) NOT NULL,
   nome VARCHAR(20),
   cognome VARCHAR(20),
   paese VARCHAR(30),
   data_nascita DATE,
   data_registrazione DATE,
   ruolo VARCHAR(10),
   PRIMARY KEY (ID),
   FOREIGN KEY (ruolo) REFERENCES Ruolo(nome),
   UNIQUE (email),
   UNIQUE (nickname)
);

create table Lingua(
   nome VARCHAR(15),
   PRIMARY KEY(nome)
);

create table Storia(
   ID INT AUTO_INCREMENT,
   titolo VARCHAR(50) NOT NULL,
   numero_capitolo INT,
   data_pubblicazione DATE,
   ora_pubblicazione TIMESTAMP,
   totale_voti INT,
   flag_nascosto BOOL,
   thumbnail BLOB,
   lingua VARCHAR(15),
   autore varchar(15) NOT NULL,
   PRIMARY KEY (ID),
   FOREIGN KEY (lingua) REFERENCES Lingua(nome),
   FOREIGN KEY (autore) REFERENCES Account(nickname)
);

create table Capitolo(
   ID INT AUTO_INCREMENT,
   titolo VARCHAR(50) NOT NULL,
   testo TEXT,
   data_pubblicazione DATE,
   ora_pubblicazione TIMESTAMP,
   totale_voti INT,
   flag_nascosto BOOL,
   flag_pubblicato BOOL,
   ID_storia INT NOT NULL,
   PRIMARY KEY (ID),
   FOREIGN KEY (ID_storia) REFERENCES Storia(ID)
);

create table Thought(
   ID INT AUTO_INCREMENT,
   testo VARCHAR(4096) NOT NULL,
   data_pubblicazione DATE,
   ora_pubblicazione TIMESTAMP,
   flag_nascosto BOOL,
   ID_capitolo INT NOT NULL,
   ID_thought_padre INT,
   autore varchar(15),
   PRIMARY KEY (ID),
   FOREIGN KEY (ID_capitolo) REFERENCES Capitolo(ID),
   FOREIGN KEY (ID_thought_padre) REFERENCES Thought(ID),   
   FOREIGN KEY (autore) REFERENCES Account(nickname)   
);


create table Genere(
   nome VARCHAR(15),
   PRIMARY KEY(nome)
);


create table Reaction(
   codice INT,
   PRIMARY KEY(codice)
);


create table Account_Storia(
   account_ID INT,
   storia_ID INT,
   PRIMARY KEY(account_ID, storia_ID),
   FOREIGN KEY(account_ID) REFERENCES Account(ID),
   FOREIGN KEY(storia_ID) REFERENCES Storia(ID)
);

create table Account_Genere(
   account_ID INT,
   genere_nome VARCHAR(15),
   PRIMARY KEY(account_ID, genere_nome),
   FOREIGN KEY(account_ID) REFERENCES Account(ID),
   FOREIGN KEY(genere_nome) REFERENCES Genere(nome)
);

create table Follower_Followed(
   follower_ID INT,
   followed_ID INT,
   PRIMARY KEY(follower_ID, followed_ID),
   FOREIGN KEY(follower_ID) REFERENCES Account(ID),
   FOREIGN KEY(followed_ID) REFERENCES Account(ID)
);

create table Account_Lingua(
   account_ID INT,
   lingua_nome VARCHAR(15),
   PRIMARY KEY(account_ID, lingua_nome),
   FOREIGN KEY(account_ID) REFERENCES Account(ID),
   FOREIGN KEY(lingua_nome) REFERENCES Lingua(nome)
);

create table Thought_Account_Reaction(
   thought_ID INT,
   account_ID INT,
   reaction_codice INT,   
   PRIMARY KEY(thought_ID, account_ID, reaction_codice),
   FOREIGN KEY(thought_ID) REFERENCES Thought(ID),
   FOREIGN KEY(account_ID) REFERENCES Account(ID),
   FOREIGN KEY(reaction_codice) REFERENCES Reaction(codice)
);

create table Capitolo_Account_Reaction(
   capitolo_ID INT,
   account_ID INT,
   reaction_codice INT,   
   PRIMARY KEY(capitolo_ID, account_ID, reaction_codice),
   FOREIGN KEY(capitolo_ID) REFERENCES Capitolo(ID),
   FOREIGN KEY(account_ID) REFERENCES Account(ID),
   FOREIGN KEY(reaction_codice) REFERENCES Reaction(codice)
);

create table Moderatore_Lingua(
   moderatore_ID INT,
   lingua_nome VARCHAR(15),
   PRIMARY KEY(moderatore_ID, lingua_nome),
   FOREIGN KEY(moderatore_ID) REFERENCES Account(ID),
   FOREIGN KEY(lingua_nome) REFERENCES Lingua(nome)
);

create table Genere_Storia(
   genere_nome VARCHAR(15),
   storia_ID INT,
   PRIMARY KEY(genere_nome, storia_ID),
   FOREIGN KEY(genere_nome) REFERENCES Genere(nome),
   FOREIGN KEY(storia_ID) REFERENCES Storia(ID)
);

create table Voto_Storia(
   account_ID INT,
   storia_ID INT,
   voto BOOL,
   PRIMARY KEY(account_ID, storia_ID),
   FOREIGN KEY(account_ID) REFERENCES Account(ID),
   FOREIGN KEY(storia_ID) REFERENCES Storia(ID)
);

create table Voto_Capitolo(
   account_ID INT,
   capitolo_ID INT,
   voto BOOL,
   PRIMARY KEY(account_ID, capitolo_ID),
   FOREIGN KEY(account_ID) REFERENCES Account(ID),
   FOREIGN KEY(capitolo_ID) REFERENCES Capitolo(ID)
);


INSERT INTO `Ruolo` (`nome`) VALUES ('Utente');
INSERT INTO `Ruolo` (`nome`) VALUES ('Moderatore');
INSERT INTO `Ruolo` (`nome`) VALUES ('Amministratore'); 