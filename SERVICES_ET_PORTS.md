# Services et Ports - Projet Clairo

## 📋 Liste des Services Docker

Le projet utilise **9 conteneurs Docker** :

### 1. Application PHP (Symfony)
- **Conteneur** : `symfony_php`
- **Image** : `symfony_php`
- **Ports** : 
  - **9080** → Application web
  - **9004** → Xdebug
- **URL** : http://localhost:9080

### 2. Base de Données MySQL
- **Conteneur** : `symfony_mysql`
- **Image** : `symfony_mysql` (MariaDB 12.0.2)
- **Port** : **3307** → MySQL
- **Identifiants** :
  - User: `cezar`
  - Password: `surete*2023`
  - Database: `cezar`

### 3. phpMyAdmin
- **Conteneur** : `symfony_phpmyadmin`
- **Image** : `phpmyadmin:latest`
- **Port** : **9081** → Interface web
- **URL** : http://localhost:9081
- **Identifiants** : Mêmes que MySQL

### 4. MailHog (Serveur SMTP de test)
- **Conteneur** : `symfony_mailhog`
- **Image** : `mailhog/mailhog`
- **Ports** :
  - **8025** → Interface web
  - **1025** → Serveur SMTP
- **URL** : http://localhost:8025

### 5. Redis (Cache)
- **Conteneur** : `cleo-redis`
- **Image** : `redis:7-alpine`
- **Port** : **6380** → Redis
- **Commande** : `docker compose exec redis redis-cli`

### 6. RedisInsight (Interface Redis)
- **Conteneur** : `cleo-redisinsight`
- **Image** : `redis/redisinsight:latest`
- **Port** : **5541** → Interface web
- **URL** : http://localhost:5541

### 7. SonarQube (Analyse de code)
- **Conteneur** : `sonarqube-allin`
- **Image** : Custom (avec scanner intégré)
- **Port** : **9900** → Interface web
- **URL** : http://localhost:9900
- **Identifiants** :
  - User: `admin`
  - Password: `change_me_strong`

### 8. PostgreSQL (Base SonarQube)
- **Conteneur** : `sonar-db`
- **Image** : `postgres:15`
- **Port** : Interne uniquement
- **Database** : `sonarqube`

### 9. ClamAV (Antivirus)
- **Conteneur** : `clamav`
- **Image** : `clamav/clamav:latest`
- **Port** : **3310** → Service ClamAV
- **Utilisation** : Scan automatique des fichiers uploadés

---

## 🔗 Tableau Récapitulatif

| Service | Conteneur | Port(s) | URL | Identifiants |
|---------|-----------|---------|-----|--------------|
| **Application Symfony** | `symfony_php` | 9080, 9004 | http://localhost:9080 | À créer |
| **MySQL** | `symfony_mysql` | 3307 | - | cezar / surete*2023 |
| **phpMyAdmin** | `symfony_phpmyadmin` | 9081 | http://localhost:9081 | cezar / surete*2023 |
| **MailHog** | `symfony_mailhog` | 8025, 1025 | http://localhost:8025 | - |
| **Redis** | `cleo-redis` | 6380 | - | - |
| **RedisInsight** | `cleo-redisinsight` | 5541 | http://localhost:5541 | - |
| **SonarQube** | `sonarqube-allin` | 9900 | http://localhost:9900 | admin / change_me_strong |
| **PostgreSQL** | `sonar-db` | - | - | sonar / sonarpass |
| **ClamAV** | `clamav` | 3310 | - | - |

---

## 🔍 Vérifier les Services

### Statut des Conteneurs

```bash
docker compose ps
```

### Logs d'un Service

```bash
# Application PHP
docker compose logs -f php

# MySQL
docker compose logs -f mysql

# Tous les services
docker compose logs -f
```

### Accéder au Shell d'un Conteneur

```bash
# PHP
docker compose exec php bash

# MySQL
docker compose exec mysql bash

# Redis
docker compose exec redis sh
```

---

## 🛠️ Commandes de Gestion

### Démarrer les Services

```bash
docker compose up -d
```

### Arrêter les Services

```bash
docker compose down
```

### Redémarrer un Service

```bash
docker compose restart php
docker compose restart mysql
```

### Reconstruire les Images

```bash
docker compose build --no-cache
docker compose up -d
```

---

## 📊 Utilisation des Ressources

### Voir l'Utilisation

```bash
docker stats
```

### Nettoyer Docker

```bash
# Arrêter et supprimer les conteneurs
docker compose down

# Supprimer les volumes (⚠️ Supprime les données)
docker compose down -v

# Supprimer les images
docker compose down --rmi all
```

---

## 🔐 Sécurité

### Ports Exposés

Tous les ports listés ci-dessus sont exposés sur **localhost uniquement** et ne sont pas accessibles depuis l'extérieur par défaut.

### Mots de Passe en Production

⚠️ **Important** : Avant de déployer en production, changez TOUS les mots de passe dans `.env.local` :

- `MYSQL_PASSWORD`
- `MYSQL_ROOT_PASSWORD`
- `SONARQUBE_ADMIN_NEW`
- `STRIPE_API_KEY` (utilisez la clé de production)
- `APP_SECRET`
- `ENCRYPTION_KEY`

---

## 📝 Notes

### Ports Modifiés

Les ports ont été modifiés par rapport à la configuration standard pour éviter les conflits :

| Service | Port Standard | Port Utilisé |
|---------|---------------|--------------|
| Application | 8080 | **9080** |
| phpMyAdmin | 8081 | **9081** |
| MySQL | 3306 | **3307** |
| Redis | 6379 | **6380** |
| RedisInsight | 5540 | **5541** |

### Volumes Docker

Les données persistantes sont stockées dans des volumes Docker :

- `mysql_data` : Données MySQL
- `redis-data` : Données Redis
- `sonar-db-data` : Base de données SonarQube
- `sonar-data` : Données SonarQube
- `sonar-extensions` : Extensions SonarQube
- `sonar-logs` : Logs SonarQube
- `clamav_data` : Base de signatures ClamAV

---

**Dernière mise à jour** : 22 novembre 2025
