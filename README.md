# FOREG API

## Pré-requis

* php 8.2

## Installation 

### Cloner le projet 

```cmd
git clone https://github.com/IGNF/foreg-api.git
```

### Installer les dépendances

```cmd
composer install
```

### Mettre à jour .env.local

```cmd
DATABASE_URL=postgresql://{name}:{password}@{database_ip}:{port (defaut: 5432)}/{database_name}
JWT_TOKEN_TTL=123 #Durée de vie du token (86400 pour 1 jour, 10 pour 10 secondes si besoin) Défaut 300 (5 min)
```

### Créer la bdd (si non existante)

```cmd
php bin/console doctrine:database:create
```

### Mettre à jour le schéma

```cmd
php bin/console doctrine:migration:migrate
```

### Générer les clés publiques/privées pour les tokens JWT

```cmd
php bin/console lexik:jwt:generate-keypair
```

## Installation via Docker compose

``` bash
docker compose up
```
### Pour le DEV 

Ce connecter au conteneur foreg-api et lancer les commandes suivante :

#### Ajouter un jeu de données (local uniquement)

```cmd
php bin/console app:populate-fake
```

## Utilisation depuis foreg-site

1. Login : 
    - ouvrir **dans un nouvel onglet** `localhost/foreg-site/login` pour simuler l'utilisation du SSO GPF 
    - remplir le formulaire (les login/mdp sont écrits)
    - ça redirige vers /vous-etes-connecté, on enregistre les jetons GPE@token et GPE@refresh_token dans localstorage
    - Fermer l'onglet
2. Foreg-site
    - A la fermeture, récupérer les tokens
    - Pour les resources non publiques, 
        - ajouter dans le header `header: bearer {token}`
        - Faire au préalable une requête sur /api/token/refresh pour récupérer un token neuf
