-- Script pour corriger les sorts raciaux des combinaisons race/classe personnalisées du module ARAC
-- Ce script s'assure que tous les personnages reçoivent correctement leurs sorts raciaux
-- et propose des alternatives pour les combinaisons où les sorts raciaux sont incompatibles

USE `acore_world`;

-- Liste des IDs de sorts raciaux par race
-- Humain (1): 20598 (Perception), 20864 (Diplomatie), 20599 (L'esprit humain)
-- Orc (2): 20573 (Robustesse), 20574 (Rage sanguinaire), 33697 (Résistance aux étourdissements)
-- Nain (3): 20594 (Forme de pierre), 20595 (Spécialisation fusil), 20596 (Résistance au givre)
-- Elfe de la nuit (4): 20582 (Nature sauvage), 20583 (Camouflage dans l'ombre), 20585 (Esprit feu-follet)
-- Mort-vivant (5): 20577 (Cannibalisme), 20579 (Volonté des Réprouvés), 5227 (Respiration aquatique sous-marine)
-- Tauren (6): 20549 (Choc martial), 20550 (Endurance), 20551 (Résistance à la nature)
-- Gnome (7): 20589 (Évasion), 20591 (Expansion d'esprit), 20592 (Résistance aux arcanes)
-- Troll (8): 20555 (Régénération), 20557 (Berserker), 20558 (Spécialisation armes de jet)
-- Elfe de sang (10): 28877 (Résistance aux arcanes), 28730 (Torrent arcanique), 822 (Affinité avec la magie)
-- Draeneï (11): 59221 (Affinité avec les gemmes), 28875 (Présence héroïque), 59545 (Don des Naaru)

-- ====================================================================
-- 1. S'assurer que toutes les races ont leurs sorts raciaux
-- ====================================================================

-- Créer une table temporaire contenant tous les sorts raciaux par race
CREATE TEMPORARY TABLE tmp_racial_spells (
    race_id INT NOT NULL,
    spell_id INT NOT NULL,
    PRIMARY KEY (race_id, spell_id)
);

-- Insérer tous les sorts raciaux dans la table temporaire
INSERT INTO tmp_racial_spells VALUES
-- Humains
(1, 20598), -- Perception
(1, 20864), -- Diplomatie
(1, 20599), -- L'esprit humain
-- Orcs
(2, 20573), -- Robustesse
(2, 20574), -- Rage sanguinaire  
(2, 33697), -- Résistance aux étourdissements
-- Nains
(3, 20594), -- Forme de pierre
(3, 20595), -- Spécialisation fusil
(3, 20596), -- Résistance au givre
-- Elfes de la nuit
(4, 20582), -- Nature sauvage
(4, 20583), -- Camouflage dans l'ombre
(4, 20585), -- Esprit feu-follet
-- Morts-vivants
(5, 20577), -- Cannibalisme
(5, 20579), -- Volonté des Réprouvés
(5, 5227),  -- Respiration aquatique sous-marine
-- Taurens
(6, 20549), -- Choc martial
(6, 20550), -- Endurance
(6, 20551), -- Résistance à la nature
-- Gnomes
(7, 20589), -- Évasion
(7, 20591), -- Expansion d'esprit
(7, 20592), -- Résistance aux arcanes
-- Trolls
(8, 20555), -- Régénération
(8, 20557), -- Berserker
(8, 20558), -- Spécialisation armes de jet
-- Elfes de sang
(10, 28877), -- Résistance aux arcanes
(10, 28730), -- Torrent arcanique
(10, 822),   -- Affinité avec la magie
-- Draeneï
(11, 59221), -- Affinité avec les gemmes
(11, 28875), -- Présence héroïque
(11, 59545); -- Don des Naaru

-- Ajouter les sorts raciaux pour toutes les classes si manquants
INSERT IGNORE INTO `playercreateinfo_spell` (race, class, Spell, Note)
SELECT tmp.race_id, pc.class, tmp.spell_id, 'Racial Spell Fix'
FROM `playercreateinfo` pc
JOIN tmp_racial_spells tmp ON pc.race = tmp.race_id;

-- ====================================================================
-- 2. Ajouter les sorts raciaux aux barres d'action
-- ====================================================================

-- Vérifier quels sorts raciaux actifs ne sont pas sur la barre d'action
-- Seulement pour quelques sorts raciaux importants qui sont activables

-- Pour chaque race, ajouter les sorts raciaux clés aux barres d'action si manquants

-- Humains: Perception (20598)
INSERT IGNORE INTO `playercreateinfo_action` (race, class, button, action, type)
SELECT 1, pc.class, 11, 20598, 0
FROM `playercreateinfo` pc
WHERE pc.race = 1;

-- Orcs: Rage sanguinaire (20574)
INSERT IGNORE INTO `playercreateinfo_action` (race, class, button, action, type)
SELECT 2, pc.class, 11, 20574, 0
FROM `playercreateinfo` pc
WHERE pc.race = 2;

-- Nains: Forme de pierre (20594)
INSERT IGNORE INTO `playercreateinfo_action` (race, class, button, action, type)
SELECT 3, pc.class, 11, 20594, 0
FROM `playercreateinfo` pc
WHERE pc.race = 3;

-- Elfes de la nuit: Camouflage dans l'ombre (20583)
INSERT IGNORE INTO `playercreateinfo_action` (race, class, button, action, type)
SELECT 4, pc.class, 11, 20583, 0
FROM `playercreateinfo` pc
WHERE pc.race = 4;

-- Morts-vivants: Volonté des Réprouvés (20579)
INSERT IGNORE INTO `playercreateinfo_action` (race, class, button, action, type)
SELECT 5, pc.class, 11, 20579, 0
FROM `playercreateinfo` pc
WHERE pc.race = 5;

-- Taurens: Choc martial (20549)
INSERT IGNORE INTO `playercreateinfo_action` (race, class, button, action, type)
SELECT 6, pc.class, 11, 20549, 0
FROM `playercreateinfo` pc
WHERE pc.race = 6;

-- Gnomes: Évasion (20589)
INSERT IGNORE INTO `playercreateinfo_action` (race, class, button, action, type)
SELECT 7, pc.class, 11, 20589, 0
FROM `playercreateinfo` pc
WHERE pc.race = 7;

-- Trolls: Berserker (20557)
INSERT IGNORE INTO `playercreateinfo_action` (race, class, button, action, type)
SELECT 8, pc.class, 11, 20557, 0
FROM `playercreateinfo` pc
WHERE pc.race = 8;

-- Elfes de sang: Torrent arcanique (28730)
INSERT IGNORE INTO `playercreateinfo_action` (race, class, button, action, type)
SELECT 10, pc.class, 11, 28730, 0
FROM `playercreateinfo` pc
WHERE pc.race = 10;

-- Draeneï: Don des Naaru (59545)
INSERT IGNORE INTO `playercreateinfo_action` (race, class, button, action, type)
SELECT 11, pc.class, 11, 59545, 0
FROM `playercreateinfo` pc
WHERE pc.race = 11;

-- ====================================================================
-- 3. Corrections pour les combinaisons problématiques
-- ====================================================================

-- 3.1 Combinaisons avec des ressources incompatibles (ex: races avec mana et classes sans mana)

-- Adapter les tips pour le Torrent arcanique des elfes de sang guerriers et voleurs
-- qui n'ont pas de mana, mais pourraient régénérer de la rage ou de l'énergie
UPDATE `playercreateinfo_action` 
SET action = 28734, type = 0
WHERE race = 10 AND class IN (1, 4) AND action = 28730;

-- Insérer le nouveau sort pour remplacer le Torrent arcanique pour les guerriers
INSERT IGNORE INTO `spell_dbc` (`ID`, `Name`, `Description`, `ToolTip`, `SchoolMask`, `SpellDuration`, `TargetAuraState`, `ManaCost`, `ManaCostPercentage`, `BaseLevel`, `MaxLevel`, `DamageClass`, `ProcFlags`, `ProcChance`)
SELECT 28734, 'Torrent de Puissance', 'Absorbe l\'énergie environnante, restaurant 6% de votre rage ou énergie et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.', 'Absorbe l\'énergie environnante, restaurant 6% de votre rage ou énergie et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.', 64, 25, 0, 0, 0, 0, 0, 0, 0, 101
FROM `spell_dbc` LIMIT 1
WHERE NOT EXISTS (SELECT 1 FROM `spell_dbc` WHERE `ID` = 28734);

-- 3.2 Correction pour les morts-vivants paladins (conflit thématique)
-- Ajout d'un sort racial unique pour les morts-vivants paladins
INSERT IGNORE INTO `playercreateinfo_spell` (race, class, Spell, Note)
VALUES (5, 2, 28730, 'Torrent d\'ombre'); -- Réutilisation de l'ID du torrent arcanique

-- Mise à jour du nom et de la description du sort pour cette combinaison unique
INSERT IGNORE INTO `spell_dbc` (`ID`, `Name`, `Description`, `ToolTip`, `SchoolMask`, `SpellDuration`, `TargetAuraState`, `ManaCost`, `ManaCostPercentage`, `BaseLevel`, `MaxLevel`, `DamageClass`, `ProcFlags`, `ProcChance`)
SELECT 28735, 'Torrent d\'ombre', 'Canalise l\'énergie des ombres, restaurant 6% de votre mana et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.', 'Canalise l\'énergie des ombres, restaurant 6% de votre mana et réduisant la chance d\'échec des sorts de tous les ennemis proches de 3% pendant 15 sec.', 32, 25, 0, 0, 0, 0, 0, 0, 0, 101
FROM `spell_dbc` LIMIT 1
WHERE NOT EXISTS (SELECT 1 FROM `spell_dbc` WHERE `ID` = 28735);

-- 3.3 Correction pour les sorts d'affinité raciale (magie, forge, etc.)
-- S'assurer que tous les personnages ont les bons bonus de compétence

-- Elfes de sang: Affinité avec la magie (+10 Enchantement)
INSERT IGNORE INTO `playercreateinfo_skill` (race, class, skill, rank, comment)
SELECT 10, pc.class, 333, 0, 'Racial - Enchanting Skill'
FROM `playercreateinfo` pc
WHERE pc.race = 10;

-- Draeneï: Affinité avec les gemmes (+10 Joaillerie)
INSERT IGNORE INTO `playercreateinfo_skill` (race, class, skill, rank, comment)
SELECT 11, pc.class, 755, 0, 'Racial - Jewelcrafting Skill'
FROM `playercreateinfo` pc
WHERE pc.race = 11;

-- Nains: +5 Forge
INSERT IGNORE INTO `playercreateinfo_skill` (race, class, skill, rank, comment)
SELECT 3, pc.class, 164, 0, 'Racial - Blacksmithing Skill'
FROM `playercreateinfo` pc
WHERE pc.race = 3;

-- Nettoyage final
DROP TEMPORARY TABLE tmp_racial_spells;

-- Supprimer les doublons potentiels de sorts raciaux
DELETE a FROM `playercreateinfo_spell` a
INNER JOIN (
    SELECT MIN(ps.id) as min_id, ps.race, ps.class, ps.Spell
    FROM `playercreateinfo_spell` ps
    GROUP BY ps.race, ps.class, ps.Spell
    HAVING COUNT(*) > 1
) b ON a.race = b.race AND a.class = b.class AND a.Spell = b.Spell
WHERE a.id > b.min_id;
