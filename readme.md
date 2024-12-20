# Tp Symfony

Thèmatique: 10 - Evenement

## Installation

1. Initialiser composer et node dans le dossier `app`:
    ```bash
    cd ./app
    composer install && npm i
    ```
   
2. Ajouter un `.env` dans le projet du dossier `app` (exemple dans `.env.dev`)

3. Démarrer le docker avec le docker-compose à la **racine du projet**:
    ```bash
    docker compose up -d
    ```
   
4. Effectuer toutes les commandes symfony pour initialiser le projet (migration, fixtures, ...)