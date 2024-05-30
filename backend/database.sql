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

-- Table amis
CREATE TABLE IF NOT EXISTS amis (
    id_amis INT AUTO_INCREMENT PRIMARY KEY,
    id_user1 INT NOT NULL,
    id_user2 INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user1) REFERENCES utilisateur(id_user),
    FOREIGN KEY (id_user2) REFERENCES utilisateur(id_user),
    CONSTRAINT CHK_statut_amis CHECK (statut IN ('actif', 'inactif'))
) ENGINE=InnoDB;

-- Table requetes_amis
CREATE TABLE IF NOT EXISTS requetes_amis (
    id_requete INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES utilisateur(id_user),
    FOREIGN KEY (receiver_id) REFERENCES utilisateur(id_user),
    CONSTRAINT CHK_statut_requete CHECK (status IN ('pending', 'accepted', 'rejected'))
) ENGINE=InnoDB;

-- Table chats
CREATE TABLE IF NOT EXISTS chats (
    id_chat INT AUTO_INCREMENT PRIMARY KEY,
    id_user1 INT NOT NULL,
    id_user2 INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user1) REFERENCES utilisateur(id_user),
    FOREIGN KEY (id_user2) REFERENCES utilisateur(id_user)
) ENGINE=InnoDB;

-- Table commentaires
CREATE TABLE IF NOT EXISTS commentaires (
    id_commentaire INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_user INT NOT NULL,
    commentaire TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_post) REFERENCES posts(id_post),
    FOREIGN KEY (id_user) REFERENCES utilisateur(id_user)
) ENGINE=InnoDB;

-- Table likes
CREATE TABLE IF NOT EXISTS likes (
    id_like INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_user INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_post) REFERENCES posts(id_post),
    FOREIGN KEY (id_user) REFERENCES utilisateur(id_user)
) ENGINE=InnoDB;

-- Table messages
CREATE TABLE IF NOT EXISTS messages (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES utilisateur(id_user),
    FOREIGN KEY (receiver_id) REFERENCES utilisateur(id_user)
) ENGINE=InnoDB;

-- Table post_likes
CREATE TABLE IF NOT EXISTS post_likes (
    id_like INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_user INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_post) REFERENCES posts(id_post),
    FOREIGN KEY (id_user) REFERENCES utilisateur(id_user),
    UNIQUE (id_post, id_user)
) ENGINE=InnoDB;
