CREATE TABLE mot(
   id_mot INT GENERATED BY DEFAULT AS IDENTITY,
   nom_mot VARCHAR(50),
   longueur_mot SMALLINT NOT NULL,
   nombre_voyelle SMALLINT NOT NULL,
   nombre_caracteres_speciaux SMALLINT NOT NULL,
   PRIMARY KEY(id_mot),
   UNIQUE(nom_mot)
);

CREATE TABLE utilisateur(
   id_utilisateur INT GENERATED BY DEFAULT AS IDENTITY,
   email_utilisateur VARCHAR(100) NOT NULL,
   login_utilisateur VARCHAR(50) NOT NULL,
   password_utilisateur VARCHAR(255) NOT NULL,
   prenom_utilisateur VARCHAR(50) NOT NULL,
   nom_utilisateur VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_utilisateur),
   UNIQUE(email_utilisateur),
   UNIQUE(login_utilisateur),
   UNIQUE(prenom_utilisateur)
);

CREATE TABLE partie(
   id_partie INT GENERATED BY DEFAULT AS IDENTITY,
   score_partie INT NOT NULL,
   date_depart_partie TIMESTAMP NOT NULL,
   date_fin_partie TIMESTAMP,
   gagne_partie SMALLINT,
   id_utilisateur_partie INT,
   id_mot_partie INT NOT NULL,
   PRIMARY KEY(id_partie),
   FOREIGN KEY(id_utilisateur_partie) REFERENCES utilisateur(id_utilisateur),
   FOREIGN KEY(id_mot_partie) REFERENCES mot(id_mot)
);
