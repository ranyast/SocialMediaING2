CREATE TABLE utilisateur (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(200) NOT NULL,
    statut INT NOT NULL,
    photo_profil VARCHAR(191),
    description TEXT,
    experience TEXT,
    etudes INT,
    sexe INT,
    competences TEXT,
    formation TEXT,
    CONSTRAINT CHK_statut CHECK (statut IN (0, 1, 2)),
    CONSTRAINT CHK_etudes CHECK (etudes IN (0, 1, 2, 3, 4, 5, 6)),
    CONSTRAINT CHK_sexe CHECK (sexe IN (0, 1, 2))
);


CREATE TABLE amitie ( 
    id_amitie INT AUTO_INCREMENT PRIMARY KEY, 
    id_user1 INT NOT NULL, 
    id_user2 INT NOT NULL, 
    est_ami BOOLEAN NOT NULL, 
    CONSTRAINT FK_user1 FOREIGN KEY (id_user1) REFERENCES utilisateur(id_user), 
    CONSTRAINT FK_user2 FOREIGN KEY (id_user2) REFERENCES utilisateur(id_user) 

);