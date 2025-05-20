-- Script modifié pour corriger les sorts raciaux des combinaisons race/classe personnalisées du module ARAC
-- Version adaptée pour AzerothCore où la table playercreateinfo_spell n'existe pas
-- Les sorts raciaux sont gérés via playercreateinfo_action

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

-- 2.1 Créer un correctif pour les races qui n'utilisent pas de mana
-- Pour les guerriers et voleurs elfes de sang, le Torrent arcanique n'est pas utile

-- Créer une entrée pour le "Torrent de Puissance" (version adaptée pour rage/énergie)
INSERT IGNORE INTO `spell_dbc` (`ID`, `Name`, `Description`, `ToolTip`, `SchoolMask`, `SpellDuration`, `TargetAuraState`, `ManaCost`, `ManaCostPercentage`, `BaseLevel`, `MaxLevel`, `DamageClass`, `ProcFlags`, `ProcChance`)
SELECT 28734, 'Torrent de Puissance', 'Absorbe l\'énergie environnante, restaurant 6% de votre rage ou énergie et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.', 'Absorbe l\'énergie environnante, restaurant 6% de votre rage ou énergie et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.', 64, 25, 0, 0, 0, 0, 0, 0, 0, 101
FROM `spell_dbc` LIMIT 1
WHERE NOT EXISTS (SELECT 1 FROM `spell_dbc` WHERE `ID` = 28734);

-- Remplacer le Torrent arcanique par le Torrent de Puissance pour les guerriers et voleurs elfes de sang
UPDATE `playercreateinfo_action` 
SET action = 28734
WHERE race = 10 AND class IN (1, 4) AND button = 11;

-- 2.2 Corriger les morts-vivants paladins (conflit thématique)

-- Créer une entrée pour le "Torrent d'ombre" (version adaptée pour morts-vivants paladins)
INSERT IGNORE INTO `spell_dbc` (`ID`, `Name`, `Description`, `ToolTip`, `SchoolMask`, `SpellDuration`, `TargetAuraState`, `ManaCost`, `ManaCostPercentage`, `BaseLevel`, `MaxLevel`, `DamageClass`, `ProcFlags`, `ProcChance`)
SELECT 28735, 'Torrent d\'ombre', 'Canalise l\'énergie des ombres, restaurant 6% de votre mana et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.', 'Canalise l\'énergie des ombres, restaurant 6% de votre mana et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.', 32, 25, 0, 0, 0, 0, 0, 0, 0, 101
FROM `spell_dbc` LIMIT 1
WHERE NOT EXISTS (SELECT 1 FROM `spell_dbc` WHERE `ID` = 28735);

-- Ajouter le Torrent d'ombre pour les paladins morts-vivants
INSERT IGNORE INTO `playercreateinfo_action` (race, class, button, action, type)
VALUES (5, 2, 11, 28735, 0);

-- ====================================================================
-- 3. Correction des compétences raciales (skills)
-- ====================================================================

-- 3.1 S'assurer que les bonus de compétence raciale sont présents

-- Elfes de sang: Affinité avec la magie (+10 Enchantement)
INSERT IGNORE INTO `playercreateinfo_skills` (raceMask, classMask, skill, rank, comment)
SELECT 512, POWER(2, pc.class - 1), 333, 0, 'Racial - Enchanting Skill'
FROM `playercreateinfo` pc
WHERE pc.race = 10
GROUP BY pc.class;

-- Draeneï: Affinité avec les gemmes (+10 Joaillerie)
INSERT IGNORE INTO `playercreateinfo_skills` (raceMask, classMask, skill, rank, comment)
SELECT 1024, POWER(2, pc.class - 1), 755, 0, 'Racial - Jewelcrafting Skill'
FROM `playercreateinfo` pc
WHERE pc.race = 11
GROUP BY pc.class;

-- Nains: +5 Forge
INSERT IGNORE INTO `playercreateinfo_skills` (raceMask, classMask, skill, rank, comment)
SELECT 4, POWER(2, pc.class - 1), 164, 0, 'Racial - Blacksmithing Skill'
FROM `playercreateinfo` pc
WHERE pc.race = 3
GROUP BY pc.class;

-- Nettoyage
DROP TEMPORARY TABLE tmp_racial_abilities;
