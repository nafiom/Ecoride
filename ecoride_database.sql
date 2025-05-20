
-- Création de la table utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('visiteur', 'utilisateur', 'chauffeur', 'employe', 'admin') DEFAULT 'visiteur',
    credits INT DEFAULT 20,
    photo VARCHAR(255) DEFAULT NULL,
    suspendu TINYINT(1) NOT NULL DEFAULT 0,
    token VARCHAR(255) DEFAULT NULL,
    valide TINYINT(1) DEFAULT 0
);

-- Création de la table voitures
CREATE TABLE voitures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    marque VARCHAR(100),
    modele VARCHAR(100),
    immatriculation VARCHAR(100),
    couleur VARCHAR(50),
    date_immatriculation DATE,
    places INT NOT NULL DEFAULT 1,
    ecologique BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id)
);

-- Création de la table covoiturages
CREATE TABLE covoiturages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chauffeur_id INT NOT NULL,
    depart VARCHAR(100) NOT NULL,
    arrivee VARCHAR(100) NOT NULL,
    date_depart DATETIME NOT NULL,
    date_arrivee DATETIME NOT NULL,
    prix FLOAT NOT NULL,
    places INT NOT NULL,
    voiture_id INT,
    ecologique BOOLEAN DEFAULT FALSE,
    statut VARCHAR(20) NOT NULL DEFAULT 'en_attente',
    FOREIGN KEY (chauffeur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (voiture_id) REFERENCES voitures(id)
);

-- Création de la table participants
CREATE TABLE participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    covoiturage_id INT NOT NULL,
    date_participation DATETIME DEFAULT CURRENT_TIMESTAMP,
    etat ENUM('en_attente', 'confirme', 'annule') DEFAULT 'en_attente',
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (covoiturage_id) REFERENCES covoiturages(id)
);

-- Création de la table avis
CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT NOT NULL,
    passager_id INT NOT NULL,
    covoiturage_id INT NOT NULL,
    note INT CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    valide BOOLEAN DEFAULT FALSE,
    signalement BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (conducteur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (passager_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (covoiturage_id) REFERENCES covoiturages(id)
);

-- Création de la table préférences conducteur
CREATE TABLE preferences_conducteur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    type_pref VARCHAR(100), 
    valeur BOOLEAN,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Création de la table crédits plateforme
CREATE TABLE credits_plateforme (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    credits_gagnes INT NOT NULL
);

-- Création de la table réservations
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    covoiturage_id INT NOT NULL,
    statut ENUM('en_attente', 'confirme', 'annule') DEFAULT 'en_attente',
    notification_vue BOOLEAN DEFAULT 0,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (covoiturage_id) REFERENCES covoiturages(id)
);

-- Création de la table demandes trajets
CREATE TABLE demandes_trajets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    depart VARCHAR(255) NOT NULL,
    arrivee VARCHAR(255) NOT NULL,
    date_depart DATETIME NOT NULL,
    statut ENUM('en_attente', 'accepte', 'refuse') DEFAULT 'en_attente',
    chauffeur_id INT DEFAULT NULL,
    prix_propose INT DEFAULT NULL,
    notification_vue BOOLEAN DEFAULT 0,
    covoiturage_id INT DEFAULT NULL,
    date_arrivee DATETIME NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (chauffeur_id) REFERENCES utilisateurs(id)
);

-- Création de la table notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    type VARCHAR(50),
    message TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);




-- Insertion d'un employé de test
INSERT INTO utilisateurs (pseudo, email, password, role, valide) VALUES
('employe1', 'employe1@example.com', '$2y$12$AEp/bQNwlcPtfzXipflhNehbAt5BsEdqdXoUDHntLptSIxv7gojWK', 'employe', 1);
