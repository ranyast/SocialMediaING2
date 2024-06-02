-- Table utilisateur
DROP TABLE IF EXISTS utilisateur;
CREATE TABLE IF NOT EXISTS utilisateur (
    id_user INT NOT NULL AUTO_INCREMENT,
    nom varchar(255) NOT NULL,
    prenom varchar(255) NOT NULL,
    date_naissance DATE NOT NULL,
    email varchar(100) NOT NULL UNIQUE,
    mot_de_passe varchar(255) NOT NULL,
    statut INT NOT NULL,
    photo_profil VARCHAR(191),
    description TEXT,
    experience TEXT,
    etudes INT,
    sexe INT,
    competences TEXT,
    formation TEXT,
    feeling TEXT,
    PRIMARY KEY (id_user),
    CONSTRAINT CHK_statut CHECK (statut IN (0, 1, 2)),
    CONSTRAINT CHK_etudes CHECK (etudes IN (0, 1, 2, 3, 4, 5, 6)),
    CONSTRAINT CHK_sexe CHECK (sexe IN (0, 1, 2))
    ) ENGINE=InnoDB;



-- Correction de la structure de la table messages
DROP TABLE IF EXISTS messages;
CREATE TABLE IF NOT EXISTS messages (
  id int NOT NULL AUTO_INCREMENT,
  sender varchar(255) NOT NULL,
  recipient varchar(255) NOT NULL,
  message text,
  timestamp timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX sender (sender(250)),
  INDEX recipient (recipient(250))
) ENGINE=InnoDB;

-- Table comments
DROP TABLE IF EXISTS comments;
CREATE TABLE IF NOT EXISTS comments (
    id_comments int NOT NULL AUTO_INCREMENT,
    id_post int NOT NULL,
    nom varchar(255) NOT NULL,
    prenom varchar(255) NOT NULL,
    comment text NOT NULL,
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_comments),
    INDEX id_post (id_post)
    ) ENGINE=InnoDB;

-- Table friends
DROP TABLE IF EXISTS friends;
CREATE TABLE IF NOT EXISTS friends (
    id_friends int NOT NULL AUTO_INCREMENT,
    user1 varchar(255) NOT NULL,
    user2 varchar(255) NOT NULL,
    status enum('pending','accepted','rejected') DEFAULT 'pending',
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_friends)
    ) ENGINE=InnoDB;

-- Table friend_requests
DROP TABLE IF EXISTS friend_requests;
CREATE TABLE IF NOT EXISTS friend_requests (
    id_friend_requests int NOT NULL AUTO_INCREMENT,
    sender varchar(255) NOT NULL,
    receiver varchar(255) NOT NULL,
    status enum('pending','accepted','rejected') DEFAULT 'pending',
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_friend_requests)
    ) ENGINE=InnoDB;

-- Table job_offers
DROP TABLE IF EXISTS job_offers;
CREATE TABLE IF NOT EXISTS job_offers (
    id_job_offers int NOT NULL AUTO_INCREMENT,
    id_post int NOT NULL,
    id_user int NOT NULL,
    nom varchar(255) NOT NULL,
    prenom varchar(255) NOT NULL,
    emploiNom varchar(255) NOT NULL,
    emploiPoste varchar(255) NOT NULL,
    emploiProfil text NOT NULL,
    emploiDescription text NOT NULL,
    location varchar(255) DEFAULT NULL,
    datetime datetime NOT NULL,
    media_path varchar(255) DEFAULT NULL,
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_job_offers),
    INDEX id_post (id_post)
    ) ENGINE=InnoDB;

-- Table likes
DROP TABLE IF EXISTS likes;
CREATE TABLE IF NOT EXISTS likes (
    id_like int UNSIGNED NOT NULL AUTO_INCREMENT,
    id_post int UNSIGNED NOT NULL,
    id_user int UNSIGNED NOT NULL,
    reg_date timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_like),
    INDEX id_post (id_post),
    INDEX id_user (id_user)
    ) ENGINE=InnoDB;

-- Table posts
DROP TABLE IF EXISTS posts;
CREATE TABLE IF NOT EXISTS posts (
    id_posts int NOT NULL AUTO_INCREMENT,
    id_user int NOT NULL,
    nom varchar(255) NOT NULL,
    prenom varchar(255) NOT NULL,
    content text NOT NULL,
    location varchar(255) DEFAULT NULL,
    datetime datetime NOT NULL,
    media_path varchar(255) DEFAULT NULL,
    created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    likes int DEFAULT '0',
    PRIMARY KEY (id_posts)
    ) ENGINE=InnoDB;

-- Table post_likes
DROP TABLE IF EXISTS post_likes;
CREATE TABLE IF NOT EXISTS post_likes (
    id_post_likes int NOT NULL AUTO_INCREMENT,
    id_user int DEFAULT NULL,
    id_post int DEFAULT NULL,
    PRIMARY KEY (id_post_likes),
    UNIQUE KEY id_user (id_user, id_post)
    ) ENGINE=InnoDB;

-- Table notifications
DROP TABLE IF EXISTS notifications;
CREATE TABLE IF NOT EXISTS notifications (
    id_notification INT NOT NULL AUTO_INCREMENT,
    id_user INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_notification),
    INDEX (id_user)
) ENGINE=InnoDB;

-- Correction de la structure de la table chats
DROP TABLE IF EXISTS chats;
CREATE TABLE IF NOT EXISTS chats (
  id_chat int NOT NULL AUTO_INCREMENT,
  id_user1 int NOT NULL,
  id_user2 int NOT NULL,
  PRIMARY KEY (id_chat),
  INDEX id_user1 (id_user1),
  INDEX user2_id (id_user2)
) ENGINE=InnoDB;

