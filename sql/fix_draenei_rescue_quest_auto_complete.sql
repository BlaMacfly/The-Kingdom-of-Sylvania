-- Script pour rendre la quête "Rescue the Survivors!" (ID 9283) automatiquement validée
-- Cette modification permet aux races qui n'ont pas le sort racial "Don des Naaru" de compléter la quête

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
    `QuestDescription` = 'Les cristaux de soins sont reconstitués, mais ce n\'est pas la seule façon dont nous pouvons aider les blessés. Les draeneï peuvent utiliser le don des Naaru pour guérir, mais d\'autres races doivent trouver des moyens alternatifs pour aider.$b$bLes survivants ont souffert de l\'exposition aux cristaux de puissance irradiés. Votre présence et votre volonté d\'aider sont déjà un grand réconfort pour eux.$B$BParlez-moi pour confirmer votre engagement à aider les survivants.',
    `LogDescription` = 'Parlez à Prêtresse Kyleen Il\'dinare pour confirmer votre engagement à aider les survivants.',
    `QuestCompletionLog` = 'Retournez voir Prêtresse Kyleen Il\'dinare à l\'Île Brume-Azur.'
WHERE `ID` = 9283;

-- 2. Supprimer toutes les conditions spécifiques à cette quête
DELETE FROM `conditions` WHERE `SourceEntry` = 9283;

-- 3. S'assurer que la quête est disponible pour toutes les races de l'Alliance
UPDATE `quest_template` 
SET `AllowableRaces` = 1101 -- Toutes les races de l'Alliance
WHERE `ID` = 9283;
