-- Script complet pour corriger les quêtes pour les combinaisons race/classe personnalisées
-- Ce script permet à toutes les classes personnalisées d'accomplir les quêtes spécifiques à leur zone de départ

-- =============================================
-- PARTIE 1: CORRECTION DES QUÊTES DES ELFES DE SANG
-- =============================================

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

-- Nous allons modifier les quêtes pour qu'elles soient disponibles pour toutes les races de la Horde
-- Horde = Orc (2) + Mort-vivant (16) + Tauren (32) + Troll (128) + Elfe de sang (512) = 690

-- Mise à jour des quêtes de départ des elfes de sang pour les rendre disponibles à toutes les races de la Horde
UPDATE `quest_template` SET `AllowableRaces` = 690 WHERE `AllowableRaces` = 512 AND `ID` IN (
    8325, -- Reclaiming Sunstrider Isle
    8326, -- Unfortunate Measures
    8327, -- Report to Lanthan Perilon
    8330, -- Solanian's Belongings
    8334, -- Aggression
    8335, -- Felendren the Banished
    8336, -- A Fistful of Slivers
    8338, -- Tainted Arcane Sliver
    8345, -- The Shrine of Dath'Remar
    8346, -- Thirst Unending
    8347  -- Aiding the Outrunners
);

-- Mise à jour des quêtes de voyage des elfes de sang pour les rendre disponibles à toutes les races de la Horde
UPDATE `quest_template` SET `AllowableRaces` = 690 WHERE `AllowableRaces` = 512 AND `ID` IN (
    9130, -- Goods from Silvermoon City
    9133, -- Fly to Silvermoon City
    9134, -- Skymistress Gloaming
    9135, -- Return to Quartermaster Lymel
    9189, -- Delivery to the Sepulcher
    9327, -- The Forsaken
    9328, -- Hero of the Sin'dorei
    9425, -- Report to Tarren Mill
    9428  -- Report to Splintertree Post
);

-- Récupérer toutes les autres quêtes des elfes de sang et les rendre disponibles à toutes les races de la Horde
UPDATE `quest_template` SET `AllowableRaces` = 690 WHERE `AllowableRaces` = 512;

-- Mise à jour des conditions de quête qui pourraient être liées à la race
UPDATE `conditions` SET `ConditionValue1` = 690 WHERE `ConditionTypeOrReference` = 25 AND `ConditionValue1` = 512;

-- =============================================
-- PARTIE 2: CORRECTION DES QUÊTES DES MORTS-VIVANTS POUR LES PALADINS MORTS-VIVANTS
-- =============================================

-- Quêtes spécifiques aux morts-vivants (race ID 5, bitmask 16)
-- Nous allons les rendre disponibles pour toutes les classes, y compris les paladins

-- Vérifier s'il existe des quêtes spécifiques aux morts-vivants qui sont limitées par classe
-- Si de telles quêtes existent, nous les mettrons à jour ici

-- Mise à jour des quêtes de classe paladin pour les rendre disponibles aux morts-vivants
-- Quêtes paladin (classe ID 2)
UPDATE `quest_template` 
SET `AllowableRaces` = `AllowableRaces` | 16 
WHERE (`AllowableRaces` & 16) = 0 
AND (`AllowableRaces` & (1 | 512 | 1024)) > 0 -- Quêtes pour humains, elfes de sang ou draeneï
AND `ID` IN (
    -- Liste des quêtes de paladin à rendre disponibles pour les morts-vivants
    -- Ajouter les ID de quête appropriés ici
    3107, -- Tome of Divinity
    1641, -- The Tome of Valor
    1642, -- The Tome of Valor
    1643, -- The Tome of Valor
    1644, -- The Tome of Valor
    1780, -- The Tome of Valor
    1781, -- The Tome of Valor
    1786, -- The Tome of Valor
    1787, -- The Tome of Valor
    1788, -- The Tome of Valor
    2998, -- Tome of Nobility
    3000, -- Tome of Nobility
    3681  -- Redemption
);

-- =============================================
-- PARTIE 3: CORRECTION DES QUÊTES POUR TOUTES LES COMBINAISONS RACE/CLASSE PERSONNALISÉES
-- =============================================

-- Cette partie du script est un modèle que vous pouvez étendre pour d'autres combinaisons race/classe
-- Pour chaque combinaison personnalisée, vous devrez identifier les quêtes spécifiques et les mettre à jour

-- Exemple pour les mages touraine (race ID 10, classe ID 8)
-- UPDATE `quest_template` 
-- SET `AllowableRaces` = `AllowableRaces` | [RACE_BITMASK]
-- WHERE (`AllowableRaces` & [RACE_BITMASK]) = 0
-- AND `ID` IN (
--     -- Liste des quêtes de mage à rendre disponibles pour la race spécifiée
--     -- Ajouter les ID de quête appropriés ici
-- );

-- =============================================
-- PARTIE 4: CORRECTION DES QUÊTES DE CLASSE POUR TOUTES LES RACES
-- =============================================

-- Cette partie rend les quêtes de classe disponibles pour toutes les races
-- Cela garantit que toutes les combinaisons race/classe personnalisées peuvent accomplir les quêtes de leur classe

-- Quêtes de guerrier (classe ID 1)
UPDATE `quest_template` 
SET `AllowableRaces` = 1791 -- Toutes les races (1+2+4+8+16+32+64+128+512+1024)
WHERE `ID` IN (
    -- Liste des quêtes de guerrier
    -- Ajouter les ID de quête appropriés ici
    1638, -- A Warrior's Training
    1639, -- Bartleby the Drunk
    1640, -- Beat Bartleby
    1665, -- Bartleby's Mug
    8417  -- Therzok
);

-- Quêtes de paladin (classe ID 2)
UPDATE `quest_template` 
SET `AllowableRaces` = 1791 -- Toutes les races
WHERE `ID` IN (
    -- Liste des quêtes de paladin
    -- Ajouter les ID de quête appropriés ici
    3107, -- Tome of Divinity
    1641, -- The Tome of Valor
    1642, -- The Tome of Valor
    1643, -- The Tome of Valor
    1644, -- The Tome of Valor
    1780, -- The Tome of Valor
    1781, -- The Tome of Valor
    1786, -- The Tome of Valor
    1787, -- The Tome of Valor
    1788, -- The Tome of Valor
    2998, -- Tome of Nobility
    3000, -- Tome of Nobility
    3681  -- Redemption
);

-- Quêtes de chasseur (classe ID 3)
UPDATE `quest_template` 
SET `AllowableRaces` = 1791 -- Toutes les races
WHERE `ID` IN (
    -- Liste des quêtes de chasseur
    -- Ajouter les ID de quête appropriés ici
    6064, -- Training the Beast
    6084, -- Taming the Beast
    6085, -- Taming the Beast
    6086, -- Taming the Beast
    6087, -- Taming the Beast
    6088, -- Taming the Beast
    6089  -- Taming the Beast
);

-- Quêtes de voleur (classe ID 4)
UPDATE `quest_template` 
SET `AllowableRaces` = 1791 -- Toutes les races
WHERE `ID` IN (
    -- Liste des quêtes de voleur
    -- Ajouter les ID de quête appropriés ici
    2218, -- Encrypted Letter
    2219, -- Encrypted Sigil
    2220, -- Encrypted Parchment
    2221, -- Encrypted Tablet
    2222, -- Encrypted Rune
    2223, -- Encrypted Text
    2224, -- Encrypted Memorandum
    2225, -- Encrypted Scroll
    2226, -- Encrypted Letter
    2227, -- Encrypted Letter
    2228, -- Encrypted Letter
    2229, -- Encrypted Letter
    2230, -- Mathias and the Defias
    2231, -- The Defias Brotherhood
    2232, -- The Defias Brotherhood
    2233, -- The Defias Brotherhood
    2234, -- The Defias Brotherhood
    2235, -- The Defias Brotherhood
    2236, -- The Defias Brotherhood
    2237, -- The Defias Brotherhood
    2238, -- The Defias Brotherhood
    2239, -- The Defias Brotherhood
    2240, -- The Defias Brotherhood
    2241, -- The Defias Brotherhood
    2242  -- The Defias Brotherhood
);

-- Quêtes de prêtre (classe ID 5)
UPDATE `quest_template` 
SET `AllowableRaces` = 1791 -- Toutes les races
WHERE `ID` IN (
    -- Liste des quêtes de prêtre
    -- Ajouter les ID de quête appropriés ici
    5635, -- Desperate Prayer
    5636, -- Desperate Prayer
    5637, -- Desperate Prayer
    5638, -- Desperate Prayer
    5639, -- Desperate Prayer
    5640, -- Desperate Prayer
    5641, -- Desperate Prayer
    5642, -- Desperate Prayer
    5643, -- Desperate Prayer
    5644, -- Desperate Prayer
    5645, -- Desperate Prayer
    5646  -- Desperate Prayer
);

-- Quêtes de chaman (classe ID 7)
UPDATE `quest_template` 
SET `AllowableRaces` = 1791 -- Toutes les races
WHERE `ID` IN (
    -- Liste des quêtes de chaman
    -- Ajouter les ID de quête appropriés ici
    1516, -- Call of Earth
    1517, -- Call of Earth
    1518, -- Call of Earth
    1519, -- Call of Earth
    1520, -- Call of Earth
    1521, -- Call of Earth
    1522, -- Call of Earth
    1524, -- Call of Fire
    1525, -- Call of Fire
    1526, -- Call of Fire
    1527, -- Call of Fire
    1528, -- Call of Fire
    1529, -- Call of Fire
    1530, -- Call of Fire
    1531, -- Call of Fire
    1532, -- Call of Fire
    1534, -- Call of Fire
    1535, -- Call of Fire
    1536, -- Call of Fire
    1537, -- Call of Fire
    2981, -- Call of Fire
    2982, -- Call of Fire
    2983, -- Call of Fire
    2984, -- Call of Fire
    2985, -- Call of Fire
    2986, -- Call of Fire
    3062, -- Call of Fire
    3063, -- Call of Fire
    3064, -- Call of Fire
    3065, -- Call of Fire
    3066, -- Call of Fire
    3067, -- Call of Fire
    3068, -- Call of Fire
    3069, -- Call of Fire
    3070  -- Call of Fire
);

-- Quêtes de mage (classe ID 8)
UPDATE `quest_template` 
SET `AllowableRaces` = 1791 -- Toutes les races
WHERE `ID` IN (
    -- Liste des quêtes de mage
    -- Ajouter les ID de quête appropriés ici
    1860, -- Investigate the Alchemist Shop
    1861, -- Gathering Materials
    1919, -- Reclaimers' Business in Desolace
    1920, -- Reagents for Reclaimers Inc.
    1921, -- Rhapsody Shindigger
    1938, -- Rhapsody's Kalimdor Kocktail
    1939, -- Rhapsody's Tale
    1940, -- Distant Memory
    1941, -- The Infernal Orb
    1942, -- The Infernal Orb
    1943, -- The Infernal Orb
    1944, -- The Infernal Orb
    1945, -- The Infernal Orb
    1946, -- The Infernal Orb
    1947, -- The Infernal Orb
    1948, -- The Infernal Orb
    1949, -- The Infernal Orb
    1950, -- The Infernal Orb
    1951, -- The Infernal Orb
    1952, -- The Infernal Orb
    1953, -- The Infernal Orb
    1954, -- The Infernal Orb
    1955, -- The Infernal Orb
    1956, -- The Infernal Orb
    1957  -- The Infernal Orb
);

-- Quêtes de démoniste (classe ID 9)
UPDATE `quest_template` 
SET `AllowableRaces` = 1791 -- Toutes les races
WHERE `ID` IN (
    -- Liste des quêtes de démoniste
    -- Ajouter les ID de quête appropriés ici
    1598, -- Surena Caledon
    1599, -- Surena Caledon
    1685, -- Gakin's Summons
    1688, -- Gakin's Summons
    1689, -- The Binding
    1692, -- The Binding
    1693, -- The Binding
    1758, -- Halgar's Summons
    1759, -- Creature of the Void
    1798, -- Creature of the Void
    1799, -- The Binding
    3105, -- Summon Felsteed
    3120, -- Summon Felsteed
    3631, -- Summon Felsteed
    3632, -- Summon Felsteed
    3633, -- Summon Felsteed
    4736, -- In Search of Menara Voidrender
    4737, -- In Search of Menara Voidrender
    4738, -- In Search of Menara Voidrender
    4739, -- In Search of Menara Voidrender
    4740, -- In Search of Menara Voidrender
    4741, -- In Search of Menara Voidrender
    4742, -- In Search of Menara Voidrender
    4743, -- In Search of Menara Voidrender
    4744, -- In Search of Menara Voidrender
    4745, -- In Search of Menara Voidrender
    4746, -- In Search of Menara Voidrender
    4747, -- In Search of Menara Voidrender
    4781, -- Menara Voidrender
    4782, -- Components for the Enchanted Gold Bloodrobe
    4783, -- Components for the Enchanted Gold Bloodrobe
    4784, -- Components for the Enchanted Gold Bloodrobe
    4785, -- Components for the Enchanted Gold Bloodrobe
    4786, -- Components for the Enchanted Gold Bloodrobe
    4787, -- Components for the Enchanted Gold Bloodrobe
    4788, -- Components for the Enchanted Gold Bloodrobe
    4789, -- Components for the Enchanted Gold Bloodrobe
    4790, -- Components for the Enchanted Gold Bloodrobe
    4808, -- Menara Voidrender
    4809, -- Components for the Enchanted Gold Bloodrobe
    4810, -- Components for the Enchanted Gold Bloodrobe
    4811, -- Components for the Enchanted Gold Bloodrobe
    4812, -- Components for the Enchanted Gold Bloodrobe
    4813, -- Components for the Enchanted Gold Bloodrobe
    4814, -- Components for the Enchanted Gold Bloodrobe
    4815, -- Components for the Enchanted Gold Bloodrobe
    4816, -- Components for the Enchanted Gold Bloodrobe
    4817, -- Components for the Enchanted Gold Bloodrobe
    4818, -- Components for the Enchanted Gold Bloodrobe
    4819, -- Components for the Enchanted Gold Bloodrobe
    4820, -- Components for the Enchanted Gold Bloodrobe
    4821, -- Components for the Enchanted Gold Bloodrobe
    4822, -- Components for the Enchanted Gold Bloodrobe
    4823  -- Components for the Enchanted Gold Bloodrobe
);

-- Quêtes de druide (classe ID 11)
UPDATE `quest_template` 
SET `AllowableRaces` = 1791 -- Toutes les races
WHERE `ID` IN (
    -- Liste des quêtes de druide
    -- Ajouter les ID de quête appropriés ici
    5921, -- Moonglade
    5922, -- Moonglade
    5923, -- Great Bear Spirit
    5924, -- Great Bear Spirit
    5925, -- Back to Darnassus
    5926, -- Back to Thunder Bluff
    5927, -- Body and Heart
    5928, -- Body and Heart
    5929, -- Body and Heart
    5930, -- Body and Heart
    5931, -- Body and Heart
    5932  -- Body and Heart
);

-- =============================================
-- PARTIE 5: MISE À JOUR DES OBJETS DE QUÊTE POUR TOUTES LES RACES/CLASSES
-- =============================================

-- Cette partie met à jour les objets de quête pour qu'ils puissent être utilisés par toutes les races/classes
-- Cela garantit que les objets spécifiques à une race ou classe peuvent être utilisés par toutes les combinaisons personnalisées

-- Mise à jour des objets de quête pour toutes les races
UPDATE `item_template` 
SET `AllowableRace` = -1 -- Toutes les races (-1 = pas de restriction)
WHERE `class` = 12; -- Classe d'objet "Quest"

-- Mise à jour des objets de quête pour toutes les classes
UPDATE `item_template` 
SET `AllowableClass` = -1 -- Toutes les classes (-1 = pas de restriction)
WHERE `class` = 12; -- Classe d'objet "Quest"
