# BileMo

[![forthebadge](https://forthebadge.com/images/badges/built-with-love.svg)](http://forthebadge.com)
[![SymfonyInsight](https://insight.symfony.com/projects/087f7d89-0aed-4b07-8cdf-b34ef662fec0/small.svg)](https://insight.symfony.com/projects/087f7d89-0aed-4b07-8cdf-b34ef662fec0)

### Installation

- Clonez le Repository.

- Téléchargez et installez les librairies avec la commande ``composer install``

- Configurez vos variables d'environnement dans le fichier .env

- Créez une base de données sur votre SGBD ou avec la commande ``php bin/console doctrine:database:create``

- Importez le fichier bilemo.sql ou commencez la migration avec la commande ``php bin/console doctrine:migrations:migrate`` et insérez les fixtures avec la commande ``php bin/console doctrine:fixtures:load``

### Exécution

- Lancez l'éxecution du projet avec la commande ``symfony server:start``

### Documentation

``http://127.0.0.1:8000/api/doc``

## Versions

Ce site a été réalisé avec:
- Symfony 5.4.7
- Composer 2.1.9
- PHP 8.0.12
