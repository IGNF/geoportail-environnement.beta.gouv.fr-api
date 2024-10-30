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
DATABASE_URL=postgresql://postgres:root@localhost:5432/foreg
```

### Ajouter un jeu de données 

```cmd
php bin/console app:populate-fake
```

## Utilisation depuis foreg-site

1. Login : 
    - ouvrir **dans un nouvel onglet** `localhost/foreg-site/login` pour simuler l'utilisation du SSO GPF 
    - remplir le formulaire (les login/mdp sont écrits)
    - ça redirige vers /vous-etes-connecté, on enregistre les jetons token et refresh_token dans localstorage
    - Fermer l'onglet
2. Foreg-site
    - A la fermeture, récupérer les tokens
    - Pour les resources non publiques, 
        - ajouter dans le header `header: bearer {token}`
        - Faire au préalable une requête sur /api/token/refresh pour récupérer un token neuf

Note: Pour une utilisation immédiate, les clés publiques/privées utilisées pour l'authentification JWT sont enregistrées en dur, de même que les login/password des utilisateurs
