# Documentation des Fonctionnalités - Projet Clairo

## 📋 Table des Matières

1. [Vue d'Ensemble](#vue-densemble)
2. [Architecture Technique](#architecture-technique)
3. [Modules et Bundles](#modules-et-bundles)
4. [Fonctionnalités Utilisateur](#fonctionnalités-utilisateur)
5. [Fonctionnalités Administrateur](#fonctionnalités-administrateur)
6. [APIs et Intégrations](#apis-et-intégrations)
7. [Sécurité et Authentification](#sécurité-et-authentification)
8. [Workflows Multi-Étapes](#workflows-multi-étapes)
9. [Génération de Documents](#génération-de-documents)
10. [Base de Données](#base-de-données)

---

## 🎯 Vue d'Ensemble

**Clairo** est une application web de gestion administrative permettant aux utilisateurs de :
- Soumettre des demandes de titres (circulation, véhicules)
- Gérer leurs documents personnels et professionnels
- Suivre l'état de leurs demandes
- Effectuer des paiements en ligne
- Communiquer avec l'administration

### Objectifs Principaux

- ✅ Dématérialisation des démarches administratives
- ✅ Suivi en temps réel des demandes
- ✅ Sécurisation des données personnelles
- ✅ Automatisation des processus
- ✅ Intégration avec des services externes

---

## 🏗️ Architecture Technique

### Stack Technologique

| Composant | Technologie | Version |
|-----------|-------------|---------|
| **Framework** | Symfony | 7.3 |
| **Langage** | PHP | 8.3 |
| **Base de données** | MariaDB | 12.0.2 |
| **Cache** | Redis | 7 |
| **Template Engine** | Twig | 3.x |
| **ORM** | Doctrine | 2.16 |
| **Formulaires** | Symfony Forms | 7.3 |
| **Validation** | Symfony Validator | 7.3 |
| **Sécurité** | Symfony Security | 7.3 |
| **Messagerie** | Symfony Messenger | 7.3 |

### Bibliothèques Principales

#### Génération de Documents
- **wkhtmltopdf** : Conversion HTML vers PDF
- **DomPDF** : Génération de PDF
- **mPDF** : Génération avancée de PDF
- **PHPSpreadsheet** : Export Excel
- **PHPWord** : Génération de documents Word

#### Authentification et Sécurité
- **scheb/2fa-bundle** : Authentification à deux facteurs
- **Google Authenticator** : 2FA via application mobile
- **Email 2FA** : 2FA par email
- **Trusted Device** : Gestion des appareils de confiance

#### Paiements
- **Stripe PHP SDK** : Intégration des paiements

#### Autres
- **QR Code** : Génération de QR codes (endroid/qr-code)
- **Twilio SDK** : Envoi de SMS
- **ClamAV** : Scan antivirus des fichiers uploadés

---

## 📦 Modules et Bundles

### 1. AdminBundle

**Localisation** : `src/AdminBundle/`

Gestion complète de l'interface d'administration.

**Fonctionnalités** :
- Dashboard administrateur
- Gestion des utilisateurs
- Gestion des demandes
- Statistiques et rapports
- Configuration système

### 2. MultiStepBundle

**Localisation** : `src/MultiStepBundle/`

Gestion des workflows multi-étapes pour les formulaires complexes.

**Fonctionnalités** :
- Formulaires en plusieurs étapes
- Sauvegarde automatique de la progression
- Validation par étape
- Navigation entre les étapes
- Récapitulatif final

**Cas d'usage** :
- Demande de titre de circulation
- Demande de titre de véhicule
- Inscription d'entreprise
- Dossiers complexes

### 3. AppIntegrationBundle

**Localisation** : `src/AppIntegrationBundle/`

Intégrations avec des services externes.

**Intégrations** :
- API INSEE (données entreprises)
- API Véhicules (immatriculation)
- API Microcesame
- Google Maps API

---

## 👤 Fonctionnalités Utilisateur

### 1. Authentification et Compte

#### Inscription
- **Route** : `/register`
- **Contrôleur** : `RegistrationController`
- **Fonctionnalités** :
  - Création de compte utilisateur
  - Validation par email
  - Vérification anti-spam
  - Politique de mots de passe sécurisés

#### Connexion
- **Route** : `/login`
- **Contrôleur** : `SecurityController`
- **Fonctionnalités** :
  - Authentification par email/mot de passe
  - 2FA obligatoire (Google Authenticator ou Email)
  - Gestion des appareils de confiance
  - Historique des connexions

#### Réinitialisation de Mot de Passe
- **Route** : `/reset-password`
- **Contrôleur** : `ResetPasswordController`
- **Fonctionnalités** :
  - Demande de réinitialisation par email
  - Token sécurisé avec expiration
  - Validation du nouveau mot de passe

### 2. Tableau de Bord

**Route** : `/dashboard`  
**Contrôleur** : `DashboardController`

**Sections** :
- 📊 Statistiques personnelles
- 📝 Demandes en cours
- 📬 Messages récents
- 🔔 Notifications
- 📄 Documents récents
- 💳 Historique des paiements

### 3. Gestion des Documents

#### Documents Personnels
**Route** : `/document-personnel`  
**Contrôleur** : `DocumentPersonnelController`

**Types de documents** :
- Carte d'identité
- Passeport
- Acte de naissance
- Justificatif de domicile
- Photo d'identité
- Casier judiciaire
- Titre de séjour

**Fonctionnalités** :
- Upload de fichiers (PDF, images)
- Scan antivirus automatique (ClamAV)
- Validation des formats
- Historique des versions
- Téléchargement

#### Documents Professionnels
**Route** : `/document-professionnel`  
**Contrôleur** : `DocumentProfessionnelController`

**Types de documents** :
- Kbis
- Statuts de l'entreprise
- Attestation fiscale
- Assurance professionnelle
- Diplômes et certifications

### 4. Demandes de Titres

#### Demande de Titre de Circulation
**Route** : `/demande-titre-circulation`  
**Contrôleur** : `DemandeTitreCirculationController`

**Workflow** (Multi-étapes) :
1. **Informations personnelles**
   - État civil
   - Adresse
   - Filiation

2. **Documents requis**
   - Pièce d'identité
   - Justificatif de domicile
   - Photo d'identité

3. **Informations complémentaires**
   - Motif de la demande
   - Urgence

4. **Récapitulatif et validation**
   - Vérification des données
   - Signature électronique

5. **Paiement**
   - Montant calculé
   - Paiement Stripe

**Statuts possibles** :
- 🟡 Brouillon
- 🔵 En cours de traitement
- 🟢 Validée
- 🔴 Rejetée
- ⚫ Annulée

#### Demande de Titre de Véhicule
**Route** : `/demande-titre-vehicule`  
**Contrôleur** : `DemandeTitreVehiculeController`

**Informations requises** :
- Données du véhicule (immatriculation, marque, modèle)
- Certificat de cession
- Contrôle technique
- Assurance
- Informations du propriétaire

**Intégration** :
- API de vérification d'immatriculation
- Calcul automatique des taxes

### 5. Gestion d'Entreprise

**Routes** :
- `/entreprise/create` - Création
- `/entreprise/edit/{id}` - Modification
- `/entreprise/view/{id}` - Consultation

**Fonctionnalités** :
- Recherche SIREN/SIRET via API INSEE
- Importation automatique des données
- Gestion des établissements
- Documents légaux
- Représentants légaux

### 6. Messagerie

**Route** : `/messages`  
**Contrôleur** : `MessageController`

**Fonctionnalités** :
- Envoi de messages à l'administration
- Réception de réponses
- Pièces jointes
- Notifications email
- Historique des conversations

### 7. Interventions

**Route** : `/intervention`  
**Contrôleur** : `InterventionController`

**Fonctionnalités** :
- Demande d'intervention
- Suivi en temps réel
- Assignation d'un agent
- Historique des interventions

### 8. Problèmes de Carte

**Route** : `/probleme-carte`  
**Contrôleur** : `ProblemeCarteController`

**Types de problèmes** :
- Carte perdue
- Carte volée
- Carte endommagée
- Erreur sur la carte

**Workflow** :
- Déclaration du problème
- Upload de documents justificatifs
- Demande de remplacement
- Suivi de la production

### 9. Paramètres Utilisateur

**Route** : `/settings`  
**Contrôleur** : `SettingsController`

**Sections** :
- 👤 Informations personnelles
- 🔐 Sécurité et mot de passe
- 📱 Authentification 2FA
- 🔔 Préférences de notification
- 🌐 Langue et localisation

---

## 👨‍💼 Fonctionnalités Administrateur

### 1. Dashboard Administrateur

**Route** : `/admin/dashboard`

**Métriques** :
- Nombre total d'utilisateurs
- Demandes en attente
- Demandes traitées aujourd'hui
- Revenus du mois
- Taux de satisfaction

**Graphiques** :
- Évolution des demandes
- Répartition par type
- Performance des agents

### 2. Gestion des Utilisateurs

**Routes** :
- `/admin/users` - Liste
- `/admin/users/{id}` - Détails
- `/admin/users/{id}/edit` - Modification
- `/admin/users/{id}/delete` - Suppression

**Fonctionnalités** :
- Recherche et filtres avancés
- Activation/Désactivation de comptes
- Réinitialisation de mot de passe
- Gestion des rôles et permissions
- Historique d'activité

### 3. Gestion des Demandes

**Routes** :
- `/admin/demandes` - Liste
- `/admin/demandes/{id}` - Traitement

**Actions possibles** :
- ✅ Valider une demande
- ❌ Rejeter une demande
- 📝 Demander des informations complémentaires
- 📄 Générer des documents
- 💬 Communiquer avec l'utilisateur

### 4. Gestion des Documents

**Fonctionnalités** :
- Validation des documents uploadés
- Scan antivirus
- Archivage automatique
- Recherche par métadonnées

### 5. Configuration Système

**Route** : `/admin/settings`

**Paramètres** :
- Tarifs des services
- Délais de traitement
- Templates d'emails
- Messages système
- Maintenance

### 6. Rapports et Statistiques

**Routes** :
- `/admin/reports/daily` - Rapport journalier
- `/admin/reports/monthly` - Rapport mensuel
- `/admin/reports/custom` - Rapport personnalisé

**Exports** :
- PDF
- Excel
- CSV

---

## 🔌 APIs et Intégrations

### 1. API INSEE

**Service** : Récupération des données d'entreprises

**Configuration** :
```env
INSEE_CONSUMER_KEY=...
INSEE_CONSUMER_SECRET=...
INSEE_API_KEY=...
```

**Endpoints utilisés** :
- Recherche par SIREN
- Recherche par SIRET
- Données établissement

### 2. API Véhicules

**Service** : Vérification d'immatriculation

**Configuration** :
```env
VEHICLE_API_KEY=...
VEHICLE_API_HOST_NAME=https://apiplaqueimmatriculation.com
```

**Données récupérées** :
- Marque et modèle
- Date de première immatriculation
- Puissance fiscale
- Émissions CO2

### 3. Google Maps API

**Service** : Géolocalisation et adresses

**Configuration** :
```env
GOOGLE_MAPS_API_KEY=...
```

**Fonctionnalités** :
- Autocomplétion d'adresses
- Validation d'adresses
- Calcul de distances

### 4. Stripe

**Service** : Paiements en ligne

**Configuration** :
```env
STRIPE_API_KEY=sk_test_... # Test
STRIPE_API_KEY=sk_live_... # Production
```

**Fonctionnalités** :
- Paiements par carte bancaire
- Webhooks pour confirmation
- Remboursements
- Historique des transactions

### 5. Twilio

**Service** : Envoi de SMS

**Fonctionnalités** :
- Notifications SMS
- Codes de vérification
- Alertes importantes

### 6. Microcesame API

**Service** : Intégration système interne

**Configuration** :
```env
MICROCESAME_API_URL=...
MICROCESAME_API_KEY=...
```

---

## 🔐 Sécurité et Authentification

### 1. Authentification à Deux Facteurs (2FA)

#### Google Authenticator
- Génération de QR code
- Validation TOTP (Time-based One-Time Password)
- Codes de secours

#### Email 2FA
- Envoi de code par email
- Expiration après 10 minutes
- Limitation des tentatives

#### Appareils de Confiance
- Mémorisation des appareils
- Durée de confiance configurable
- Révocation possible

### 2. Gestion des Mots de Passe

**Politique** :
- Minimum 8 caractères
- Au moins une majuscule
- Au moins un chiffre
- Au moins un caractère spécial

**Hachage** :
- Algorithme : bcrypt
- Cost factor : 13

### 3. Protection CSRF

- Tokens CSRF sur tous les formulaires
- Validation automatique par Symfony

### 4. Validation des Fichiers

**Contrôles** :
- Types MIME autorisés
- Taille maximale : 10 MB
- Scan antivirus (ClamAV)
- Vérification d'intégrité

### 5. Rate Limiting

**Limites** :
- Connexion : 5 tentatives / 15 minutes
- Réinitialisation mot de passe : 3 / heure
- API : 100 requêtes / minute

### 6. Chiffrement

**Données sensibles** :
- Chiffrement AES-256
- Clé de chiffrement dans `.env`
- Rotation des clés

---

## 🔄 Workflows Multi-Étapes

### Architecture

Le `MultiStepBundle` permet de créer des formulaires complexes en plusieurs étapes.

### Composants Principaux

1. **StepManager** : Gestion de la progression
2. **StepData** : Stockage des données temporaires
3. **StepValidator** : Validation par étape
4. **StepNavigator** : Navigation entre étapes

### Exemple : Demande de Titre

```php
// Configuration des étapes
$steps = [
    'etat_civil' => EtatCivilType::class,
    'adresse' => AdresseType::class,
    'filiation' => FiliationType::class,
    'documents' => DocumentsType::class,
    'recapitulatif' => RecapitulatifType::class,
];
```

### Stockage

- **Session** : Données temporaires pendant le workflow
- **Base de données** : Sauvegarde automatique (table `step_data`)
- **Redis** : Cache pour performance

### Navigation

- ⏮️ Retour à l'étape précédente
- ⏭️ Passage à l'étape suivante
- 🏠 Retour au début
- 💾 Sauvegarde et continuer plus tard

---

## 📄 Génération de Documents

### 1. PDF avec wkhtmltopdf

**Avantages** :
- Rendu HTML/CSS fidèle
- Support JavaScript
- Haute qualité

**Utilisation** :
```php
$pdf = $this->get('knp_snappy.pdf');
$html = $this->renderView('pdf/template.html.twig', $data);
$pdf->generateFromHtml($html, '/path/to/file.pdf');
```

### 2. PDF avec DomPDF

**Avantages** :
- Pure PHP (pas de dépendance système)
- Léger et rapide

### 3. PDF avec mPDF

**Avantages** :
- Support UTF-8 complet
- En-têtes et pieds de page avancés
- Watermarks

### 4. QR Codes

**Utilisation** :
```php
$qrCode = QrCode::create($data)
    ->setSize(300)
    ->setMargin(10);
```

**Cas d'usage** :
- Vérification de documents
- Liens rapides
- Codes de suivi

### 5. Export Excel

**Bibliothèque** : PHPSpreadsheet

**Fonctionnalités** :
- Export de listes
- Rapports complexes
- Graphiques

---

## 💾 Base de Données

### Entités Principales

#### User
**Table** : `user`

**Champs principaux** :
- `id` : Identifiant unique
- `email` : Email (unique)
- `password` : Mot de passe haché
- `roles` : Rôles JSON
- `is_verified` : Email vérifié
- `google_authenticator_secret` : Secret 2FA
- `trusted_version` : Version des appareils de confiance

**Relations** :
- `demandes_titre_circulation` : OneToMany
- `demandes_titre_vehicule` : OneToMany
- `messages` : OneToMany
- `documents` : OneToMany

#### DemandeTitreCirculation
**Table** : `demande_titre_circulation`

**Champs** :
- `id`
- `user_id` : Utilisateur
- `status` : Statut (draft, pending, approved, rejected)
- `created_at` : Date de création
- `updated_at` : Date de modification
- `etat_civil_id` : État civil
- `adresse_id` : Adresse
- `filiation_id` : Filiation

#### DemandeTitreVehicule
**Table** : `demande_titre_vehicule`

**Champs similaires** + :
- `immatriculation` : Plaque d'immatriculation
- `marque` : Marque du véhicule
- `modele` : Modèle
- `annee` : Année

#### Entreprise
**Table** : `entreprise`

**Champs** :
- `siren` : Numéro SIREN
- `siret` : Numéro SIRET
- `denomination` : Raison sociale
- `forme_juridique` : Forme juridique
- `capital` : Capital social
- `date_creation` : Date de création

#### Message
**Table** : `message`

**Champs** :
- `sender_id` : Expéditeur
- `recipient_id` : Destinataire
- `subject` : Sujet
- `content` : Contenu
- `is_read` : Lu/Non lu
- `created_at` : Date d'envoi

### Migrations

**Localisation** : `migrations/`

**Commandes** :
```bash
# Créer une migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Rollback
php bin/console doctrine:migrations:migrate prev
```

---

## 🧪 Tests

### Tests Unitaires

**Localisation** : `tests/`

**Exécution** :
```bash
docker compose exec php php bin/phpunit
```

### Tests Fonctionnels

**Outils** :
- Symfony Browser Kit
- Symfony CSS Selector

---

## 📧 Emails

### Configuration

**Développement** : MailHog (SMTP local)
```env
MAILER_DSN=smtp://mailhog:1025
```

**Production** : SMTP externe
```env
MAILER_DSN=smtp://user:pass@smtp.example.com:587
```

### Templates

**Localisation** : `templates/emails/`

**Types d'emails** :
- Confirmation d'inscription
- Réinitialisation de mot de passe
- Code 2FA
- Notification de demande
- Confirmation de paiement

---

## 🔔 Notifications

### Canaux

1. **Email** : Notifications importantes
2. **SMS** : Alertes urgentes (via Twilio)
3. **In-app** : Notifications dans l'application

### Types de Notifications

- ✅ Demande validée
- ❌ Demande rejetée
- 📝 Informations complémentaires requises
- 💳 Paiement confirmé
- 📄 Document expiré

---

## 📊 Monitoring et Logs

### Logs

**Localisation** : `var/log/`

**Niveaux** :
- DEBUG
- INFO
- WARNING
- ERROR
- CRITICAL

**Rotation** : Automatique (Monolog)

### SonarQube

**URL** : http://localhost:9900

**Métriques** :
- Qualité du code
- Couverture de tests
- Bugs et vulnérabilités
- Code smells
- Dette technique

---

## 🚀 Performance

### Optimisations

1. **Cache** :
   - APCu : Cache PHP
   - Redis : Cache applicatif et sessions
   - Doctrine : Cache des requêtes

2. **Base de données** :
   - Index sur les colonnes fréquemment recherchées
   - Requêtes optimisées avec Doctrine QueryBuilder

3. **Assets** :
   - Minification CSS/JS
   - Compression Gzip

---

## 📱 Responsive Design

L'application est entièrement responsive et fonctionne sur :
- 💻 Desktop
- 📱 Mobile
- 📲 Tablette

---

## 🌐 Internationalisation

**Langues supportées** :
- Français (par défaut)
- Anglais
- Espagnol (partiel)

**Configuration** : `translations/`

---

**Dernière mise à jour** : 22 novembre 2025
