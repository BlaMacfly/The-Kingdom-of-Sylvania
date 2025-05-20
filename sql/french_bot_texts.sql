-- Script SQL pour ajouter des textes en français pour les bots du serveur The Kingdom of Sylvania
-- Ce script modifie la base de données playerbots pour que les bots parlent en français

-- Créer les tables si elles n'existent pas
CREATE TABLE IF NOT EXISTS `playerbot_texts` (
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `say_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `reply_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `locale_frFR` text NOT NULL,
  PRIMARY KEY (`name`,`text`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `playerbot_texts_chance` (
  `name` varchar(255) NOT NULL,
  `probability` int(10) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vider les tables existantes pour éviter les doublons
DELETE FROM `playerbot_texts` WHERE `locale_frFR` != '';

-- Ajouter des textes en français pour les situations courantes
INSERT INTO `playerbot_texts` (`name`, `text`, `say_type`, `reply_type`, `locale_frFR`) VALUES
-- Textes de salutation
('hello', 'Hello!', 0, 0, 'Salut !'),
('hello', 'Hi there!', 0, 0, 'Bonjour !'),
('hello', 'Greetings!', 0, 0, 'Salutations !'),
('hello', 'Hey!', 0, 0, 'Hé !'),
('hello', 'Good day!', 0, 0, 'Bonne journée !'),

-- Textes de combat
('critical health', 'Help! I\'m dying!', 0, 0, 'À l\'aide ! Je meurs !'),
('critical health', 'Need healing now!', 0, 0, 'Besoin de soins immédiatement !'),
('critical health', 'I\'m almost dead!', 0, 0, 'Je suis presque mort !'),
('low health', 'I need healing!', 0, 0, 'J\'ai besoin de soins !'),
('low health', 'Can I get a heal?', 0, 0, 'Quelqu\'un peut me soigner ?'),
('low health', 'Heal me please!', 0, 0, 'Soignez-moi s\'il vous plaît !'),
('low mana', 'I\'m low on mana!', 0, 0, 'Je manque de mana !'),
('low mana', 'I need to drink!', 0, 0, 'Je dois boire !'),
('low mana', 'Out of mana soon!', 0, 0, 'Bientôt à court de mana !'),
('taunt', 'I\'ll tank them!', 0, 0, 'Je vais les tanker !'),
('taunt', 'Come to me!', 0, 0, 'Venez à moi !'),
('taunt', 'I\'ll hold them!', 0, 0, 'Je vais les retenir !'),
('aoe', 'AoE incoming!', 0, 0, 'AoE en approche !'),
('aoe', 'Watch out for AoE!', 0, 0, 'Attention à l\'AoE !'),
('aoe', 'Area attack!', 0, 0, 'Attaque de zone !'),

-- Textes de quête
('quest', 'Let\'s do this quest!', 0, 0, 'Faisons cette quête !'),
('quest', 'I need to complete this quest.', 0, 0, 'Je dois terminer cette quête.'),
('quest', 'Anyone doing this quest?', 0, 0, 'Quelqu\'un fait cette quête ?'),

-- Textes de groupe
('lfg', 'Looking for group!', 0, 0, 'Recherche un groupe !'),
('lfg', 'Anyone need a <class> for dungeons?', 0, 0, 'Quelqu\'un a besoin d\'un <class> pour des donjons ?'),
('lfg', 'LFG <subzone>', 0, 0, 'RDG <subzone>'),
('lfg', 'LFG for quests in <subzone>', 0, 0, 'RDG pour quêtes à <subzone>'),

-- Textes de remerciement
('thanks', 'Thanks!', 0, 0, 'Merci !'),
('thanks', 'Thank you!', 0, 0, 'Merci beaucoup !'),
('thanks', 'Much appreciated!', 0, 0, 'Très apprécié !'),
('thanks', 'You\'re the best!', 0, 0, 'Vous êtes le meilleur !'),

-- Textes de félicitation
('congrats', 'Congratulations!', 0, 0, 'Félicitations !'),
('congrats', 'Well done!', 0, 0, 'Bien joué !'),
('congrats', 'Great job!', 0, 0, 'Excellent travail !'),

-- Textes de salutation pour les joueurs
('greet', 'Hello there, <name>!', 0, 0, 'Bonjour, <name> !'),
('greet', 'Nice to see you, <name>!', 0, 0, 'Ravi de te voir, <name> !'),
('greet', 'Greetings, <name>!', 0, 0, 'Salutations, <name> !'),

-- Textes d'adieu
('goodbye', 'Goodbye!', 0, 0, 'Au revoir !'),
('goodbye', 'See you later!', 0, 0, 'À plus tard !'),
('goodbye', 'Farewell!', 0, 0, 'Adieu !'),
('goodbye', 'Until next time!', 0, 0, 'À la prochaine !'),

-- Textes de combat spécifiques aux classes
('warrior', 'For the <randomfaction>!', 0, 0, 'Pour la <randomfaction> !'),
('warrior', 'Charge!', 0, 0, 'À l\'attaque !'),
('paladin', 'Light give me strength!', 0, 0, 'Que la Lumière me donne de la force !'),
('paladin', 'By the Light!', 0, 0, 'Par la Lumière !'),
('hunter', 'I\'ve got <target> in my sights!', 0, 0, 'J\'ai <target> dans ma ligne de mire !'),
('hunter', 'My pet will tear you apart!', 0, 0, 'Mon familier va te déchirer !'),
('rogue', 'You\'ll never see me coming!', 0, 0, 'Tu ne me verras jamais venir !'),
('rogue', 'Stabby stabby!', 0, 0, 'Coup de poignard !'),
('priest', 'The Light will heal you!', 0, 0, 'La Lumière va vous guérir !'),
('priest', 'Your wounds shall be mended!', 0, 0, 'Vos blessures seront soignées !'),
('shaman', 'Elements, aid me!', 0, 0, 'Éléments, aidez-moi !'),
('shaman', 'Storm, Earth and Fire!', 0, 0, 'Tempête, Terre et Feu !'),
('mage', 'Feel the power of the arcane!', 0, 0, 'Ressentez la puissance des arcanes !'),
('mage', 'I\'ll turn you into a sheep!', 0, 0, 'Je vais te transformer en mouton !'),
('warlock', 'Your soul will be mine!', 0, 0, 'Ton âme sera mienne !'),
('warlock', 'Fear me!', 0, 0, 'Crains-moi !'),
('druid', 'Nature\'s wrath upon you!', 0, 0, 'Que la colère de la nature s\'abatte sur toi !'),
('druid', 'The wild calls!', 0, 0, 'La nature m\'appelle !'),
('death knight', 'Suffer!', 0, 0, 'Souffre !'),
('death knight', 'Your end has come!', 0, 0, 'Ta fin est venue !'),

-- Textes liés aux enchantements aléatoires (mod-random-enchants)
('random enchant', 'Look at my awesome enchanted gear!', 0, 0, 'Regardez mon équipement avec des enchantements incroyables !'),
('random enchant', 'I got a great random enchant!', 0, 0, 'J\'ai obtenu un super enchantement aléatoire !'),
('random enchant', 'This enchant makes me so powerful!', 0, 0, 'Cet enchantement me rend si puissant !'),

-- Textes liés au module de transmogrification (mod-transmog)
('transmog', 'How do you like my outfit?', 0, 0, 'Comment trouvez-vous ma tenue ?'),
('transmog', 'I just transmogged my gear!', 0, 0, 'Je viens de transmogrifier mon équipement !'),
('transmog', 'My armor looks amazing now!', 0, 0, 'Mon armure a l\'air incroyable maintenant !'),

-- Textes liés au module solocraft (mod-solocraft)
('solocraft', 'I can solo this dungeon!', 0, 0, 'Je peux faire ce donjon en solo !'),
('solocraft', 'Who needs a group when you\'re this strong?', 0, 0, 'Qui a besoin d\'un groupe quand on est si fort ?'),
('solocraft', 'Dungeons are easy with these buffs!', 0, 0, 'Les donjons sont faciles avec ces améliorations !');

-- Ajouter des probabilités pour les textes
INSERT INTO `playerbot_texts_chance` (`name`, `probability`) VALUES
('hello', 100),
('critical health', 90),
('low health', 70),
('low mana', 70),
('taunt', 80),
('aoe', 80),
('quest', 50),
('lfg', 60),
('thanks', 90),
('congrats', 90),
('greet', 80),
('goodbye', 70),
('warrior', 40),
('paladin', 40),
('hunter', 40),
('rogue', 40),
('priest', 40),
('shaman', 40),
('mage', 40),
('warlock', 40),
('druid', 40),
('death knight', 40),
('random enchant', 30),
('transmog', 30),
('solocraft', 30);
