-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 08 oct. 2024 à 16:10
-- Version du serveur : 8.0.39-0ubuntu0.22.04.1
-- Version de PHP : 8.1.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `cezar2`
--

-- --------------------------------------------------------

--
-- Structure de la table `acte_naissance`
--

CREATE TABLE `acte_naissance` (
                                  `id` int NOT NULL,
                                  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `adresse`
--

CREATE TABLE `adresse` (
                           `id` int NOT NULL,
                           `tour_etc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                           `escalier_etc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                           `num_voie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                           `cp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                           `distribution` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                           `ville` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                           `pays` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `adresse_entreprise`
--

CREATE TABLE `adresse_entreprise` (
                                      `id` int NOT NULL,
                                      `num_voie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                      `distribution` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                      `ville` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                      `tour_etc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                      `cp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                      `num_telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `atex0`
--

CREATE TABLE `atex0` (
                         `id` int NOT NULL,
                         `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                         `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `attestation_hebergeant`
--

CREATE TABLE `attestation_hebergeant` (
                                          `id` int NOT NULL,
                                          `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                          `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `autre_document`
--

CREATE TABLE `autre_document` (
                                  `id` int NOT NULL,
                                  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `casier_judiciaire`
--

CREATE TABLE `casier_judiciaire` (
                                     `id` int NOT NULL,
                                     `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                     `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demande_titre_circulation`
--

CREATE TABLE `demande_titre_circulation` (
                                             `id` int NOT NULL,
                                             `intervention_id` int DEFAULT NULL,
                                             `etatcivil_id` int DEFAULT NULL,
                                             `filiation_id` int DEFAULT NULL,
                                             `adresse_id` int DEFAULT NULL,
                                             `infocomplementaire_id` int DEFAULT NULL,
                                             `documentpersonnel_id` int DEFAULT NULL,
                                             `documentprofessionnel_id` int DEFAULT NULL,
                                             `created_at` datetime DEFAULT NULL,
                                             `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
                                               `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
                                               `executed_at` datetime DEFAULT NULL,
                                               `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
                                                                                           ('DoctrineMigrations\\Version20230829095830', '2023-10-18 15:02:14', 20),
                                                                                           ('DoctrineMigrations\\Version20230830122232', '2023-10-18 15:02:14', 158),
                                                                                           ('DoctrineMigrations\\Version20230904070135', '2023-10-18 15:02:14', 394),
                                                                                           ('DoctrineMigrations\\Version20230904071018', '2023-10-18 15:02:15', 4),
                                                                                           ('DoctrineMigrations\\Version20230904072444', '2023-10-18 15:02:15', 37),
                                                                                           ('DoctrineMigrations\\Version20230904073237', '2023-10-18 15:02:15', 6),
                                                                                           ('DoctrineMigrations\\Version20230904100542', '2023-10-18 15:02:15', 15),
                                                                                           ('DoctrineMigrations\\Version20230914075441', '2023-10-18 15:02:15', 83);

-- --------------------------------------------------------

--
-- Structure de la table `document_identite`
--

CREATE TABLE `document_identite` (
                                     `id` int NOT NULL,
                                     `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                     `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `document_personnel`
--

CREATE TABLE `document_personnel` (
                                      `id` int NOT NULL,
                                      `arrondissement_naissance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                      `identity_id` int DEFAULT NULL,
                                      `photo_id` int DEFAULT NULL,
                                      `casier_id` int DEFAULT NULL,
                                      `acte_naiss_id` int DEFAULT NULL,
                                      `domicile_id` int DEFAULT NULL,
                                      `hebergement_id` int DEFAULT NULL,
                                      `ident_hebergent_id` int DEFAULT NULL,
                                      `sejour_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `document_professionnel`
--

CREATE TABLE `document_professionnel` (
                                          `id` int NOT NULL,
                                          `date_gies0_debut` date DEFAULT NULL,
                                          `date_gies0_fin` date DEFAULT NULL,
                                          `date_atex0_debut` date DEFAULT NULL,
                                          `date_atex0_fin` date DEFAULT NULL,
                                          `gies0_id` int DEFAULT NULL,
                                          `gies1_id` int DEFAULT NULL,
                                          `gies2_id` int DEFAULT NULL,
                                          `atex0_id` int DEFAULT NULL,
                                          `autre_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `entreprise`
--

CREATE TABLE `entreprise` (
                              `id` int NOT NULL,
                              `nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `code_ape` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `signe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `complement_nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `tva_intra_communautaire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `secteur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `statut` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `nature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `siret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `num_telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `nom_responsable` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `siren` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `naf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `nationalite` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `email_referent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etat_civil`
--

CREATE TABLE `etat_civil` (
                              `id` int NOT NULL,
                              `titre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `prenom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `prenom2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `prenom3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `prenom4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `date_naissance` date DEFAULT NULL,
                              `pays_naissance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `lieu_naissance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `cp_naissance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `arrondissement_naissance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `nom_marital` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `nationalite` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `filiation`
--

CREATE TABLE `filiation` (
                             `id` int NOT NULL,
                             `nom_pere` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                             `prenom_pere` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                             `nom_mere` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                             `prenom_mere` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gies0`
--

CREATE TABLE `gies0` (
                         `id` int NOT NULL,
                         `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                         `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gies1`
--

CREATE TABLE `gies1` (
                         `id` int NOT NULL,
                         `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                         `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gies2`
--

CREATE TABLE `gies2` (
                         `id` int NOT NULL,
                         `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                         `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `identite_hebergeant`
--

CREATE TABLE `identite_hebergeant` (
                                       `id` int NOT NULL,
                                       `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `info_complementaire`
--

CREATE TABLE `info_complementaire` (
                                       `id` int NOT NULL,
                                       `num_telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                       `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `intervention`
--

CREATE TABLE `intervention` (
                                `id` int NOT NULL,
                                `bat_administration` tinyint(1) DEFAULT NULL,
                                `exploitation_fos` tinyint(1) DEFAULT NULL,
                                `exploitation_lavera` tinyint(1) DEFAULT NULL,
                                `motif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                `duree` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                `autre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                `date_intervention` date DEFAULT NULL,
                                `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `justificatif_domicile`
--

CREATE TABLE `justificatif_domicile` (
                                         `id` int NOT NULL,
                                         `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                         `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
                                      `id` bigint NOT NULL,
                                      `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                      `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                                      `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
                                      `created_at` datetime NOT NULL,
                                      `available_at` datetime NOT NULL,
                                      `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `photo_identite`
--

CREATE TABLE `photo_identite` (
                                  `id` int NOT NULL,
                                  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `probleme_carte`
--

CREATE TABLE `probleme_carte` (
                                  `id` int NOT NULL,
                                  `motif` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                  `nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                  `date_naissance` date NOT NULL,
                                  `suite_donner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `titre_sejour`
--

CREATE TABLE `titre_sejour` (
                                `id` int NOT NULL,
                                `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                `original_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
                        `id` int NOT NULL,
                        `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `roles` json NOT NULL,
                        `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `is_verified` tinyint(1) NOT NULL,
                        `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `acte_naissance`
--
ALTER TABLE `acte_naissance`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `adresse`
--
ALTER TABLE `adresse`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `adresse_entreprise`
--
ALTER TABLE `adresse_entreprise`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `atex0`
--
ALTER TABLE `atex0`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `attestation_hebergeant`
--
ALTER TABLE `attestation_hebergeant`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `autre_document`
--
ALTER TABLE `autre_document`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `casier_judiciaire`
--
ALTER TABLE `casier_judiciaire`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `demande_titre_circulation`
--
ALTER TABLE `demande_titre_circulation`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `UNIQ_C52149D98EAE3863` (`intervention_id`),
    ADD UNIQUE KEY `UNIQ_C52149D9F7560086` (`etatcivil_id`),
    ADD UNIQUE KEY `UNIQ_C52149D9DE3E023A` (`filiation_id`),
    ADD UNIQUE KEY `UNIQ_C52149D94DE7DC5C` (`adresse_id`),
    ADD UNIQUE KEY `UNIQ_C52149D9AACB1B2F` (`infocomplementaire_id`),
    ADD UNIQUE KEY `UNIQ_C52149D9C00415E1` (`documentpersonnel_id`),
    ADD UNIQUE KEY `UNIQ_C52149D9FE5E2AB0` (`documentprofessionnel_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
    ADD PRIMARY KEY (`version`);

--
-- Index pour la table `document_identite`
--
ALTER TABLE `document_identite`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `document_personnel`
--
ALTER TABLE `document_personnel`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `UNIQ_B484DB7FFF3ED4A8` (`identity_id`),
    ADD UNIQUE KEY `UNIQ_B484DB7F7E9E4C8C` (`photo_id`),
    ADD UNIQUE KEY `UNIQ_B484DB7F643911C6` (`casier_id`),
    ADD UNIQUE KEY `UNIQ_B484DB7FCEA0B946` (`acte_naiss_id`),
    ADD UNIQUE KEY `UNIQ_B484DB7F95715F7D` (`domicile_id`),
    ADD UNIQUE KEY `UNIQ_B484DB7F23BB0F66` (`hebergement_id`),
    ADD UNIQUE KEY `UNIQ_B484DB7F266B14AC` (`ident_hebergent_id`),
    ADD UNIQUE KEY `UNIQ_B484DB7F84CF0CF` (`sejour_id`);

--
-- Index pour la table `document_professionnel`
--
ALTER TABLE `document_professionnel`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `UNIQ_5D8765842E429F88` (`gies0_id`),
    ADD UNIQUE KEY `UNIQ_5D87658496FEF8ED` (`gies1_id`),
    ADD UNIQUE KEY `UNIQ_5D876584844B5703` (`gies2_id`),
    ADD UNIQUE KEY `UNIQ_5D87658499F45A10` (`atex0_id`),
    ADD UNIQUE KEY `UNIQ_5D876584416A67AB` (`autre_id`);

--
-- Index pour la table `entreprise`
--
ALTER TABLE `entreprise`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `etat_civil`
--
ALTER TABLE `etat_civil`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `filiation`
--
ALTER TABLE `filiation`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `gies0`
--
ALTER TABLE `gies0`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `gies1`
--
ALTER TABLE `gies1`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `gies2`
--
ALTER TABLE `gies2`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `identite_hebergeant`
--
ALTER TABLE `identite_hebergeant`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `info_complementaire`
--
ALTER TABLE `info_complementaire`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `intervention`
--
ALTER TABLE `intervention`
    ADD PRIMARY KEY (`id`),
    ADD KEY `IDX_D11814ABA76ED395` (`user_id`);

--
-- Index pour la table `justificatif_domicile`
--
ALTER TABLE `justificatif_domicile`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
    ADD PRIMARY KEY (`id`),
    ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
    ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
    ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `photo_identite`
--
ALTER TABLE `photo_identite`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `probleme_carte`
--
ALTER TABLE `probleme_carte`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `titre_sejour`
--
ALTER TABLE `titre_sejour`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `acte_naissance`
--
ALTER TABLE `acte_naissance`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `adresse`
--
ALTER TABLE `adresse`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `adresse_entreprise`
--
ALTER TABLE `adresse_entreprise`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `atex0`
--
ALTER TABLE `atex0`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `attestation_hebergeant`
--
ALTER TABLE `attestation_hebergeant`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `autre_document`
--
ALTER TABLE `autre_document`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `casier_judiciaire`
--
ALTER TABLE `casier_judiciaire`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `demande_titre_circulation`
--
ALTER TABLE `demande_titre_circulation`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `document_identite`
--
ALTER TABLE `document_identite`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `document_personnel`
--
ALTER TABLE `document_personnel`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `document_professionnel`
--
ALTER TABLE `document_professionnel`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `entreprise`
--
ALTER TABLE `entreprise`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `etat_civil`
--
ALTER TABLE `etat_civil`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `filiation`
--
ALTER TABLE `filiation`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `gies0`
--
ALTER TABLE `gies0`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `gies1`
--
ALTER TABLE `gies1`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `gies2`
--
ALTER TABLE `gies2`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `identite_hebergeant`
--
ALTER TABLE `identite_hebergeant`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `info_complementaire`
--
ALTER TABLE `info_complementaire`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `intervention`
--
ALTER TABLE `intervention`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `justificatif_domicile`
--
ALTER TABLE `justificatif_domicile`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
    MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `photo_identite`
--
ALTER TABLE `photo_identite`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `probleme_carte`
--
ALTER TABLE `probleme_carte`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `titre_sejour`
--
ALTER TABLE `titre_sejour`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `demande_titre_circulation`
--
ALTER TABLE `demande_titre_circulation`
    ADD CONSTRAINT `FK_C52149D94DE7DC5C` FOREIGN KEY (`adresse_id`) REFERENCES `adresse` (`id`),
    ADD CONSTRAINT `FK_C52149D98EAE3863` FOREIGN KEY (`intervention_id`) REFERENCES `intervention` (`id`),
    ADD CONSTRAINT `FK_C52149D9AACB1B2F` FOREIGN KEY (`infocomplementaire_id`) REFERENCES `info_complementaire` (`id`),
    ADD CONSTRAINT `FK_C52149D9C00415E1` FOREIGN KEY (`documentpersonnel_id`) REFERENCES `document_personnel` (`id`),
    ADD CONSTRAINT `FK_C52149D9DE3E023A` FOREIGN KEY (`filiation_id`) REFERENCES `filiation` (`id`),
    ADD CONSTRAINT `FK_C52149D9F7560086` FOREIGN KEY (`etatcivil_id`) REFERENCES `etat_civil` (`id`),
    ADD CONSTRAINT `FK_C52149D9FE5E2AB0` FOREIGN KEY (`documentprofessionnel_id`) REFERENCES `document_professionnel` (`id`);

--
-- Contraintes pour la table `document_personnel`
--
ALTER TABLE `document_personnel`
    ADD CONSTRAINT `FK_B484DB7F23BB0F66` FOREIGN KEY (`hebergement_id`) REFERENCES `attestation_hebergeant` (`id`),
    ADD CONSTRAINT `FK_B484DB7F266B14AC` FOREIGN KEY (`ident_hebergent_id`) REFERENCES `identite_hebergeant` (`id`),
    ADD CONSTRAINT `FK_B484DB7F643911C6` FOREIGN KEY (`casier_id`) REFERENCES `casier_judiciaire` (`id`),
    ADD CONSTRAINT `FK_B484DB7F7E9E4C8C` FOREIGN KEY (`photo_id`) REFERENCES `photo_identite` (`id`),
    ADD CONSTRAINT `FK_B484DB7F84CF0CF` FOREIGN KEY (`sejour_id`) REFERENCES `titre_sejour` (`id`),
    ADD CONSTRAINT `FK_B484DB7F95715F7D` FOREIGN KEY (`domicile_id`) REFERENCES `justificatif_domicile` (`id`),
    ADD CONSTRAINT `FK_B484DB7FCEA0B946` FOREIGN KEY (`acte_naiss_id`) REFERENCES `acte_naissance` (`id`),
    ADD CONSTRAINT `FK_B484DB7FFF3ED4A8` FOREIGN KEY (`identity_id`) REFERENCES `document_identite` (`id`);

--
-- Contraintes pour la table `document_professionnel`
--
ALTER TABLE `document_professionnel`
    ADD CONSTRAINT `FK_5D8765842E429F88` FOREIGN KEY (`gies0_id`) REFERENCES `gies0` (`id`),
    ADD CONSTRAINT `FK_5D876584416A67AB` FOREIGN KEY (`autre_id`) REFERENCES `autre_document` (`id`),
    ADD CONSTRAINT `FK_5D876584844B5703` FOREIGN KEY (`gies2_id`) REFERENCES `gies2` (`id`),
    ADD CONSTRAINT `FK_5D87658496FEF8ED` FOREIGN KEY (`gies1_id`) REFERENCES `gies1` (`id`),
    ADD CONSTRAINT `FK_5D87658499F45A10` FOREIGN KEY (`atex0_id`) REFERENCES `atex0` (`id`);

--
-- Contraintes pour la table `intervention`
--
ALTER TABLE `intervention`
    ADD CONSTRAINT `FK_D11814ABA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
