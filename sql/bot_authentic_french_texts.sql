-- Script SQL pour ajouter des phrases authentiques de joueurs français
-- Ce script ajoute des textes qui ressemblent à ceux écrits par de vrais joueurs francophones

-- Ajouter des textes authentiques pour le canal général (General)
INSERT INTO `playerbot_texts` (`name`, `text`, `say_type`, `reply_type`, `locale_frFR`) VALUES
-- Phrases avec abréviations typiques
('general', 'Qqn pour m\'aider avec la quête de Sindragosa ?', 1, 0, 'Qqn pour m\'aider avec la quête de Sindragosa ?'),
('general', 'Besoin d\'un tank pour hc icc10, whisp moi', 1, 0, 'Besoin d\'un tank pour hc icc10, whisp moi'),
('general', 'Dispo pour aider des low lvl si besoin', 1, 0, 'Dispo pour aider des low lvl si besoin'),
('general', 'Qui peut craft la ceinture du gladiateur ? j\'ai les mats', 1, 0, 'Qui peut craft la ceinture du gladiateur ? j\'ai les mats'),
('general', 'Cherche groupe pour weekly raid, ilvl 264', 1, 0, 'Cherche groupe pour weekly raid, ilvl 264'),

-- Questions typiques
('general', 'C où qu\'on farm les primordiaux d\'eau ?', 1, 0, 'C où qu\'on farm les primordiaux d\'eau ?'),
('general', 'Comment on rejoint la guilde The Kingdom of Sylvania ?', 1, 0, 'Comment on rejoint la guilde The Kingdom of Sylvania ?'),
('general', 'Quelqu\'un sait où est Thrall en ce moment ?', 1, 0, 'Quelqu\'un sait où est Thrall en ce moment ?'),
('general', 'Y\'a un event ce soir ?', 1, 0, 'Y\'a un event ce soir ?'),
('general', 'Qui peut m\'expliquer comment marche le module mod-random-enchants ?', 1, 0, 'Qui peut m\'expliquer comment marche le module mod-random-enchants ?'),

-- Expressions typiques avec émojis
('general', 'Je viens de drop Deathbringer\'s Will !!! :D', 1, 0, 'Je viens de drop Deathbringer\'s Will !!! :D'),
('general', 'Encore un wipe sur le LK... -_-', 1, 0, 'Encore un wipe sur le LK... -_-'),
('general', 'Trop bien ce serveur, merci aux admins <3', 1, 0, 'Trop bien ce serveur, merci aux admins <3'),
('general', 'Qui veut faire des arènes ? 2v2 ou 3v3 je m\'en fiche ^^', 1, 0, 'Qui veut faire des arènes ? 2v2 ou 3v3 je m\'en fiche ^^'),
('general', 'Enfin lvl 80 !! :p', 1, 0, 'Enfin lvl 80 !! :p'),

-- Phrases avec fautes typiques
('general', 'Oré un chaman heal pour naxx25 ?', 1, 0, 'Oré un chaman heal pour naxx25 ?'),
('general', 'Sé ou le pnj pour les montures ?', 1, 0, 'Sé ou le pnj pour les montures ?'),
('general', 'G besoin d\'aide pour la quete des elfes de sang svp', 1, 0, 'G besoin d\'aide pour la quete des elfes de sang svp'),
('general', 'Kelle est la meilleur classe pour pvp ?', 1, 0, 'Kelle est la meilleur classe pour pvp ?'),
('general', 'Ya koi comme event pour noel ?', 1, 0, 'Ya koi comme event pour noel ?'),

-- Discussions sur les modules personnalisés
('general', 'Le mod-arac est trop cool, je joue un prêtre tauren !', 1, 0, 'Le mod-arac est trop cool, je joue un prêtre tauren !'),
('general', 'Comment on utilise le mod-transmog ?', 1, 0, 'Comment on utilise le mod-transmog ?'),
('general', 'Qui veut tester le mod-solocraft sur hc ?', 1, 0, 'Qui veut tester le mod-solocraft sur hc ?'),
('general', 'J\'ai eu un enchant +55 force avec mod-random-enchants !', 1, 0, 'J\'ai eu un enchant +55 force avec mod-random-enchants !'),
('general', 'Le mod-premium vaut vraiment le coup ?', 1, 0, 'Le mod-premium vaut vraiment le coup ?');

-- Ajouter des textes authentiques pour le canal commerce (Trade)
INSERT INTO `playerbot_texts` (`name`, `text`, `say_type`, `reply_type`, `locale_frFR`) VALUES
-- Ventes typiques avec abréviations
('trade', 'WTS [Brise-échine] 5k po négo whisp', 2, 0, 'WTS [Brise-échine] 5k po négo whisp'),
('trade', 'Vend mats JC en gros lot, -10% prix AH', 2, 0, 'Vend mats JC en gros lot, -10% prix AH'),
('trade', 'WTB [Flacon de la guerre] x20 pour raid ce soir', 2, 0, 'WTB [Flacon de la guerre] x20 pour raid ce soir'),
('trade', 'Ench dispo ! Tous les ench haut lvl, tip apprécié', 2, 0, 'Ench dispo ! Tous les ench haut lvl, tip apprécié'),
('trade', 'WTS run ICC25 HM, whisp pour prix', 2, 0, 'WTS run ICC25 HM, whisp pour prix'),

-- Annonces de services
('trade', 'Forgeron 450 cherche travail, je craft tout avec vos mats, pourboire bienvenu', 2, 0, 'Forgeron 450 cherche travail, je craft tout avec vos mats, pourboire bienvenu'),
('trade', 'Portail vers Dalaran 10po, whisp moi', 2, 0, 'Portail vers Dalaran 10po, whisp moi'),
('trade', 'Alchi 450 dispo pour transmutations, CD dispo', 2, 0, 'Alchi 450 dispo pour transmutations, CD dispo'),
('trade', 'Je fais vos gemmes JC contre 5po/gemme (mats fournis)', 2, 0, 'Je fais vos gemmes JC contre 5po/gemme (mats fournis)'),
('trade', 'Ingé 450 peut craft [Visée gyroscopique] et autres trucs cool', 2, 0, 'Ingé 450 peut craft [Visée gyroscopique] et autres trucs cool'),

-- Achats typiques
('trade', 'Achète [Cristal de l\'ombre] en masse, 30po/u', 2, 0, 'Achète [Cristal de l\'ombre] en masse, 30po/u'),
('trade', 'WTB mats pour [Tapis volant] pst avec prix', 2, 0, 'WTB mats pour [Tapis volant] pst avec prix'),
('trade', 'Cherche mineur pour farm 1h, je partage les drops 50/50', 2, 0, 'Cherche mineur pour farm 1h, je partage les drops 50/50'),
('trade', 'Besoin de [Potion de soins] x100 pour raid, offre 200po', 2, 0, 'Besoin de [Potion de soins] x100 pour raid, offre 200po'),
('trade', 'WTB tous vos [Cuir lourd] 10po/stack', 2, 0, 'WTB tous vos [Cuir lourd] 10po/stack'),

-- Annonces de guilde
('trade', 'La guilde <Les Gardiens de Sylvania> recrute ! /w pour plus d\'infos', 2, 0, 'La guilde <Les Gardiens de Sylvania> recrute ! /w pour plus d\'infos'),
('trade', 'Guilde PvP recrute joueurs 2k2+ /w moi', 2, 0, 'Guilde PvP recrute joueurs 2k2+ /w moi'),
('trade', 'On monte une guilde de leveling, susus les débutants !', 2, 0, 'On monte une guilde de leveling, susus les débutants !'),
('trade', '<Progress> 12/12 ICC25HM recrute heal & tank, stuff min 264', 2, 0, '<Progress> 12/12 ICC25HM recrute heal & tank, stuff min 264'),
('trade', 'Nouvelle guilde cherche membres actifs, ambiance cool et raids 3 soirs/semaine', 2, 0, 'Nouvelle guilde cherche membres actifs, ambiance cool et raids 3 soirs/semaine'),

-- Phrases avec émojis et style typique
('trade', 'WTS [Tabard de l\'Écarlate] RARE!!! 10k po ferme /w vite !!!', 2, 0, 'WTS [Tabard de l\'Écarlate] RARE!!! 10k po ferme /w vite !!!'),
('trade', 'Cherche groupe pour weekly VG25, ilvl 264, achievs ok, pas de wipe plz', 2, 0, 'Cherche groupe pour weekly VG25, ilvl 264, achievs ok, pas de wipe plz'),
('trade', 'Dispo pour tank ICC10, 6.2k gs, exp++', 2, 0, 'Dispo pour tank ICC10, 6.2k gs, exp++'),
('trade', 'WTS [Lame d\'ébène] 3.5k nég raisonnable /w moi', 2, 0, 'WTS [Lame d\'ébène] 3.5k nég raisonnable /w moi'),
('trade', 'Qui peut m\'aider à farm les compos pour [Tisse-brume] ? Je paie bien ;)', 2, 0, 'Qui peut m\'aider à farm les compos pour [Tisse-brume] ? Je paie bien ;)');

-- Ajouter des probabilités pour les nouveaux textes
UPDATE `playerbot_texts_chance` SET `probability` = 80 WHERE `name` IN ('general', 'trade');
