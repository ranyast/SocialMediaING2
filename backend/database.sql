CREATE TABLE utilisateur (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATETIME NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(200) NOT NULL
);

CREATE TABLE profil (
    id_profil INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    photo_profil VARCHAR(191),
    description TEXT,
    etudes INT,
    sexe INT,
    competences TEXT,
    FOREIGN KEY (id_user) REFERENCES utilisateur(id_user),
    CONSTRAINT CHK_etudes CHECK (etudes IN (0, 1, 2, 3, 4, 5, 6)),
    CONSTRAINT CHK_sexe CHECK (sexe IN (0, 1, 2))
);


