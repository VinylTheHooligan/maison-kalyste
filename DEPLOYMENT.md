# Guide de déploiement local

Ce document explique comment cloner le projet, configurer l'environnement local, lancer le serveur Symfony, charger les fixtures et travailler sur les styles.

> Ce guide est destiné à un environnement de développement. Il ne décrit pas un déploiement en production.

---

## 1. Prérequis

- PHP 8.2 minimum
- Composer
- Git
- Base de données locale recommandé : MySQL 8.4
- Mailpit (possiblement via Docker)
- Optionnel, mais recommandé sur Windows : Laragon

### Pourquoi Laragon ?

Sur Windows, Laragon est pratique car il gère facilement :
- PHP 8.2
- bases de données locales (MySQL)
- serveurs web locaux
- variables d'environnement

Si Laragon est utilisé, veillez à ce que la version de PHP soit bien réglée sur **8.2**.

---

## 2. Cloner le projet

```bash
git clone <url-du-repository> maison-kalyste
cd maison-kalyste
```

Remplacez `<url-du-repository>` par l'URL du dépôt projet.

---

## 3. Installer les dépendances PHP

```bash
composer install
```
Si Docker est utilisé, il faut lancer cette commande après avoir démarré les services Docker.

---

## 4. Configuration locale

### 4.1 Créer le fichier `.env.local`

Copier le fichier `.env` et adapter les valeurs locales :

```bash
cp .env .env.local
```

Ensuite, éditez `.env.local` et vérifiez les variables suivantes :

```dotenv
APP_ENV=dev
APP_SECRET=valeur_quelconque
DEFAULT_URI=http://localhost
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
DATABASE_URL="mysql://root@127.0.0.1:3306/df_kalyste?serverVersion=8.4.3"
MAILER_DSN=smtp://localhost:1025
```
La composition de `DATABASE_URL` peut varié, veillez à bien correspondre le nom d'utilisateur avec celui de votre SGBD. Pour plus d'informations : https://symfony.com/doc/current/doctrine.html#configuring-the-database

Les variables sensibles (APP_SECRET, mots de passe DB) ne doivent jamais être versionnées et doivent être stockées dans des variables d’environnement.

### 4.2 Utilisation de Mailpit

Le projet contient une configuration Mailpit pour le test des réceptions de mail.

Que vous utilisez Laragon ou Docker Compose, Laragon ou `compose.override.yaml` démarre le service Mailpit sur :
- SMTP : `localhost:1025`
- Interface web : `http://localhost:8025`

Avec Mailpit, les emails envoyés par l'application sont interceptés localement et consultables via l'interface web.

---

## 5. Créer la base de données

Pour créer la base de donnée, exécutez cette commande :

```bash
php bin/console doctrine:database:create
```

Puis exécutez les migrations :

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

---

## 6. Charger les fixtures

Le projet inclut des fixtures de développement.

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

Cette commande permet de peupler la base de données avec des données initiales utiles pour le développement.

---

## 7. Lancer le serveur Symfony

Pour démarrer le serveur local Symfony en deamon :

```bash
symfony serve -d
```

Le site sera alors accessible sur `http://localhost:8000`.

---

## 8. Travailler sur les styles

Le projet utilise Tailwind et la commande suivante doit être lancée pendant le développement CSS :

```bash
php bin/console tailwind:build --watch
```

Cette commande reconstruit automatiquement les styles à chaque modification.

---

## 9. Commandes utiles

```bash
# Vérifier la version de PHP
php -v

# Vérifier les prérequis Symfony
php bin/console about

# Effacer le cache
php bin/console cache:clear

# Installer les assets (si nécessaire)
php bin/console assets:install --symlink
```

---

## 10. Script de déploiement rapide

```bash
# Script de déploiement local

composer install
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
symfony serve -d
```

---

## 11. Notes importantes

- Ce guide est fait pour un environnement de développement, pas pour la production.
- Une veille régulière est effectuée sur les mises à jour de Symfony, Doctrine, PHP et Tailwind afin de garantir un déploiement sécurisé.
- Ne commitez jamais votre `.env.local`, assurez-vous qu'il soit ignoré dans `.gitignore`, à la racine du projet.
- Si un serveur de mail local est nécessaire, Mailpit est déjà prévu dans le fichier `compose.override.yaml`.
- La variable d'environnement `APP_SECRET` n'a que peu d'importance pour le moment. En production, il sera regénéré avec la commande `php bin/console about --generate-secret`.