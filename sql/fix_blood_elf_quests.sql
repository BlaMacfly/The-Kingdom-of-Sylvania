-- Script pour corriger les quêtes des elfes de sang pour les classes/races personnalisées
-- Ce script permet à toutes les races qui débutent dans la zone des elfes de sang d'accomplir les quêtes des elfes de sang

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
