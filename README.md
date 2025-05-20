# Ecoride
Ecoride projet 
DESCRIPTION DU PROJET ECORIDE
Charte Graphique ECORIDE

Couleurs principales :
- Vert foncé : #2e7d32
- Vert clair : #a5d6a7
- Blanc : #ffffff
- Gris clair : #f4fff6

Typographie :
- Police principale : Segoe UI, sans-serif
- Style des boutons : texte en gras, boutons arrondis

Identité visuelle :
- Thème écologique, moderne, épuré
- Icônes utilisées : RemixIcon
- Transitions en JavaScript pour les interactions (hover, clic)

Maquettes utilisées :
- Aucune maquette Figma, mais une organisation manuelle dans le fichier (note project layout) avec les pages listées et leur enchaînement.

Documentation Technique ECORIDE

1. Choix technologiques :
- Langages : PHP 8 (backend), HTML/CSS/JS (frontend)
- Base de données : MySQL
- Hébergement : o2switch (avec certificat SSL Let's Encrypt)
- API : Google Maps JavaScript API pour calcul de trajet, suggestion d’adresses, et carte géographique.
- Php mailer : envoi de courriels automatiques faits avec un template HTML.

2. Configuration de l’environnement :
- Utilisation d’un fichier .env pour stocker les informations sensibles et .htaccess pour une sécurité supplémentaire.
- Connexion PDO sécurisée à la base.
- Structure MVC légère : pages, process, config, utils.

3. Tables créées pour la base de données :
- Utilisateurs, voitures, covoiturages, participants, avis, préférences_conducteur, notifications, réservations, demandes_trajets, credits_plateforme.

4. Diagrammes :
- Diagramme d’utilisation : Utilisateurs peuvent réserver, proposer et gérer des trajets.
- Diagramme de séquence : Authentification, réservation, validation, et avis.

5. Déploiement :
- Hébergeur : O2switch
- Dépôt du code via FTP dans `public_html`.
- Importation de la base via phpMyAdmin.
- Configuration du fichier `.env` avec les identifiants MySQL, clés API, mots de passe et username de l’adresse mail.
- Génération du certificat SSL depuis le cPanel (Let's Encrypt) = HTTPS

Gestion de Projet ECORIDE

Méthodologie :
- Organisation manuelle du projet à partir du cahier des charges.
- Listage des fonctionnalités en colonnes (To do, En cours, Terminé).
- Suivi manuel des tâches dans un fichier PDF (Note project layout.pdf).
- Découpage par US (User Stories) validées selon les blocs métier de l’ECF.

Livrables :
- Fichier SQL de la base de données complet.
- Code PHP, JS, CSS en dépôt GitHub.
- Documentation technique et utilisateur.
- PDF Note Project.
- Déploiement réel : https://covoiturageco.fr/

Technologies :
- Front : HTML5, CSS3 (responsive), JavaScript
- Back : PHP 8, MySQL
- Sécurité : fichier .env, protection SQL, sessions, redirections
- Hébergement : o2switch

Manuel Utilisateur ECORIDE

Bienvenue sur ECORIDE, une plateforme de covoiturage durable et solidaire.
Voici comment utiliser l’application selon votre profil.

1. VISITEUR :
- Accéder à la page d’accueil : https://covoiturageco.fr/
- Rechercher un trajet via le formulaire de recherche.
- Visualiser les trajets disponibles et voir les détails.
- Afin de pouvoir réserver des courses, l’utilisateur doit s’inscrire à la plateforme.

2. UTILISATEUR :
- Créer un compte via la page Inscription.
- Activer son compte via l’email automatique reçu.
- Se connecter à son espace utilisateur.
- Réserver un trajet (si des places sont disponibles), sinon créer des demandes de trajet.
- Visualiser l’historique de trajets et laisser un avis.
- Supprimer son compte.

3. CHAUFFEUR :
- Activer ou désactiver son statut dans l’espace utilisateur.
- Ajouter son véhicule (modèle, immatriculation, écologie, etc).
- Définir ses préférences de conduite.
- Accepter les demandes de covoiturages proposées ou proposer un trajet (lieu, date, heure, nombre de places).
- Gérer ses réservations (accepter ou refuser).
- Démarrer et terminer une course.
- Gérer son profil, photo et préférences.
- Supprimer son compte.

4. EMPLOYÉ :
- Se connecter avec les identifiants fournis.
- Accéder à l’espace employé.
- Gérer les signalements et les avis.

5. ADMINISTRATEUR :
- Suspendre ou réactiver les comptes des utilisateurs.
- Gérer les crédits de la plateforme.
- Gérer la sécurité et modération globale.

