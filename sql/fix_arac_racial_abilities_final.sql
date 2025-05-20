-- Script simplifié pour corriger les sorts raciaux des combinaisons race/classe personnalisées du module ARAC
-- Version adaptée pour AzerothCore, compatible avec la structure spécifique de votre base

USE `acore_world`;

-- ====================================================================
-- 1. S'assurer que toutes les races ont leurs sorts raciaux dans la barre d'action
-- ====================================================================

-- Création d'une table temporaire pour stocker les associations race-sort racial
CREATE TEMPORARY TABLE tmp_racial_abilities (
    race_id INT NOT NULL,
    spell_id INT NOT NULL,
    button INT NOT NULL,
    PRIMARY KEY (race_id, spell_id)
);

-- Insertion des sorts raciaux importants dans la table temporaire
INSERT INTO tmp_racial_abilities VALUES
-- Humains
(1, 20598, 11), -- Perception
-- Orcs
(2, 20574, 11), -- Rage sanguinaire
-- Nains
(3, 20594, 11), -- Forme de pierre
-- Elfes de la nuit
(4, 20583, 11), -- Camouflage dans l'ombre
-- Morts-vivants
(5, 20579, 11), -- Volonté des Réprouvés
-- Taurens
(6, 20549, 11), -- Choc martial
-- Gnomes
(7, 20589, 11), -- Évasion
-- Trolls
(8, 20557, 11), -- Berserker
-- Elfes de sang
(10, 28730, 11), -- Torrent arcanique
-- Draeneï
(11, 59545, 11); -- Don des Naaru

-- Ajouter les sorts raciaux manquants à la barre d'action
-- Cette requête ajoute les sorts raciaux pour les combinaisons race/classe où ils manquent
INSERT IGNORE INTO `playercreateinfo_action` (race, class, button, action, type)
SELECT t.race_id, pc.class, t.button, t.spell_id, 0
FROM `playercreateinfo` pc
JOIN tmp_racial_abilities t ON pc.race = t.race_id
LEFT JOIN `playercreateinfo_action` pca ON pca.race = t.race_id AND pca.class = pc.class AND pca.button = t.button
WHERE pca.action IS NULL;

-- ====================================================================
-- 2. Corrections pour les combinaisons problématiques 
-- ====================================================================

-- 2.1 Créer un nouveau sort pour remplacer le Torrent arcanique pour les guerriers et voleurs
DELETE FROM `spell_dbc` WHERE `ID` = 28734;
INSERT IGNORE INTO `spell_dbc` (`ID`, `Name`) 
VALUES (28734, 'Torrent de Puissance');

UPDATE `spell_dbc` 
SET `Description` = 'Absorbe l\'énergie environnante, restaurant 6% de votre rage ou énergie et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.',
    `ToolTip` = 'Absorbe l\'énergie environnante, restaurant 6% de votre rage ou énergie et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.',
    `SchoolMask` = 64, 
    `SpellDuration` = 25,
    `ProcChance` = 101
WHERE `ID` = 28734;

-- Remplacer le Torrent arcanique par le Torrent de Puissance pour les guerriers et voleurs elfes de sang
UPDATE `playercreateinfo_action` 
SET action = 28734
WHERE race = 10 AND class IN (1, 4) AND action = 28730;

-- 2.2 Corriger les morts-vivants paladins (conflit thématique)
DELETE FROM `spell_dbc` WHERE `ID` = 28735;
INSERT IGNORE INTO `spell_dbc` (`ID`, `Name`) 
VALUES (28735, 'Torrent d\'ombre');

UPDATE `spell_dbc` 
SET `Description` = 'Canalise l\'énergie des ombres, restaurant 6% de votre mana et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.',
    `ToolTip` = 'Canalise l\'énergie des ombres, restaurant 6% de votre mana et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.',
    `SchoolMask` = 32, 
    `SpellDuration` = 25,
    `ProcChance` = 101
WHERE `ID` = 28735;

-- Ajouter le Torrent d'ombre pour les paladins morts-vivants
DELETE FROM `playercreateinfo_action` WHERE race = 5 AND class = 2 AND button = 11;
INSERT INTO `playercreateinfo_action` (race, class, button, action, type)
VALUES (5, 2, 11, 28735, 0);

-- Nettoyage
DROP TEMPORARY TABLE tmp_racial_abilities;
