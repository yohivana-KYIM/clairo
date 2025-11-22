# Guide de Démarrage - Projet Clairo

## 📋 Table des Matières

1. [Prérequis](#prérequis)
2. [Architecture du Projet](#architecture-du-projet)
3. [Services Docker](#services-docker)
4. [Démarrage Rapide](#démarrage-rapide)
5. [Accès aux Services](#accès-aux-services)
6. [Configuration](#configuration)
7. [Commandes Utiles](#commandes-utiles)
8. [Résolution des Problèmes](#résolution-des-problèmes)

---

## 🔧 Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **Docker** (version 20.10 ou supérieure)
- **Docker Compose** (version 2.0 ou supérieure)
- Au moins **8 GB de RAM** disponible pour Docker
- Ports disponibles : **9080**, **9081**, **3307**, **6380**, **8025**, **9900**, **5541**, **3310**

### Vérification des Prérequis

```bash
# Vérifier Docker
docker --version

# Vérifier Docker Compose
docker compose version

# Vérifier l'espace disque disponible
df -h
```

---

## 🏗️ Architecture du Projet

Le projet **Clairo** est une application Symfony 7.3 avec les composants suivants :

### Technologies Principales

- **Backend** : Symfony 7.3 (PHP 8.3)
- **Base de données** : MariaDB 12.0.2
- **Cache** : Redis 7
- **Serveur Web** : PHP Built-in Server (développement)
- **Génération PDF** : wkhtmltopdf, DomPDF, mPDF
- **Authentification** : 2FA (Google Authenticator, Email)
- **Paiements** : Stripe
- **Antivirus** : ClamAV

### Structure des Dossiers

```
clairo-fix-check_files_add_workflow_and_dockerfile/
├── bin/                    # Scripts exécutables Symfony
├── config/                 # Configuration Symfony
├── iac/                    # Infrastructure as Code (Dockerfiles)
│   ├── php/               # Configuration PHP et Dockerfile
│   ├── mysql/             # Configuration MySQL et Dockerfile
│   ├── sonarqube/         # Configuration SonarQube
│   └── scripts/           # Scripts d'initialisation
├── migrations/             # Migrations de base de données
├── public/                 # Point d'entrée web
├── src/                    # Code source de l'application
│   ├── AdminBundle/       # Bundle d'administration
│   ├── Controller/        # Contrôleurs
│   ├── Entity/            # Entités Doctrine
│   ├── Form/              # Formulaires
│   ├── MultiStepBundle/   # Workflows multi-étapes
│   ├── Repository/        # Repositories Doctrine
│   ├── Security/          # Sécurité et authentification
│   └── Service/           # Services métier
├── templates/              # Templates Twig
├── tests/                  # Tests unitaires et fonctionnels
├── docker-compose.yml      # Configuration Docker Compose
└── .env.local             # Variables d'environnement locales
```

---

## 🐳 Services Docker

Le projet utilise **9 conteneurs Docker** :

| Service | Conteneur | Port(s) | Description |
|---------|-----------|---------|-------------|
| **PHP** | `symfony_php` | 9080, 9004 | Application Symfony avec PHP 8.3-FPM |
| **MySQL** | `symfony_mysql` | 3307 | Base de données MariaDB |
| **phpMyAdmin** | `symfony_phpmyadmin` | 9081 | Interface web pour MySQL |
| **MailHog** | `symfony_mailhog` | 8025, 1025 | Serveur SMTP de test |
| **Redis** | `cleo-redis` | 6380 | Cache et sessions |
| **RedisInsight** | `cleo-redisinsight` | 5541 | Interface web pour Redis |
| **SonarQube** | `sonarqube-allin` | 9900 | Analyse de qualité de code |
| **PostgreSQL** | `sonar-db` | - | Base de données pour SonarQube |
| **ClamAV** | `clamav` | 3310 | Antivirus pour scan de fichiers |

---

## 🚀 Démarrage Rapide

### Étape 1 : Cloner et Accéder au Projet

```bash
cd /home/ivana/Téléchargements/clairo-fix-check_files_add_workflow_and_dockerfile
```

### Étape 2 : Vérifier le Fichier .env.local

Le fichier `.env.local` a déjà été créé avec la configuration appropriée pour Docker.

### Étape 3 : Construire et Démarrer les Conteneurs

```bash
# Construire les images Docker (première fois uniquement)
docker compose build

# Démarrer tous les services en arrière-plan
docker compose up -d
```

**Temps estimé** : 5-10 minutes pour la première construction.

### Étape 4 : Vérifier le Statut des Conteneurs

```bash
docker compose ps
```

Tous les conteneurs doivent afficher le statut **Up** ou **Healthy**.

### Étape 5 : Exécuter les Migrations de Base de Données

```bash
# Créer la base de données si elle n'existe pas
docker compose exec php php bin/console doctrine:database:create --if-not-exists

# Exécuter les migrations
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

### Étape 6 : Charger les Fixtures (Optionnel)

```bash
# Charger des données de test
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

### Étape 7 : Vider le Cache

```bash
docker compose exec php php bin/console cache:clear
```

---

## 🌐 Accès aux Services

Une fois tous les conteneurs démarrés, vous pouvez accéder aux services suivants :

| Service | URL | Identifiants |
|---------|-----|--------------|
| **Application Symfony** | http://localhost:9080 | Voir fixtures ou créer un compte |
| **phpMyAdmin** | http://localhost:9081 | User: `cezar`<br>Password: `surete*2023` |
| **MailHog (Interface)** | http://localhost:8025 | Aucun |
| **RedisInsight** | http://localhost:5541 | Aucun |
| **SonarQube** | http://localhost:9900 | Admin: `admin`<br>Password: `change_me_strong` |

---

## ⚙️ Configuration

### Variables d'Environnement Importantes

Le fichier `.env.local` contient toutes les variables nécessaires :

```bash
# Application
APP_ENV=dev
APP_DEBUG=1
WEBSITE_DOMAIN=http://localhost:9080

# Base de données
DATABASE_URL=mysql://cezar:surete*2023@symfony_mysql:3306/cezar

# Redis
REDIS_URL=redis://redis:6379

# Email (MailHog pour le développement)
MAILER_DSN=smtp://mailhog:1025

# Stripe (Mode Test)
STRIPE_API_KEY=sk_test_...
```

### Modifier la Configuration

1. Éditez le fichier `.env.local`
2. Redémarrez les conteneurs :
   ```bash
   docker compose restart
   ```

---

## 🛠️ Commandes Utiles

### Gestion des Conteneurs

```bash
# Démarrer tous les services
docker compose up -d

# Arrêter tous les services
docker compose down

# Redémarrer un service spécifique
docker compose restart php

# Voir les logs d'un service
docker compose logs -f php

# Voir les logs de tous les services
docker compose logs -f

# Accéder au shell d'un conteneur
docker compose exec php bash
```

### Commandes Symfony

```bash
# Vider le cache
docker compose exec php php bin/console cache:clear

# Lister les routes
docker compose exec php php bin/console debug:router

# Créer un utilisateur
docker compose exec php php bin/console app:create-user

# Vérifier la configuration
docker compose exec php php bin/console about

# Lancer les tests
docker compose exec php php bin/phpunit
```

### Base de Données

```bash
# Créer la base de données
docker compose exec php php bin/console doctrine:database:create

# Exécuter les migrations
docker compose exec php php bin/console doctrine:migrations:migrate

# Créer une nouvelle migration
docker compose exec php php bin/console make:migration

# Charger les fixtures
docker compose exec php php bin/console doctrine:fixtures:load

# Accéder à MySQL via CLI
docker compose exec mysql mysql -u cezar -p cezar
```

### Redis

```bash
# Accéder au CLI Redis
docker compose exec redis redis-cli

# Vider le cache Redis
docker compose exec redis redis-cli FLUSHALL
```

---

## 🔍 Résolution des Problèmes

### Problème : Les ports sont déjà utilisés

**Solution** : Modifiez les ports dans `docker-compose.yml`

```yaml
ports:
  - "NOUVEAU_PORT:PORT_INTERNE"
```

Puis redémarrez :
```bash
docker compose down
docker compose up -d
```

### Problème : Erreur de connexion à la base de données

**Vérifications** :

1. Le conteneur MySQL est-il démarré ?
   ```bash
   docker compose ps mysql
   ```

2. Vérifiez les logs MySQL :
   ```bash
   docker compose logs mysql
   ```

3. Testez la connexion :
   ```bash
   docker compose exec mysql mysql -u cezar -psurete*2023 -e "SHOW DATABASES;"
   ```

### Problème : Erreur APCu ou cache

**Solution** :

```bash
# Vider le cache Symfony
docker compose exec php php bin/console cache:clear

# Vider le cache Redis
docker compose exec redis redis-cli FLUSHALL

# Redémarrer PHP
docker compose restart php
```

### Problème : Composer out of memory

**Solution** : Augmentez la mémoire disponible pour Docker dans les paramètres Docker Desktop.

### Problème : Les migrations échouent

**Solution** :

```bash
# Supprimer la base de données et la recréer
docker compose exec php php bin/console doctrine:database:drop --force
docker compose exec php php bin/console doctrine:database:create
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

### Problème : Le conteneur PHP ne démarre pas

**Vérifications** :

```bash
# Voir les logs détaillés
docker compose logs php

# Reconstruire l'image
docker compose build --no-cache php
docker compose up -d php
```

### Nettoyer Complètement Docker

Si vous rencontrez des problèmes persistants :

```bash
# Arrêter et supprimer tous les conteneurs
docker compose down -v

# Supprimer les images
docker compose down --rmi all

# Reconstruire depuis zéro
docker compose build --no-cache
docker compose up -d
```

---

## 📊 Monitoring et Logs

### Voir les Logs en Temps Réel

```bash
# Tous les services
docker compose logs -f

# Service spécifique
docker compose logs -f php
docker compose logs -f mysql
```

### Vérifier l'Utilisation des Ressources

```bash
docker stats
```

---

## 🔐 Sécurité

### Changement des Mots de Passe en Production

Avant de déployer en production, changez **TOUS** les mots de passe et clés API dans `.env.local` :

- `APP_SECRET`
- `MYSQL_PASSWORD`
- `MYSQL_ROOT_PASSWORD`
- `STRIPE_API_KEY` (utilisez la clé de production)
- `ENCRYPTION_KEY`
- `SONARQUBE_ADMIN_NEW`

### Désactiver le Mode Debug

```bash
APP_ENV=prod
APP_DEBUG=0
```

---

## 📚 Ressources Supplémentaires

- [Documentation Symfony](https://symfony.com/doc/current/index.html)
- [Documentation Docker](https://docs.docker.com/)
- [Documentation Doctrine](https://www.doctrine-project.org/)
- [Documentation Stripe](https://stripe.com/docs)

---

## 🆘 Support

Pour toute question ou problème :

1. Consultez les logs : `docker compose logs -f`
2. Vérifiez la documentation Symfony
3. Consultez le fichier `DOCUMENTATION_FONCTIONNALITES.md` pour plus de détails sur les fonctionnalités

---

**Dernière mise à jour** : 22 novembre 2025
