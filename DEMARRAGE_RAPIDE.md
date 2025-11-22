# Projet Clairo - Démarrage Rapide 🚀

Application Symfony 7.3 de gestion administrative avec Docker.

## ⚡ Démarrage en 3 Étapes

### 1. Démarrer les Conteneurs

```bash
docker compose up -d
```

### 2. Initialiser la Base de Données

```bash
# Créer la base de données
docker compose exec php php bin/console doctrine:database:create --if-not-exists

# Exécuter les migrations
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

# (Optionnel) Charger des données de test
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

### 3. Accéder à l'Application

Ouvrez votre navigateur : **http://localhost:9080**

---

## 🌐 Services Disponibles

| Service | URL | Identifiants |
|---------|-----|--------------|
| **Application** | http://localhost:9080 | À créer |
| **phpMyAdmin** | http://localhost:9081 | User: `cezar` / Pass: `surete*2023` |
| **MailHog** | http://localhost:8025 | - |
| **RedisInsight** | http://localhost:5541 | - |
| **SonarQube** | http://localhost:9900 | Admin: `admin` / Pass: `change_me_strong` |

---

## 📚 Documentation Complète

- **[Guide de Démarrage](GUIDE_DEMARRAGE.md)** - Instructions détaillées
- **[Documentation des Fonctionnalités](DOCUMENTATION_FONCTIONNALITES.md)** - Toutes les fonctionnalités

---

## 🛠️ Commandes Utiles

```bash
# Voir les logs
docker compose logs -f php

# Vider le cache
docker compose exec php php bin/console cache:clear

# Arrêter les services
docker compose down
```

---

## 🔧 Technologies

- Symfony 7.3 (PHP 8.3)
- MariaDB 12.0.2
- Redis 7
- Stripe, 2FA, PDF, QR Codes
- ClamAV, SonarQube

---

**Besoin d'aide ?** Consultez [GUIDE_DEMARRAGE.md](GUIDE_DEMARRAGE.md)
