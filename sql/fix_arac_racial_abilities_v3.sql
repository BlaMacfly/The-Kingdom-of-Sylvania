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

-- 2.1 Vérifier si le sort "Torrent de Puissance" existe déjà
SET @spell_exists = (SELECT COUNT(*) FROM `spell_dbc` WHERE `ID` = 28734);
-- S'il n'existe pas, le créer
-- Note : Cette version utilise une approche plus simple compatible avec plus de versions de MySQL
DELETE FROM `spell_dbc` WHERE `ID` = 28734;
INSERT INTO `spell_dbc` (`ID`, `Name`, `Description`, `ToolTip`, `SchoolMask`, `SpellDuration`, `TargetAuraState`, `ManaCost`, `ManaCostPercentage`, `BaseLevel`, `MaxLevel`, `DamageClass`, `ProcFlags`, `ProcChance`)
VALUES (
    28734, 
    'Torrent de Puissance', 
    'Absorbe l\'énergie environnante, restaurant 6% de votre rage ou énergie et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.', 
    'Absorbe l\'énergie environnante, restaurant 6% de votre rage ou énergie et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.',
    64, 25, 0, 0, 0, 0, 0, 0, 0, 101
);

-- Remplacer le Torrent arcanique par le Torrent de Puissance pour les guerriers et voleurs elfes de sang
UPDATE `playercreateinfo_action` 
SET action = 28734
WHERE race = 10 AND class IN (1, 4) AND button = 11;

-- 2.2 Corriger les morts-vivants paladins (conflit thématique)
-- Créer le sort "Torrent d'ombre" pour les morts-vivants paladins
DELETE FROM `spell_dbc` WHERE `ID` = 28735;
INSERT INTO `spell_dbc` (`ID`, `Name`, `Description`, `ToolTip`, `SchoolMask`, `SpellDuration`, `TargetAuraState`, `ManaCost`, `ManaCostPercentage`, `BaseLevel`, `MaxLevel`, `DamageClass`, `ProcFlags`, `ProcChance`)
VALUES (
    28735, 
    'Torrent d\'ombre', 
    'Canalise l\'énergie des ombres, restaurant 6% de votre mana et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.', 
    'Canalise l\'énergie des ombres, restaurant 6% de votre mana et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.',
    32, 25, 0, 0, 0, 0, 0, 0, 0, 101
);

-- Ajouter le Torrent d'ombre pour les paladins morts-vivants
DELETE FROM `playercreateinfo_action` WHERE race = 5 AND class = 2 AND button = 11;
INSERT INTO `playercreateinfo_action` (race, class, button, action, type)
VALUES (5, 2, 11, 28735, 0);

-- ====================================================================
-- 3. Correction des compétences raciales (skills)
-- ====================================================================

-- 3.1 S'assurer que les bonus de compétence raciale sont présents

-- Vérifier si la table playercreateinfo_skills existe
-- Utiliser une approche plus sûre avec le schéma d'information pour vérifier l'existence de la table
SET @table_exists = (
    SELECT COUNT(*)
    FROM information_schema.tables
    WHERE table_schema = 'acore_world'
    AND table_name = 'playercreateinfo_skills'
);

-- Si la table playercreateinfo_skills existe, ajouter les compétences raciales
-- Cette partie n'est exécutée que si la table existe
DELIMITER //
BEGIN
    IF @table_exists > 0 THEN
        -- Elfes de sang: Affinité avec la magie (+10 Enchantement)
        INSERT IGNORE INTO `playercreateinfo_skills` (raceMask, classMask, skill, rank, comment)
        VALUES (512, 1535, 333, 0, 'Racial - Enchanting Skill');
        
        -- Draeneï: Affinité avec les gemmes (+10 Joaillerie)
        INSERT IGNORE INTO `playercreateinfo_skills` (raceMask, classMask, skill, rank, comment)
        VALUES (1024, 1535, 755, 0, 'Racial - Jewelcrafting Skill');
        
        -- Nains: +5 Forge
        INSERT IGNORE INTO `playercreateinfo_skills` (raceMask, classMask, skill, rank, comment)
        VALUES (4, 1535, 164, 0, 'Racial - Blacksmithing Skill');
    END IF;
END //
DELIMITER ;

-- Nettoyage
DROP TEMPORARY TABLE tmp_racial_abilities;
