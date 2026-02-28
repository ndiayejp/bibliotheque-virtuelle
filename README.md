# Bibliothèque Virtuelle
Application web de gestion d'une bibliothèque — emprunt, retour et catalogue de livres.
Développée avec Symfony 6.4 (backend API REST) et Vue 3 (frontend SPA), en suivant les principes de l'Architecture Hexagonale (Ports & Adapters) et du Domain-Driven Design.

# Stack technique
**Backend** PHP 8.2, Symfony 6.4
**Base de données** MySQL 8.0 via XAMPP
**ORM** Doctrine DBAL (SQL direct)
**Authentification** JWT (LexikJWTAuthenticationBundle)
**Frontend** Vue 3, Pinia, Vue Router
**Style** Tailwind CSS, Lucide Icons
**Build** Vite 5

# Prérequis
XAMPP 8.2+ (PHP + MySQL)
Composer 2.x
Node.js 20 LTS + npm 10
## Installation
### Cloner le projet
git clone https://github.com/votre-compte/bibliotheque-virtuelle.git
cd bibliotheque-virtuelle
### Démarrer XAMPP
Ouvrir le XAMPP Control Panel et démarrer MySQL.
### Créer la base de données
Ouvrir phpMyAdmin et créer une base :
Nom : bibliotheque_db
Interclassement : utf8mb4_unicode_ci
### Configurer le backend
cd backend
composer install
cp .env.example .env.local
Éditer .env.local et renseigner la connexion MySQL :
####  Sans mot de passe (XAMPP par défaut)
DATABASE_URL="mysql://root:@127.0.0.1:3306/bibliotheque_db?serverVersion=8.0&charset=utf8mb4"
####  Avec mot de passe
DATABASE_URL="mysql://root:votre_mdp@127.0.0.1:3306/bibliotheque_db?serverVersion=8.0&charset=utf8mb4"
### Créer les tables et données de démo
php bin/console doctrine:migrations:sync-metadata-storage
php bin/console doctrine:migrations:migrate
### Générer les clés JWT
php bin/console lexik:jwt:generate-keypair
### Installer le frontend
cd ../frontend
npm install
## Utilisation
Démarrer l'application
Ouvrir deux terminaux :
### Terminal 1 — Backend
cd backend
php -S localhost:8000 -t public/
### Terminal 2 — Frontend
cd frontend
npm run dev
L'application est accessible sur http://localhost:5173.
### Compte de démonstration
Admin admin@bibliotheque.fr admin123
Utilisateur user@bibliotheque.fr user123
