# Tp Symfony

Thèmatique: 10 - Evenement

## Installation

1. Ajouter un `.env` dans le projet du dossier `app` (exemple dans `.env.dev`)

2. Initialiser composer et node dans le dossier `app`:
    ```bash
    cd ./app
    composer install && npm i
    ```
3. Démarrer le docker avec le docker-compose à la **racine du projet**:
    ```bash
    docker compose up -d
    ```

5. Compiler les assets:
   ```bash
   npm run build
   ```
   
5. Effectuer si besoin toutes les commandes symfony pour initialiser le projet (migration, fixtures, ...) en commençant les commandes par `docker compose exec [...]` dans le dossier racine:
   ```bash
   docker-compose exec php bin/console make:migration
   docker-compose exec php bin/console doctrine:migrations:migrate
   docker-compose exec php bin/console doctrine:fixtures:load
   ```