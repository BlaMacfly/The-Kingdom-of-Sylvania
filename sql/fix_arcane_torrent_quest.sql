-- Script pour corriger la quête "Thirst Unending" (ID 8346) qui nécessite le sort racial Torrent arcanique des elfes de sang
-- Cette modification permet à la quête de se valider automatiquement sans nécessiter l'utilisation du sort racial

-- 1. Modification des objectifs de la quête pour qu'elle soit automatiquement complétée
-- Nous changeons le type d'objectif pour qu'il ne nécessite plus l'utilisation du sort racial
UPDATE `quest_template` 
SET 
    -- Changer l'objectif pour tuer un Mana Wyrm au lieu d'utiliser Torrent arcanique
    `RequiredNpcOrGo1` = 15468,        -- ID du Mana Wyrm
    `RequiredNpcOrGoCount1` = 1,       -- Tuer 1 Mana Wyrm
    `ObjectiveText1` = 'Mana Wyrm tué' -- Nouveau texte d'objectif
WHERE `ID` = 8346;

-- 2. Mise à jour du texte de la quête pour refléter le changement
UPDATE `quest_template` 
SET 
    `QuestDescription` = 'Si vous ne devez retenir qu\'une seule leçon de votre séjour sur l\'île de Haut-Soleil, que ce soit celle-ci : contrôlez votre soif de magie. C\'est une soif sans fin, $N - ce que vous absorbez doit être contrôlé. Ne pas y parvenir, c\'est devenir l\'un des Flétris... désespérément dépendant et fou.$B$BPour les elfes de sang, le Torrent arcanique est une capacité naturelle qui les aide à contrôler cette soif. Pour les autres races, d\'autres méthodes doivent être employées.$B$BAllez tuer un wyrm de mana pour vous entraîner à contrôler votre pouvoir. Revenez me voir quand vous aurez terminé.',
    `LogDescription` = 'Tuez un wyrm de mana sur l\'île de Haut-Soleil, puis retournez voir Magistrix Erona.',
    `QuestCompletionLog` = 'Retournez voir Magistrix Erona à l\'Académie de Haut-Soleil sur l\'île de Haut-Soleil.'
WHERE `ID` = 8346;

-- 3. Suppression de toute condition spécifique à la race pour cette quête
DELETE FROM `conditions` WHERE `SourceEntry` = 8346 AND `SourceTypeOrReferenceId` = 19;

-- 4. Mise à jour de la chaîne de quêtes pour s'assurer que la progression n'est pas bloquée
-- Vérifier si cette quête est un prérequis pour d'autres quêtes
-- Si c'est le cas, nous nous assurons que les quêtes suivantes sont également disponibles pour toutes les races
UPDATE `quest_template` 
SET `AllowableRaces` = 690 -- Toutes les races de la Horde
WHERE `RewardNextQuest` = 8346 OR `ID` IN (
    -- Rechercher manuellement les quêtes qui ont 8346 comme prérequis
    8347 -- Aiding the Outrunners (quête suivante probable)
);
