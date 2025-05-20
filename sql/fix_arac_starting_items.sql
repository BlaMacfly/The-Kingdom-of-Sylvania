-- Script pour corriger les objets de départ pour ARAC
-- Ce script s'assure que toutes les combinaisons race/classe ont les objets appropriés

USE `acore_world`;

-- 1. Humains (race=1)
-- Pour chaque classe, copier les objets de départ du guerrier (class=1)
INSERT IGNORE INTO `playercreateinfo_item` (race, class, itemid, amount)
SELECT 1, target_class.class, source.itemid, source.amount
FROM `playercreateinfo_item` source
CROSS JOIN (
    SELECT DISTINCT class FROM `playercreateinfo` WHERE race = 1 AND class NOT IN (1, 2, 4, 5, 8, 9)
) target_class
WHERE source.race = 1 AND source.class = 1;

-- 2. Orcs (race=2)
-- Pour chaque classe, copier les objets de départ du guerrier (class=1)
INSERT IGNORE INTO `playercreateinfo_item` (race, class, itemid, amount)
SELECT 2, target_class.class, source.itemid, source.amount
FROM `playercreateinfo_item` source
CROSS JOIN (
    SELECT DISTINCT class FROM `playercreateinfo` WHERE race = 2 AND class NOT IN (1, 3, 4, 7, 9)
) target_class
WHERE source.race = 2 AND source.class = 1;

-- 3. Nains (race=3)
-- Pour chaque classe, copier les objets de départ du guerrier (class=1)
INSERT IGNORE INTO `playercreateinfo_item` (race, class, itemid, amount)
SELECT 3, target_class.class, source.itemid, source.amount
FROM `playercreateinfo_item` source
CROSS JOIN (
    SELECT DISTINCT class FROM `playercreateinfo` WHERE race = 3 AND class NOT IN (1, 2, 3, 4, 5, 8)
) target_class
WHERE source.race = 3 AND source.class = 1;

-- 4. Elfes de la nuit (race=4)
-- Pour chaque classe, copier les objets de départ du druide (class=11)
INSERT IGNORE INTO `playercreateinfo_item` (race, class, itemid, amount)
SELECT 4, target_class.class, source.itemid, source.amount
FROM `playercreateinfo_item` source
CROSS JOIN (
    SELECT DISTINCT class FROM `playercreateinfo` WHERE race = 4 AND class NOT IN (1, 3, 4, 5, 11)
) target_class
WHERE source.race = 4 AND source.class = 11;

-- 5. Morts-vivants (race=5)
-- Pour chaque classe, copier les objets de départ du guerrier (class=1)
INSERT IGNORE INTO `playercreateinfo_item` (race, class, itemid, amount)
SELECT 5, target_class.class, source.itemid, source.amount
FROM `playercreateinfo_item` source
CROSS JOIN (
    SELECT DISTINCT class FROM `playercreateinfo` WHERE race = 5 AND class NOT IN (1, 4, 5, 8, 9)
) target_class
WHERE source.race = 5 AND source.class = 1;

-- 6. Taurens (race=6)
-- Pour chaque classe, copier les objets de départ du druide (class=11)
INSERT IGNORE INTO `playercreateinfo_item` (race, class, itemid, amount)
SELECT 6, target_class.class, source.itemid, source.amount
FROM `playercreateinfo_item` source
CROSS JOIN (
    SELECT DISTINCT class FROM `playercreateinfo` WHERE race = 6 AND class NOT IN (1, 3, 7, 11)
) target_class
WHERE source.race = 6 AND source.class = 11;

-- 7. Gnomes (race=7)
-- Pour chaque classe, copier les objets de départ du mage (class=8)
INSERT IGNORE INTO `playercreateinfo_item` (race, class, itemid, amount)
SELECT 7, target_class.class, source.itemid, source.amount
FROM `playercreateinfo_item` source
CROSS JOIN (
    SELECT DISTINCT class FROM `playercreateinfo` WHERE race = 7 AND class NOT IN (1, 4, 8, 9)
) target_class
WHERE source.race = 7 AND source.class = 8;

-- 8. Trolls (race=8)
-- Pour chaque classe, copier les objets de départ du chasseur (class=3)
INSERT IGNORE INTO `playercreateinfo_item` (race, class, itemid, amount)
SELECT 8, target_class.class, source.itemid, source.amount
FROM `playercreateinfo_item` source
CROSS JOIN (
    SELECT DISTINCT class FROM `playercreateinfo` WHERE race = 8 AND class NOT IN (1, 3, 4, 5, 7, 8)
) target_class
WHERE source.race = 8 AND source.class = 3;

-- 9. Elfes de sang (race=10)
-- Pour chaque classe, copier les objets de départ du paladin (class=2)
INSERT IGNORE INTO `playercreateinfo_item` (race, class, itemid, amount)
SELECT 10, target_class.class, source.itemid, source.amount
FROM `playercreateinfo_item` source
CROSS JOIN (
    SELECT DISTINCT class FROM `playercreateinfo` WHERE race = 10 AND class NOT IN (2, 3, 4, 8, 9)
) target_class
WHERE source.race = 10 AND source.class = 2;

-- 10. Draeneï (race=11)
-- Pour chaque classe, copier les objets de départ du paladin (class=2)
INSERT IGNORE INTO `playercreateinfo_item` (race, class, itemid, amount)
SELECT 11, target_class.class, source.itemid, source.amount
FROM `playercreateinfo_item` source
CROSS JOIN (
    SELECT DISTINCT class FROM `playercreateinfo` WHERE race = 11 AND class NOT IN (2, 3, 4, 8, 9)
) target_class
WHERE source.race = 11 AND source.class = 2;
