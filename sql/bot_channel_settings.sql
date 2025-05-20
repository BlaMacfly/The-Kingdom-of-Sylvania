-- Script SQL pour configurer les bots afin qu'ils parlent dans les canaux général et commerce
-- Ce script ajoute des textes spécifiques pour les canaux de discussion sur le serveur The Kingdom of Sylvania

-- Ajouter des textes pour le canal général (General)
INSERT INTO `playerbot_texts` (`name`, `text`, `say_type`, `reply_type`, `locale_frFR`) VALUES
('general', 'Quelqu\'un a vu des monstres rares dans la région ?', 1, 0, 'Quelqu\'un a vu des monstres rares dans la région ?'),
('general', 'Je cherche un groupe pour faire des quêtes à <subzone>', 1, 0, 'Je cherche un groupe pour faire des quêtes à <subzone>'),
('general', 'Quelqu\'un sait où trouver les meilleurs spots de farm ?', 1, 0, 'Quelqu\'un sait où trouver les meilleurs spots de farm ?'),
('general', 'Je viens de monter niveau 80 !', 1, 0, 'Je viens de monter niveau 80 !'),
('general', 'Qui peut m\'aider avec une quête difficile ?', 1, 0, 'Qui peut m\'aider avec une quête difficile ?'),
('general', 'Où est-ce qu\'on peut trouver les meilleurs enchantements ?', 1, 0, 'Où est-ce qu\'on peut trouver les meilleurs enchantements ?'),
('general', 'J\'adore ce serveur !', 1, 0, 'J\'adore ce serveur !'),
('general', 'Quelqu\'un connaît un bon endroit pour pêcher ?', 1, 0, 'Quelqu\'un connaît un bon endroit pour pêcher ?'),
('general', 'Je cherche une guilde active', 1, 0, 'Je cherche une guilde active'),
('general', 'Bonjour à tous les joueurs de The Kingdom of Sylvania !', 1, 0, 'Bonjour à tous les joueurs de The Kingdom of Sylvania !');

-- Ajouter des textes pour le canal commerce (Trade)
INSERT INTO `playerbot_texts` (`name`, `text`, `say_type`, `reply_type`, `locale_frFR`) VALUES
('trade', 'Vends des matériaux d\'artisanat, PST', 2, 0, 'Vends des matériaux d\'artisanat, PST'),
('trade', 'Achète du tissu runique en grande quantité', 2, 0, 'Achète du tissu runique en grande quantité'),
('trade', 'Enchanteur disponible, tous les enchantements', 2, 0, 'Enchanteur disponible, tous les enchantements'),
('trade', 'Vends des potions et des élixirs, prix raisonnables', 2, 0, 'Vends des potions et des élixirs, prix raisonnables'),
('trade', 'Cherche forgeron pour fabriquer une armure', 2, 0, 'Cherche forgeron pour fabriquer une armure'),
('trade', 'Vends des gemmes épiques, PST pour prix', 2, 0, 'Vends des gemmes épiques, PST pour prix'),
('trade', 'Achète des minerais et des herbes', 2, 0, 'Achète des minerais et des herbes'),
('trade', 'Joaillier disponible avec toutes les recettes', 2, 0, 'Joaillier disponible avec toutes les recettes'),
('trade', 'Vends des équipements de raid, PST', 2, 0, 'Vends des équipements de raid, PST'),
('trade', 'Échange des matériaux d\'artisanat contre des services', 2, 0, 'Échange des matériaux d\'artisanat contre des services');

-- Ajouter des probabilités pour les textes des canaux
INSERT INTO `playerbot_texts_chance` (`name`, `probability`) VALUES
('general', 40),
('trade', 30);

-- Mettre à jour la configuration pour augmenter la fréquence des messages dans les canaux
UPDATE `playerbot_texts_chance` SET `probability` = 60 WHERE `name` IN ('general', 'trade');
