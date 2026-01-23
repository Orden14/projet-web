# Projet Web EFREI (MyDrive)

## Auteurs
- Orden14 (Thomas L.)
- Niceley (David W.)
- Ahucha (Antoine H.)
- [Repository link](https://github.com/Orden14/projet-web)

<div align="center">
    <h4>Projet MyDrive</h4>
    <p>Cours projet web EFREI Paris</p>
    <img src="/public/logo/logo.png" alt="MyDrive" height="320px">
</div>

## Utiliser le projet localement

### Prerequis
- Composer 2.7 or supérieur [\<link\>](https://getcomposer.org/doc/00-intro.md)
- Symfony CLI [\<link\>](https://symfony.com/download#step-1-install-symfony-cli)
- Node 20 or supérieur [\<link\>](https://nodejs.org/en/download/)
- Yarn 1.22 or supérieur [\<link\>](https://yarnpkg.com/getting-started/install)
- PHP 8.2 or supérieur [\<link\>](https://www.php.net/downloads)
- Docker 27 or supérieur [\<link\>](https://docs.docker.com/get-docker/)

### Installation pour environnement de développement
1. Cloner le repository et accéder au dossier du projet :
```bash
git clone https://github.com/Orden14/projet-web

cd projet-web
```

2. Installer les dépendances et construire le projet :
```bash
yarn dependencies
```

3. Exécuter le docker compose pour avoir la base de données et PhpMyAdmin : 
```bash
docker-compose up -d
```

4. Créer la base de données avec les données de test :
```bash
yarn truncate-database
```

5. Lancer le serveur de développement
```bash
yarn server-start
```

- L'application est disponible sur `http://localhost:8000`
- PhpMyAdmin est disponible sur `http://localhost:8080` (username : root / mdp : pass123)

### Installation complète full docker

1. Cloner le repository et accéder au dossier du projet :
```bash
git clone https://github.com/Orden14/projet-web

cd projet-web
```

2. Lancer le docker compose : 
```bash
docker-compose -f docker-compose-full.yml up -d --build
```

- L'application est disponible sur `http://localhost`

### Utilisateurs de tests
- Admin :
  - email : admin@test.fr
  - mdp : admin
- Utilisateur standard :
    - email : user@test.fr
    - mdp : user
