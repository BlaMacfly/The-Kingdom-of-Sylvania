-- Script pour corriger les quêtes des draeneï pour les classes/races personnalisées
-- Ce script permet à toutes les races qui débutent dans la zone des draeneï d'accomplir les quêtes des draeneï
-- et modifie la quête "Rescue the Survivors!" qui nécessite le sort racial "Don des Naaru"

-- Définition des races (bitmasks)
-- 1 = Humain (1)
-- 2 = Orc (2)
-- 4 = Nain (4)
-- 8 = Elfe de la nuit (8)
-- 16 = Mort-vivant (16)
-- 32 = Tauren (32)
-- 64 = Gnome (64)
-- 128 = Troll (128)
-- 512 = Elfe de sang (512)
-- 1024 = Draeneï (1024)

-- Nous allons modifier les quêtes pour qu'elles soient disponibles pour toutes les races de l'Alliance
-- Alliance = Humain (1) + Nain (4) + Elfe de la nuit (8) + Gnome (64) + Draeneï (1024) = 1101

-- =============================================
-- PARTIE 1: CORRECTION DE LA QUÊTE "RESCUE THE SURVIVORS!" QUI NÉCESSITE LE DON DES NAARU
-- =============================================

-- 1. Modification des objectifs de la quête pour qu'elle soit automatiquement complétée
-- Nous changeons le type d'objectif pour qu'il ne nécessite plus l'utilisation du sort racial
UPDATE `quest_template` 
SET 
    -- Changer l'objectif pour parler à un survivant draeneï au lieu d'utiliser Don des Naaru
    `RequiredNpcOrGo1` = 16483,        -- ID du Draenei Survivor
    `RequiredNpcOrGoCount1` = 1,       -- Parler à 1 survivant
    `ObjectiveText1` = 'Survivant draeneï secouru' -- Nouveau texte d'objectif
WHERE `ID` = 9283;

-- 2. Mise à jour du texte de la quête pour refléter le changement
UPDATE `quest_template` 
SET 
    `QuestDescription` = 'Les cristaux de soins sont reconstitués, mais ce n\'est pas la seule façon dont nous pouvons aider les blessés. Les draeneï peuvent utiliser le don des Naaru pour guérir, mais d\'autres races doivent trouver des moyens alternatifs pour aider.$b$bVous devez secourir l\'un des survivants du crash.$b$bVous trouverez la plupart des survivants dans les zones à l\'ouest et au nord-ouest, mais le crash nous a dispersés dans toute la vallée.$b$bLes survivants ont souffert de l\'exposition aux cristaux de puissance irradiés, vous devriez donc pouvoir les trouver grâce à la lueur rouge qui les entoure.$B$BVous devez les sauver, $N.',
    `LogDescription` = 'Secourez un survivant draeneï dans l\'Île Brume-Azur.',
    `QuestCompletionLog` = 'Retournez voir Prêtresse Kyleen Il\'dinare à l\'Île Brume-Azur.'
WHERE `ID` = 9283;

-- 3. Suppression de toute condition spécifique à la race pour cette quête
DELETE FROM `conditions` WHERE `SourceEntry` = 9283 AND `SourceTypeOrReferenceId` = 19;

-- =============================================
-- PARTIE 2: CORRECTION DES QUÊTES DES DRAENEÏ POUR TOUTES LES RACES DE L'ALLIANCE
-- =============================================

-- Mise à jour des quêtes de départ des draeneï pour les rendre disponibles à toutes les races de l'Alliance
UPDATE `quest_template` SET `AllowableRaces` = 1101 WHERE `AllowableRaces` = 1024 AND `ID` IN (
    9279, -- You Survived!
    9280, -- Replenishing the Healing Crystals
    9283, -- Rescue the Survivors!
    9463, -- Medicinal Purpose
    9473, -- An Alternative Alternative
    9505  -- The Prophecy of Velen
);

-- Mise à jour des quêtes de voyage des draeneï pour les rendre disponibles à toutes les races de l'Alliance
UPDATE `quest_template` SET `AllowableRaces` = 1101 WHERE `AllowableRaces` = 1024 AND `ID` IN (
    9429, -- Travel to Darkshire
    9432, -- Travel to Astranaar
    9603, -- Beds, Bandages, and Beyond
    9604, -- On the Wings of a Hippogryph
    9605, -- Hippogryph Master Stephanos
    9606, -- Return to Topher Loaal
    9612, -- A Hearty Thanks!
    12776 -- The Exodar
);

-- Récupérer toutes les autres quêtes des draeneï et les rendre disponibles à toutes les races de l'Alliance
UPDATE `quest_template` SET `AllowableRaces` = 1101 WHERE `AllowableRaces` = 1024;

-- Mise à jour des conditions de quête qui pourraient être liées à la race
UPDATE `conditions` SET `ConditionValue1` = 1101 WHERE `ConditionTypeOrReference` = 25 AND `ConditionValue1` = 1024;

-- =============================================
-- PARTIE 3: MISE À JOUR DE LA CHAÎNE DE QUÊTES POUR ASSURER LA PROGRESSION
-- =============================================

-- S'assurer que la quête suivante après "Rescue the Survivors!" est également disponible pour toutes les races de l'Alliance
UPDATE `quest_template` 
SET `AllowableRaces` = 1101 -- Toutes les races de l'Alliance
WHERE `RewardNextQuest` = 9283 OR `ID` IN (
    -- Quêtes qui suivent "Rescue the Survivors!"
    9303, -- Botanist Taerix
    9309  -- Volatile Mutations
);
