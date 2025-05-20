-- Script pour rendre la quête "Thirst Unending" (ID 8346) automatiquement validée
-- Cette modification permet aux races qui n'ont pas le sort racial "Torrent arcanique" de compléter la quête

-- 1. Modifier le type de quête pour qu'elle soit automatiquement validée
UPDATE `quest_template` 
SET 
    -- Changer le type de quête en "Parler à" (type 1)
    `QuestType` = 1,
    
    -- Supprimer les objectifs existants
    `RequiredNpcOrGo1` = 0,
    `RequiredNpcOrGoCount1` = 0,
    `ObjectiveText1` = '',
    
    -- Mettre à jour les textes
    `QuestDescription` = 'Si vous ne devez retenir qu\'une seule leçon de votre séjour sur l\'île de Haut-Soleil, que ce soit celle-ci : contrôlez votre soif de magie. C\'est une soif sans fin, $N - ce que vous absorbez doit être contrôlé. Ne pas y parvenir, c\'est devenir l\'un des Flétris... désespérément dépendant et fou.$B$BPour les elfes de sang, le Torrent arcanique est une capacité naturelle qui les aide à contrôler cette soif. Pour les autres races, d\'autres méthodes doivent être employées.$B$BParlez-moi pour confirmer que vous comprenez l\'importance de contrôler votre soif de magie.',
    `LogDescription` = 'Parlez à Magistrix Erona pour confirmer que vous comprenez l\'importance de contrôler votre soif de magie.',
    `QuestCompletionLog` = 'Retournez voir Magistrix Erona à l\'Académie de Haut-Soleil sur l\'île de Haut-Soleil.'
WHERE `ID` = 8346;

-- 2. Supprimer toutes les conditions spécifiques à cette quête
DELETE FROM `conditions` WHERE `SourceEntry` = 8346;

-- 3. S'assurer que la quête est disponible pour toutes les races de la Horde
UPDATE `quest_template` 
SET `AllowableRaces` = 690 -- Toutes les races de la Horde
WHERE `ID` = 8346;
