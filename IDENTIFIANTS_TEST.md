# Identifiants de Test - Projet Clairo

## 🔐 Compte Utilisateur de Test

Un compte utilisateur a été créé pour tester l'application :

### Identifiants de Connexion

- **Email** : `test@example.com`
- **Mot de passe** : `test123`
- **Statut** : Compte vérifié ✅
- **Entreprise** : Entreprise Test (SIREN: 123456789)

> **Note** : L'utilisateur est lié à une entreprise de test. Si vous voyez "Entreprise introuvable", c'est que l'utilisateur n'est pas lié à une entreprise.

---

## 🌐 Accès à l'Application

### Page de Connexion

URL : **http://localhost:9080/login**

### Étapes pour se Connecter

1. Ouvrez votre navigateur
2. Accédez à http://localhost:9080
3. Vous serez redirigé vers la page de connexion
4. Entrez les identifiants :
   - Email : `test@example.com`
   - Mot de passe : `test123`
5. Cliquez sur "Se connecter"

---

## ⚠️ Note sur l'Authentification 2FA

Le compte de test a été créé **sans 2FA activé** pour faciliter les tests. 

Si l'application demande une authentification à deux facteurs :

### Option 1 : Désactiver temporairement le 2FA (pour les tests)

Modifiez la configuration de sécurité dans `config/packages/security.yaml` si nécessaire.

### Option 2 : Configurer le 2FA

1. Connectez-vous avec le compte test
2. Allez dans **Paramètres** > **Sécurité**
3. Activez Google Authenticator ou Email 2FA
4. Suivez les instructions

---

## 🗄️ Base de Données

### Vérifier les Données

Vous pouvez vérifier les données via **phpMyAdmin** :

- **URL** : http://localhost:9081
- **Serveur** : `symfony_mysql`
- **Utilisateur** : `cezar`
- **Mot de passe** : `surete*2023`
- **Base de données** : `cezar`

### Requête SQL pour voir l'utilisateur

```sql
SELECT id, email, is_verified, created_at, roles 
FROM user 
WHERE email = 'test@example.com';
```

---

## 📊 État de la Base de Données

### Migrations Exécutées

✅ **74 migrations** ont été exécutées avec succès
✅ **258 requêtes SQL** exécutées
✅ Base de données complètement initialisée

### Tables Principales Créées

- `user` - Utilisateurs
- `demande_titre_circulation` - Demandes de titres de circulation
- `demande_titre_vehicule` - Demandes de titres de véhicule
- `entreprise` - Entreprises
- `document_personnel` - Documents personnels
- `document_professionnel` - Documents professionnels
- `message` - Messages
- `intervention` - Interventions
- `order` - Commandes
- `messenger_messages` - File de messages Symfony
- Et bien d'autres...

---

## 🧪 Créer d'Autres Utilisateurs

### Via SQL (phpMyAdmin ou ligne de commande)

1. Générer un mot de passe hashé :

```bash
docker compose exec php php bin/console security:hash-password "votre_mot_de_passe"
```

2. Insérer l'utilisateur dans la base de données :

```sql
INSERT INTO user (
    email, 
    roles, 
    password, 
    is_verified, 
    created_at, 
    backup_codes, 
    password_history, 
    is_referent_verified, 
    trusted_version
) VALUES (
    'nouveau@example.com',
    '["ROLE_USER"]',
    '$2y$13$...',  -- Le hash généré à l'étape 1
    1,
    NOW(),
    '[]',
    '[]',
    0,
    0
);
```

### Via l'Interface d'Inscription

Si l'inscription est activée, vous pouvez créer un compte via :

**URL** : http://localhost:9080/register

---

## 👨‍💼 Créer un Utilisateur Admin

Pour créer un administrateur, utilisez le rôle `ROLE_ADMIN` :

```sql
INSERT INTO user (
    email, 
    roles, 
    password, 
    is_verified, 
    created_at, 
    backup_codes, 
    password_history, 
    is_referent_verified, 
    trusted_version
) VALUES (
    'admin@example.com',
    '["ROLE_ADMIN", "ROLE_USER"]',
    '$2y$13$0IkfJKiRInJ5zdT5oYCHguDwdJa6Zf0tIV6/bNPCiVi.qZC5Y9Bv6',  -- Mot de passe: test123
    1,
    NOW(),
    '[]',
    '[]',
    0,
    0
);
```

**Identifiants Admin** :
- Email : `admin@example.com`
- Mot de passe : `test123`

---

## 📧 Tester l'Envoi d'Emails

Tous les emails envoyés par l'application sont capturés par **MailHog** :

- **URL** : http://localhost:8025
- Aucun identifiant requis
- Vous verrez tous les emails (confirmation, 2FA, notifications, etc.)

---

## 🔄 Réinitialiser la Base de Données

Si vous voulez repartir de zéro :

```bash
# Supprimer la base de données
docker compose exec php php bin/console doctrine:database:drop --force

# Recréer la base de données
docker compose exec php php bin/console doctrine:database:create

# Exécuter les migrations
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

# Recréer l'utilisateur de test
docker compose exec mysql mariadb -u cezar -psurete*2023 cezar -e "INSERT INTO user (email, roles, password, is_verified, created_at, backup_codes, password_history, is_referent_verified, trusted_version) VALUES ('test@example.com', '[\"ROLE_USER\"]', '\$2y\$13\$0IkfJKiRInJ5zdT5oYCHguDwdJa6Zf0tIV6/bNPCiVi.qZC5Y9Bv6', 1, NOW(), '[]', '[]', 0, 0);"
```

---

## 🎯 Tester les Fonctionnalités

### 1. Tableau de Bord

Après connexion, vous serez redirigé vers : http://localhost:9080/dashboard

### 2. Créer une Demande

- **Demande de titre de circulation** : http://localhost:9080/demande-titre-circulation
- **Demande de titre de véhicule** : http://localhost:9080/demande-titre-vehicule

### 3. Gérer les Documents

- **Documents personnels** : http://localhost:9080/document-personnel
- **Documents professionnels** : http://localhost:9080/document-professionnel

### 4. Messagerie

- **Messages** : http://localhost:9080/messages

### 5. Paramètres

- **Paramètres utilisateur** : http://localhost:9080/settings

---

## 🐛 Dépannage

### Problème : Impossible de se connecter

1. Vérifiez que l'utilisateur existe :
   ```bash
   docker compose exec mysql mariadb -u cezar -psurete*2023 cezar -e "SELECT * FROM user WHERE email = 'test@example.com';"
   ```

2. Vérifiez les logs :
   ```bash
   docker compose logs -f php
   ```

### Problème : Erreur 2FA

Si le système demande une authentification 2FA mais que vous n'en avez pas configuré :

1. Désactivez temporairement le 2FA dans la configuration
2. Ou configurez le 2FA pour le compte test

### Problème : Page blanche ou erreur 500

1. Videz le cache :
   ```bash
   docker compose exec php php bin/console cache:clear
   ```

2. Vérifiez les logs :
   ```bash
   docker compose logs -f php
   ```

---

## 📝 Résumé

✅ **Base de données initialisée** avec 74 migrations
✅ **Utilisateur de test créé** : test@example.com / test123
✅ **Application accessible** : http://localhost:9080
✅ **phpMyAdmin disponible** : http://localhost:9081
✅ **MailHog disponible** : http://localhost:8025

**Vous êtes prêt à tester l'application ! 🚀**

---

**Dernière mise à jour** : 22 novembre 2025

---

## ⚠️ Résolution du Problème "Entreprise introuvable"

Si vous voyez le message **"Entreprise introuvable"** lors de la connexion, cela signifie que l'utilisateur n'est pas lié à une entreprise.

### Solution Rapide

Exécutez ces commandes pour créer une entreprise et la lier à votre utilisateur :

```bash
# 1. Créer une entreprise
docker compose exec mysql mariadb -u cezar -psurete*2023 cezar -e "INSERT INTO entreprise (nom, siren, siret, email_entreprise, created_at) VALUES ('Mon Entreprise', '987654321', '98765432100001', 'contact@mon-entreprise.com', NOW());"

# 2. Récupérer l'ID de l'entreprise créée
docker compose exec mysql mariadb -u cezar -psurete*2023 cezar -e "SELECT id, nom FROM entreprise ORDER BY id DESC LIMIT 1;"

# 3. Lier l'utilisateur à l'entreprise (remplacez ENTREPRISE_ID par l'ID obtenu)
docker compose exec mysql mariadb -u cezar -psurete*2023 cezar -e "UPDATE user SET entreprise_id = ENTREPRISE_ID WHERE email = 'test@example.com';"
```

### Vérification

```bash
# Vérifier que l'utilisateur est bien lié à une entreprise
docker compose exec mysql mariadb -u cezar -psurete*2023 cezar -e "SELECT u.email, e.nom as entreprise FROM user u LEFT JOIN entreprise e ON u.entreprise_id = e.id WHERE u.email = 'test@example.com';"
```

Vous devriez voir le nom de l'entreprise à côté de l'email.

