-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : dim. 16 oct. 2022 à 15:01
-- Version du serveur : 5.7.33
-- Version de PHP : 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bilemo`
--

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id`, `name`, `email`, `password`, `roles`) VALUES
(29, 'Bill', 'bile@mo.fr', '$2y$13$vdiuxmTmD/AA.gA1JwdVmuBulmMCbx8kQhlI7iMay9JQTl66KBxNu', '[\"ROLE_ADMIN\"]'),
(30, 'Alisa Schamberger', 'bart29@prosacco.com', '(,AvS(7z/3V_?bg', '[\"ROLE_USER\"]'),
(31, 'Kitty Gorczany', 'olson.jonathan@romaguera.com', '4*|Ct$M5ld20j<s', '[\"ROLE_USER\"]'),
(32, 'Mrs. Zoey Hammes MD', 'laverne40@runolfsson.com', 'DyA>I(yt&', '[\"ROLE_USER\"]'),
(33, 'Prof. Leone Thiel IV', 'emil47@marks.biz', 'no>1$I$ET5*TC<G', '[\"ROLE_USER\"]'),
(34, 'Mrs. Robyn West V', 'grolfson@goldner.com', 'J{:JrZHW$:', '[\"ROLE_USER\"]'),
(35, 'Catherine Funk', 'rachel10@hotmail.com', 'z\"QeN4{NoqsuCmW', '[\"ROLE_USER\"]'),
(36, 'Ariel Blick', 'dimitri.mcclure@gmail.com', 'GTd%ln%5xj=d\'A:ee9\\c', '[\"ROLE_USER\"]'),
(37, 'Jack Muller', 'rdavis@hotmail.com', 'GUn~/8]|CS', '[\"ROLE_USER\"]'),
(38, 'Presley Langosh', 'rleffler@hotmail.com', '-h:Xwc8aJIkH*', '[\"ROLE_USER\"]'),
(39, 'Jaylon Hills', 'heaney.arvel@wisozk.com', '|9SAXw:XN{Br1sv@*[{', '[\"ROLE_USER\"]'),
(40, 'Namino', 'nami@nome.fr', '$2y$13$vdiuxmTmD/AA.gA1JwdVmuBulmMCbx8kQhlI7iMay9JQTl66KBxNu', '[\"ROLE_USER\"]'),
(41, 'Yoan amberger', 'grace@prosacco.com', 'd@Sv3eds', '[\"ROLE_USER\"]'),
(45, 'Kila berna', 'suod@prosacco.com', 'd#ez3eds', '[\"ROLE_USER\"]');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`id`, `brand`, `name`, `model`, `color`, `price`, `description`) VALUES
(21, 'Blackberry', 'quam', 'esse', 'purple', 52758.7, 'Quia doloremque blanditiis voluptates voluptatem. Eaque vitae rerum voluptatem eveniet quia tempora. Enim rem voluptatem cum dolor accusantium sed perferendis. Corporis ipsum sit quidem labore. In quisquam dolorem id magni perspiciatis eos nihil et.'),
(22, 'Blackberry', 'sunt', 'molestiae', 'white', 7482153.089857, 'Adipisci ipsum ea autem. Nihil dignissimos et et velit. Laboriosam qui aliquid quaerat. Et dolore est molestias ipsam hic tempore commodi voluptatum.'),
(23, 'Blackberry', 'deleniti', 'quaerat', 'gray', 548786592.39956, 'Nemo nesciunt voluptatem fuga officia aliquam cupiditate. Ipsa ut eos itaque. Laborum nostrum aut est eius et. Quia cum dolor minus perspiciatis ipsum sequi.'),
(24, 'Blackberry', 'odit', 'at', 'blue', 187.168, 'Consequatur distinctio earum ut perferendis enim. Quia itaque ratione voluptatem a. Et et sed quasi dignissimos aut voluptas consequatur. Dolores velit voluptatem voluptatem eaque.'),
(25, 'Huawei', 'veritatis', 'ut', 'fuchsia', 12.47265545, 'Corrupti iure cumque eos provident velit et non. Nobis aspernatur illum saepe at quas aut perspiciatis quis. Consequuntur rem est atque saepe quibusdam. Voluptates reprehenderit perferendis ad et.'),
(26, 'Blackberry', 'enim', 'dolores', 'aqua', 280984.25222302, 'Provident ab optio reiciendis dolorem possimus ipsam. Libero sequi debitis quia ea ipsa non vel. Cumque vel consequuntur doloremque explicabo. Ipsum quasi dolor voluptatem aut praesentium aliquid dignissimos.'),
(27, 'Samsung', 'quis', 'eos', 'yellow', 324559112.72482, 'Enim eligendi iure aliquid accusantium. Earum voluptatem labore facilis assumenda. Et iusto dicta ut enim perferendis eum.'),
(28, 'Apple', 'dolorum', 'voluptas', 'white', 11.961321615, 'Enim numquam recusandae aut maxime maxime qui. Hic autem vel eum. Sunt maxime excepturi aut magni sunt.'),
(29, 'Huawei', 'minima', 'ad', 'purple', 2181511.5216, 'Ullam doloribus aliquam nobis sit. Temporibus totam similique eum consectetur voluptates quae. Voluptatem temporibus doloribus velit magni quia. Quia eveniet quis omnis totam.'),
(30, 'Samsung', 'quia', 'facere', 'black', 25748962, 'Aspernatur nihil totam deleniti beatae voluptates nemo. Ducimus tempore sunt dignissimos est laudantium. Autem qui nam quo dicta qui nisi sed est.');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `client_id`, `name`, `email`) VALUES
(12, 40, 'Dr. Brandon Hyatt', 'kautzer.lane@kerluke.com'),
(13, 40, 'Ms. Marguerite Marguerite', 'marge@rosette.fr'),
(14, 29, 'Rahsaan Cormier PhD', 'alvena72@wolff.info'),
(15, 29, 'Dr. Kenna Abernathy MD', 'schumm.chance@friesen.com'),
(16, 29, 'Eleazar Schuster', 'johanna22@gmail.com'),
(17, 29, 'Karley Lubowitz', 'hammes.rosalind@okuneva.com'),
(19, 29, 'Emilia Lemke', 'emiladtke@wyman.com'),
(20, 29, 'Miss Cassie Haley DVM', 'erwin89@reynolds.com'),
(21, 29, 'Kimberly Baumbach', 'alfonzo.kozey@rowe.biz'),
(22, 30, 'Rosetta Kirlin DVM', 'hokeefe@hotmail.com'),
(23, 30, 'Rose Kirlin DVM', 'hokeefe@hotmail.com'),
(27, 40, 'Rosette Kirlin DVM', 'hokeefe@hotmail.com'),
(28, 34, 'Dylan Kirlin DVM', 'dikelr@hotmail.com'),
(30, 40, 'Testi Partner', 'testo@hotmail.com'),
(31, 40, 'Testate Partner', 'testu@hotmail.com'),
(32, 40, 'Testate Partner', 'testu@hotmail.com'),
(33, 40, 'Robere Partner', 'robert@hotmail.com'),
(34, 29, 'Besame Mucho', 'mucho@hotmail.com');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8D93D64919EB6921` (`client_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT pour la table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D64919EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
