-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 26 juin 2025 à 11:09
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `quiz_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(2, 'CSS'),
(5, 'Data et IA'),
(1, 'HTML'),
(6, 'Pays tech le plus développé'),
(3, 'PHP'),
(4, 'Systèmes et Réseaux');

-- --------------------------------------------------------

--
-- Structure de la table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `option_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `options`
--

INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(1, 1, '<head>', 0),
(2, 1, '<title>', 1),
(3, 1, '<h1>', 0),
(4, 2, 'src', 0),
(5, 2, 'alt', 1),
(6, 2, 'title', 0),
(7, 3, '<section>', 0),
(8, 3, '<nav>', 1),
(9, 3, '<div>', 0),
(10, 4, '<ul>', 0),
(11, 4, '<ol>', 1),
(12, 4, '<li>', 0),
(13, 5, 'color', 0),
(14, 5, 'background-color', 1),
(15, 5, 'bgcolor', 0),
(16, 6, '#highlight', 0),
(17, 6, '.highlight', 1),
(18, 6, 'highlight', 0),
(19, 7, 'em', 0),
(20, 7, 'rem', 1),
(21, 7, 'px', 0),
(22, 8, 'font-weight: bold;', 1),
(23, 8, 'text-style: bold;', 0),
(24, 8, 'bold: true;', 0),
(25, 9, 'const', 0),
(26, 9, 'let', 1),
(27, 9, 'var', 1),
(28, 10, 'alert()', 0),
(29, 10, 'console.log()', 1),
(30, 10, 'document.write()', 0),
(31, 11, '==', 0),
(32, 11, '===', 1),
(33, 11, '!=', 0),
(34, 12, 'call maFonction;', 0),
(35, 12, 'maFonction();', 1),
(36, 12, 'run maFonction;', 0),
(37, 13, '', 0),
(38, 13, '// commentaire', 1),
(39, 13, '/* commentaire */', 0),
(40, 14, '$_GET', 0),
(41, 14, '$_POST', 1),
(42, 14, '$_REQUEST', 0),
(43, 15, 'require_once', 0),
(44, 15, 'include', 1),
(45, 15, 'use', 0),
(46, 16, 'isset($var)', 0),
(47, 16, 'empty($var)', 1),
(48, 16, 'is_null($var)', 0),
(49, 17, 'SELECT', 0),
(50, 17, 'WHERE', 1),
(51, 17, 'FROM', 0),
(52, 18, 'UPDATE', 0),
(53, 18, 'INSERT INTO', 1),
(54, 18, 'ADD NEW', 0),
(55, 19, 'MODIFY', 0),
(56, 19, 'ALTER TABLE', 0),
(57, 19, 'UPDATE', 1),
(58, 20, 'SORT BY', 0),
(59, 20, 'ORDER BY', 1),
(60, 20, 'GROUP BY', 0),
(241, 81, 'Authentifier les utilisateurs', 0),
(242, 81, 'Gérer les enregistrements de noms de domaine pour une zone spécifique', 1),
(243, 81, 'Attribuer des adresses IP dynamiques', 0),
(244, 82, 'HTTP', 0),
(245, 82, 'FTP', 1),
(246, 82, 'TCP', 0),
(247, 83, 'La bande passante disponible', 0),
(248, 83, 'Le temps de réponse (latence) entre deux hôtes', 1),
(249, 83, 'Le nombre d\'erreurs de transmission', 0),
(250, 84, 'Routeur', 0),
(251, 84, 'Hub', 1),
(252, 84, 'Commutateur (Switch)', 0),
(253, 85, 'Le mot de passe du réseau', 0),
(254, 85, 'Le nom du réseau sans fil', 1),
(255, 85, 'L\'adresse IP de la passerelle', 0),
(256, 86, 'Augmenter la vitesse de l\'Internet', 0),
(257, 86, 'Segmenter un réseau physique en plusieurs réseaux logiques', 1),
(258, 86, 'Désactiver le protocole TCP/IP', 0),
(259, 87, 'RIP', 0),
(260, 87, 'OSPF', 1),
(261, 87, 'BGP', 0),
(262, 88, 'Une adresse IP qui change fréquemment', 0),
(263, 88, 'Une adresse IP configurée manuellement qui ne change pas', 1),
(264, 88, 'Une adresse IP attribuée par DHCP', 0),
(265, 89, 'Traceroute', 0),
(266, 89, 'Wireshark', 1),
(267, 89, 'Netstat', 0),
(268, 90, 'Le matériel est plus lent que le logiciel', 0),
(269, 90, 'Le matériel est un dispositif physique dédié, le logiciel est un programme installé sur un ordinateur', 1),
(270, 90, 'Il n\'y a aucune différence fonctionnelle', 0),
(271, 91, 'Garantir la cohérence stricte des données', 0),
(272, 91, 'Gérer des données non structurées ou semi-structurées et offrir une grande évolutivité horizontale', 1),
(273, 91, 'Exécuter des requêtes SQL complexes plus rapidement', 0),
(274, 92, 'Un modèle dont le fonctionnement interne est facilement interprétable', 0),
(275, 92, 'Un modèle complexe dont les décisions sont difficiles à expliquer ou à interpréter', 1),
(276, 92, 'Un modèle qui ne peut traiter que des données binaires', 0),
(277, 93, 'Données audio', 0),
(278, 93, 'Images et vidéos', 1),
(279, 93, 'Données textuelles', 0),
(280, 94, 'Visualisation des données', 0),
(281, 94, 'Nettoyage et prétraitement des données', 1),
(282, 94, 'Modélisation prédictive', 0),
(283, 95, 'Diviser les données en ensemble d\'entraînement et de test une seule fois', 0),
(284, 95, 'Une technique pour évaluer la performance d\'un modèle en divisant les données en plusieurs sous-ensembles', 1),
(285, 95, 'La vérification de la justesse des étiquettes des données', 0),
(286, 96, 'Reconnaissance vocale', 0),
(287, 96, 'Traitement du langage naturel (NLP)', 1),
(288, 96, 'Génération de nombres aléatoires', 0),
(289, 97, 'Une base de données relationnelle optimisée pour les requêtes rapides', 0),
(290, 97, 'Un vaste référentiel centralisé qui stocke des données brutes dans leur format natif', 1),
(291, 97, 'Un petit ensemble de données utilisé pour les tests', 0),
(292, 98, 'Régression Linéaire', 0),
(293, 98, 'Analyse en Composantes Principales (PCA)', 1),
(294, 98, 'K-Nearest Neighbors (KNN)', 0),
(295, 99, 'Le surapprentissage', 0),
(296, 99, 'Le biais algorithmique', 1),
(297, 99, 'La faible latence', 0),
(298, 100, 'Apprentissage par renforcement', 1),
(299, 100, 'Apprentissage non supervisé', 0),
(300, 100, 'Systèmes experts', 0),
(301, 101, 'États-Unis', 0),
(302, 101, 'Chine', 1),
(303, 101, 'Japon', 0),
(304, 102, 'Suède', 0),
(305, 102, 'Estonie', 1),
(306, 102, 'Norvège', 0),
(307, 103, 'Mumbai', 0),
(308, 103, 'Bangalore', 1),
(309, 103, 'New Delhi', 0),
(310, 104, 'Italie', 0),
(311, 104, 'Allemagne', 1),
(312, 104, 'Corée du Sud', 0),
(313, 105, 'Thaïlande', 0),
(314, 105, 'Taïwan', 1),
(315, 105, 'Vietnam', 0),
(316, 106, 'Royaume-Uni', 0),
(317, 106, 'Allemagne', 1),
(318, 106, 'France', 0),
(319, 107, 'La robotique avancée', 0),
(320, 107, 'La lithographie pour la production de puces électroniques (ASML)', 1),
(321, 107, 'Les véhicules électriques', 0),
(322, 108, 'États-Unis', 0),
(323, 108, 'Israël', 1),
(324, 108, 'Singapour', 0),
(325, 109, 'Nouvelle-Zélande', 0),
(326, 109, 'Islande', 1),
(327, 109, 'Canada', 0),
(339, 110, 'Corée du Sud', 0),
(340, 110, 'usa', 1),
(341, 110, 'france', 0),
(342, 110, 'algerie', 0),
(351, 112, 'jj', 0),
(352, 112, 'hhb', 0),
(353, 112, 'mm', 0),
(354, 112, 'ijg', 1);

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `question_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `questions`
--

INSERT INTO `questions` (`id`, `category_id`, `question_text`) VALUES
(1, 1, 'Quelle balise est utilisée pour définir le titre d\'une page web visible dans l\'onglet du navigateur ?'),
(2, 1, 'Quel attribut est utilisé pour spécifier un texte alternatif pour une image ?'),
(3, 1, 'Quelle balise HTML5 est utilisée pour regrouper des éléments de navigation ?'),
(4, 1, 'Quel élément HTML est utilisé pour créer une liste ordonnée ?'),
(5, 2, 'Quelle propriété CSS est utilisée pour changer la couleur de fond d\'un élément ?'),
(6, 2, 'Quel sélecteur CSS cible tous les éléments qui ont la classe \"highlight\" ?'),
(7, 2, 'Quelle unité de mesure est relative à la taille de la police de l\'élément racine (root element) ?'),
(8, 2, 'Comment rendre le texte en gras en CSS ?'),
(9, NULL, 'Quel mot-clé est utilisé pour déclarer une variable dont la valeur peut être réaffectée ?'),
(10, NULL, 'Quelle est la méthode JavaScript pour afficher un message dans la console du navigateur ?'),
(11, NULL, 'Quel opérateur est utilisé pour comparer à la fois la valeur et le type en JavaScript ?'),
(12, NULL, 'Comment appeler une fonction nommée \"maFonction\" en JavaScript ?'),
(13, 3, 'Quelle est la syntaxe correcte pour un commentaire sur une seule ligne en PHP ?'),
(14, 3, 'Quelle superglobale PHP est utilisée pour collecter les données de formulaire envoyées avec la méthode POST ?'),
(15, 3, 'Quelle fonction PHP est utilisée pour inclure un fichier qui peut être inclus plusieurs fois ?'),
(16, 3, 'Comment vérifier si une variable est vide en PHP ?'),
(17, NULL, 'Quelle clause SQL est utilisée pour extraire uniquement les enregistrements qui satisfont une condition spécifiée ?'),
(18, NULL, 'Quelle commande SQL est utilisée pour insérer de nouvelles données dans une base de données ?'),
(19, NULL, 'Quelle commande SQL est utilisée pour modifier des enregistrements existants dans une base de données ?'),
(20, NULL, 'Quelle clause SQL est utilisée pour trier le jeu de résultats d\'une requête ?'),
(81, 4, 'Quelle est la fonction d\'un serveur DNS primaire ?'),
(82, 4, 'Quel est le protocole utilisé pour le transfert de fichiers sur un réseau ?'),
(83, 4, 'Qu\'est-ce que le \"ping\" mesure lors d\'un test de connectivité réseau ?'),
(84, 4, 'Quel appareil réseau fonctionne à la couche physique (couche 1) du modèle OSI et retransmet simplement les signaux ?'),
(85, 4, 'Dans un réseau Wi-Fi, que représente un SSID ?'),
(86, 4, 'Quel est l\'objectif principal d\'un VLAN ?'),
(87, 4, 'Quel protocole de routage dynamique est un protocole à état de liens (link-state) ?'),
(88, 4, 'Qu\'est-ce qu\'une adresse IP statique ?'),
(89, 4, 'Quel outil réseau permet d\'analyser les paquets passant par une interface réseau ?'),
(90, 4, 'Quelle est la différence entre un firewall matériel et un firewall logiciel ?'),
(91, 5, 'Quelle est la principale fonction d\'une base de données NoSQL par rapport à une base de données SQL traditionnelle ?'),
(92, 5, 'Qu\'est-ce qu\'un modèle d\'apprentissage automatique \"boîte noire\" ?'),
(93, 5, 'Quel type de données est le plus souvent utilisé pour entraîner des modèles de vision par ordinateur ?'),
(94, 5, 'Quel est le processus de conversion des données brutes en un format propre et structuré pour l\'analyse ?'),
(95, 5, 'En apprentissage automatique, que signifie la \"validation croisée\" ?'),
(96, 5, 'Quel concept décrit la capacité d\'une IA à comprendre le contexte et les nuances dans le langage humain ?'),
(97, 5, 'Qu\'est-ce qu\'un \"data lake\" ?'),
(98, 5, 'Quel algorithme est souvent utilisé pour la réduction de dimensionnalité, comme le PCA ?'),
(99, 5, 'Dans le domaine de l\'éthique de l\'IA, quel problème est lié au fait qu\'un modèle peut prendre des décisions injustes pour certains groupes ?'),
(100, 5, 'Quel type d\'IA se concentre sur la création de systèmes capables de résoudre des problèmes complexes par essais et erreurs ?'),
(101, 6, 'Quel pays est souvent cité pour son leadership en matière de brevets d\'intelligence artificielle et de supercalculateurs ?'),
(102, 6, 'Quel pays scandinave a été un pionnier dans les systèmes de vote électronique sécurisés et l\'e-gouvernement ?'),
(103, 6, 'Quelle ville en Inde est surnommée la \"Silicon Valley de l\'Inde\" ?'),
(104, 6, 'Quel pays est réputé pour ses avancées dans l\'impression 3D et les matériaux intelligents ?'),
(105, 6, 'Quel pays d\'Asie du Sud-Est est un leader mondial dans la fabrication de semi-conducteurs et l\'assemblage électronique ?'),
(106, 6, 'Quel pays européen est reconnu pour sa recherche de pointe en robotique industrielle et son secteur manufacturier de haute technologie ?'),
(107, 6, 'Quelle est la principale contribution technologique des Pays-Bas au niveau mondial ?'),
(108, 6, 'Quel pays est en tête pour le nombre de startups par habitant, notamment dans la cybersécurité ?'),
(109, 6, 'Quel pays est le leader mondial dans l\'énergie géothermique et a une culture d\'innovation dans les technologies vertes ?'),
(110, 6, 'Quel pays, bien que petit, est un hub majeur pour la finance technologique (FinTech) et la gestion de données en Asie ?'),
(112, 1, 'test html');

-- --------------------------------------------------------

--
-- Structure de la table `quiz_results`
--

CREATE TABLE `quiz_results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `score` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `quiz_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quiz_results`
--

INSERT INTO `quiz_results` (`id`, `user_id`, `score`, `total_questions`, `quiz_date`, `category_id`) VALUES
(1, 2, 5, 12, '2025-06-23 12:33:49', 0),
(2, 4, 4, 12, '2025-06-23 12:41:14', 0),
(3, 2, 5, 12, '2025-06-23 12:44:48', 0),
(4, 2, 5, 10, '2025-06-23 13:28:48', 6),
(5, 2, 3, 4, '2025-06-23 13:29:14', 2),
(6, 2, 1, 4, '2025-06-23 13:29:47', 3),
(7, 2, 3, 10, '2025-06-23 13:30:26', 4),
(8, 2, 4, 10, '2025-06-23 13:31:30', 6),
(9, 2, 2, 4, '2025-06-23 13:32:17', 1),
(10, 2, 1, 10, '2025-06-23 13:38:48', 4),
(11, 2, 0, 4, '2025-06-23 13:39:23', 3),
(12, 2, 1, 4, '2025-06-23 13:40:54', 3),
(13, 2, 1, 4, '2025-06-23 13:41:23', 3),
(14, 2, 2, 4, '2025-06-24 10:09:42', 2),
(15, 2, 2, 10, '2025-06-24 10:10:19', 5),
(16, 2, 1, 4, '2025-06-24 10:16:44', 2),
(17, 2, 4, 4, '2025-06-24 10:17:02', 2),
(18, 2, 4, 10, '2025-06-24 10:21:31', 5),
(19, 2, 2, 4, '2025-06-25 10:46:24', 2),
(20, 2, 2, 4, '2025-06-25 22:20:39', 2),
(21, 2, 4, 10, '2025-06-26 07:16:25', 4),
(22, 2, 4, 10, '2025-06-26 07:21:00', 6),
(23, 5, 4, 10, '2025-06-26 07:26:47', 6),
(24, 5, 0, 4, '2025-06-26 07:27:22', 1),
(25, 2, 2, 5, '2025-06-26 07:31:38', 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `is_admin`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 1, '2025-06-23 11:28:09'),
(2, 'bhi', 'bhi@gmail.com', '$2y$10$bwWvE9fsnDF2eOC8V.OG4.MI4VRrLiDeHfHE89lLaADBxBNhaavBm', 1, '2025-06-23 11:57:22'),
(3, 'ycf', 'ycf@gmail.com', '$2y$10$IdaGNd/foKhdK0lKxFcoMe.aYMmCYF3mTzA9h.n.O/CzWIvCZyrPG', 0, '2025-06-23 12:22:19'),
(4, 'bachir', 'bachir@gmail.com', '$2y$10$aG4TFi42oNNiIW0lPajQ.Ou.3IL/wm.sT.dlOOgdyM8yZ0dTAB7lO', 0, '2025-06-23 12:39:34'),
(5, 'ihab', 'ihabadmin@gmail.com', '$2y$10$CseKZFFBl9DgX4Y4r3XGCuuqbbeS0ZQF3K.RZBvZg4Zgq7W93Rbzy', 0, '2025-06-25 10:08:36'),
(6, 'ldandoy', 'ldandoy5@gmail.com', '$2y$10$HK1buVlPG37gT3AXV/XDj.uiByfPP5uE3USPqWo55ffK8Sgnw4ELO', 0, '2025-06-26 07:29:48');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Index pour la table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Index pour la table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=355;

--
-- AUTO_INCREMENT pour la table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT pour la table `quiz_results`
--
ALTER TABLE `quiz_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD CONSTRAINT `quiz_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
