-- Script SQL pour mettre à jour les textes des bots en français
-- Ce script remplace les textes anglais par les textes français dans la base de données

-- Mettre à jour les textes en copiant les textes français dans la colonne text
UPDATE `playerbot_texts` SET `text` = `locale_frFR` WHERE `locale_frFR` != '';

-- Vérifier que les textes ont bien été mis à jour
SELECT name, text FROM playerbot_texts LIMIT 10;
