-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `utilisateur`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `utilisateur` ;

CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(45) NOT NULL,
  `prenom` VARCHAR(45) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `mot_de_passe` VARCHAR(45) NOT NULL,
  `date_naissance` DATE NOT NULL,
  `adresse` VARCHAR(45) NOT NULL,
  `ville` VARCHAR(45) NOT NULL,
  `code_postal` VARCHAR(45) NOT NULL,
  `telephone` VARCHAR(45) NOT NULL,
  `photo` VARCHAR(140) NOT NULL,
  `description` LONGTEXT NULL DEFAULT NULL,
  `terminale` TINYINT(1) NULL DEFAULT NULL,
  `bac_plus_1` TINYINT(1) NULL DEFAULT NULL,
  `bac_plus_2` TINYINT(1) NULL DEFAULT NULL,
  `bac_plus_3` TINYINT(1) NULL DEFAULT NULL,
  `bac_plus_4` TINYINT(1) NULL DEFAULT NULL,
  `bac_plus_5` TINYINT(1) NULL DEFAULT NULL,
  `autre` TINYINT(1) NULL DEFAULT NULL,
  `admin` TINYINT(1) NULL DEFAULT NULL,
  `prof` TINYINT(1) NULL DEFAULT NULL,
  `eleve` TINYINT(1) NULL DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `ami`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ami` ;

CREATE TABLE IF NOT EXISTS `ami` (
  `id_ami` INT NOT NULL AUTO_INCREMENT,
  `id_utilisateur1` INT NOT NULL,
  `id_utilisateur2` INT NOT NULL,
  PRIMARY KEY (`id_ami`),
  INDEX `fk_ami_utilisateur1` (`id_utilisateur1` ASC) VISIBLE,
  INDEX `fk_ami_utilisateur2` (`id_utilisateur2` ASC) VISIBLE,
  CONSTRAINT `fk_ami_utilisateur1`
    FOREIGN KEY (`id_utilisateur1`)
    REFERENCES `utilisateur` (`id_utilisateur`),
  CONSTRAINT `fk_ami_utilisateur2`
    FOREIGN KEY (`id_utilisateur2`)
    REFERENCES `utilisateur` (`id_utilisateur`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `emploi`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `emploi` ;

CREATE TABLE IF NOT EXISTS `emploi` (
  `id_emploi` INT NOT NULL AUTO_INCREMENT,
  `titre` VARCHAR(45) NOT NULL,
  `description` TEXT NOT NULL,
  `entreprise` VARCHAR(45) NOT NULL,
  `lieu` VARCHAR(45) NOT NULL,
  `date_publication` DATETIME NOT NULL,
  `CDI` TINYINT(1) NULL,
  `CDD` TINYINT(1) NULL,
  `alternance` TINYINT(1) NULL,
  `stage` TINYINT(1) NULL,
  PRIMARY KEY (`id_emploi`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `candidature`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `candidature` ;

CREATE TABLE IF NOT EXISTS `candidature` (
  `id_candidature` INT NOT NULL AUTO_INCREMENT,
  `id_utilisateur` INT NOT NULL,
  `id_emploi` INT NOT NULL,
  `date` DATETIME NOT NULL,
  PRIMARY KEY (`id_candidature`),
  INDEX `fk_candidature_utilisateur` (`id_utilisateur` ASC) VISIBLE,
  INDEX `fk_candidature_emploi` (`id_emploi` ASC) VISIBLE,
  CONSTRAINT `fk_candidature_emploi`
    FOREIGN KEY (`id_emploi`)
    REFERENCES `emploi` (`id_emploi`),
  CONSTRAINT `fk_candidature_utilisateur`
    FOREIGN KEY (`id_utilisateur`)
    REFERENCES `utilisateur` (`id_utilisateur`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `evenement`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `evenement` ;

CREATE TABLE IF NOT EXISTS `evenement` (
  `id_evenement` INT NOT NULL AUTO_INCREMENT,
  `titre` VARCHAR(45) NOT NULL,
  `description` LONGTEXT NOT NULL,
  `date` DATE NOT NULL,
  PRIMARY KEY (`id_evenement`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `message`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `message` ;

CREATE TABLE IF NOT EXISTS `message` (
  `id_message` INT NOT NULL AUTO_INCREMENT,
  `id_chatroom` INT NOT NULL,
  `id_utilisateur_expediteur` INT NOT NULL,
  `id_utilisateur_destinataire` INT NOT NULL,
  `contenu` TEXT NOT NULL,
  `envoi` DATETIME NOT NULL,
  PRIMARY KEY (`id_message`),
  INDEX `fk_message_chatroom` (`id_chatroom` ASC) VISIBLE,
  INDEX `fk_message_utilisateur_expediteur` (`id_utilisateur_expediteur` ASC) VISIBLE,
  INDEX `fk_message_utilisateur_destinataire` (`id_utilisateur_destinataire` ASC) VISIBLE,
  CONSTRAINT `fk_message_utilisateur_destinataire`
    FOREIGN KEY (`id_utilisateur_destinataire`)
    REFERENCES `utilisateur` (`id_utilisateur`),
  CONSTRAINT `fk_message_utilisateur_expediteur`
    FOREIGN KEY (`id_utilisateur_expediteur`)
    REFERENCES `utilisateur` (`id_utilisateur`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `notifications`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `notifications` ;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id_notification` INT NOT NULL AUTO_INCREMENT,
  `id_utilisateur` INT NOT NULL,
  `emploi` TINYINT(1) NOT NULL,
  `evenement` TINYINT(1) NOT NULL,
  `message` TINYINT(1) NOT NULL,
  `titre` VARCHAR(45) NOT NULL,
  `description` LONGTEXT NOT NULL,
  `date` DATE NOT NULL,
  `id_evenement` INT NOT NULL,
  `id_emploi` INT NOT NULL,
  `id_message` INT NOT NULL,
  PRIMARY KEY (`id_notification`),
  INDEX `fk_notifications_utilisateur` (`id_utilisateur` ASC) VISIBLE,
  INDEX `fk_notifications_evenement` (`id_evenement` ASC) VISIBLE,
  INDEX `fk_notifications_emploi` (`id_emploi` ASC) VISIBLE,
  INDEX `fk_notifications_message` (`id_message` ASC) VISIBLE,
  CONSTRAINT `fk_notifications_emploi`
    FOREIGN KEY (`id_emploi`)
    REFERENCES `emploi` (`id_emploi`),
  CONSTRAINT `fk_notifications_evenement`
    FOREIGN KEY (`id_evenement`)
    REFERENCES `evenement` (`id_evenement`),
  CONSTRAINT `fk_notifications_message`
    FOREIGN KEY (`id_message`)
    REFERENCES `message` (`id_message`),
  CONSTRAINT `fk_notifications_utilisateur`
    FOREIGN KEY (`id_utilisateur`)
    REFERENCES `utilisateur` (`id_utilisateur`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;


DELIMITER //

CREATE TRIGGER message_ami_check BEFORE INSERT ON mydb.message
FOR EACH ROW
BEGIN
    DECLARE ami_count INT;

    -- Vérifier si une relation ami existe entre l'expéditeur et le destinataire
    SELECT COUNT(*) INTO ami_count
    FROM mydb.ami
    WHERE (mydb.ami.id_utilisateur1 = NEW.id_utilisateur_expediteur AND mydb.ami.id_utilisateur2 = NEW.id_utilisateur_destinataire)
        OR (mydb.ami.id_utilisateur1 = NEW.id_utilisateur_destinataire AND mydb.ami.id_utilisateur2 = NEW.id_utilisateur_expediteur);

    IF ami_count = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La relation ami n\'existe pas entre l\'expéditeur et le destinataire.';
    END IF;
END;//

DELIMITER ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
