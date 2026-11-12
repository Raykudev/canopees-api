# Déploiement du site Canopées

Ce document décrit comment mettre en ligne le site Canopées et comment le maintenir ensuite.
Le site est composé de **deux applications** qui se déploient séparément :

| Application | Rôle | Technologie | Ce qu'on met en ligne |
|---|---|---|---|
| `canopees-api` | API publique (`/api`) + back-office (`/admin`) | Symfony 8.1, API Platform, EasyAdmin, MariaDB | le projet PHP, sa base de données et les images |
| `canopees` | site vitrine public | React + Vite | les fichiers statiques produits par `npm run build` |

Organisation conseillée, avec deux adresses :

```
https://canopees.fr        → site React (fichiers statiques)
https://api.canopees.fr    → Symfony (API + administration)
```

> Les adresses `canopees.fr` et `api.canopees.fr` sont des exemples, à remplacer par celles de l'hébergement réel.

---

## 1. Prérequis de l'hébergement

| Besoin | Version / valeur | Pourquoi |
|---|---|---|
| PHP | **8.4 minimum** | exigé par Symfony 8 |
| Extensions PHP | `pdo_mysql`, `intl`, `mbstring`, `ctype`, `iconv`, `xml`, `fileinfo` | base de données, traductions, envoi d'images |
| Base de données | MariaDB 10.6+ ou MySQL 8+, moteur **InnoDB** | les clés étrangères ne fonctionnent pas en MyISAM |
| Serveur web | Apache ou Nginx, avec **HTTPS** | le dossier public de Symfony est `public/` |
| Accès | **SSH** conseillé (sinon FTP + phpMyAdmin) | lancer les commandes `composer` et `php bin/console` |
| Composer | 2.x | installer les dépendances PHP |
| Node.js | 20+ (sur le poste du développeur suffit) | construire le site React |
| Espace disque | environ 150 Mo | dépendances (~60 Mo sans les outils de dev) + images (~20 Mo) |

Réglages PHP à prévoir (`php.ini` ou panneau de l'hébergeur) pour que Bob puisse envoyer des photos :

```ini
upload_max_filesize = 8M
post_max_size = 10M
memory_limit = 256M
```

---

## 2. Déployer l'API Symfony (`canopees-api`)

### 2.1 Récupérer le code

```bash
git clone https://github.com/Raykudev/canopees-api.git
cd canopees-api
```

### 2.2 Configurer l'environnement de production

Créer un fichier **`.env.local`** (il n'est jamais envoyé sur GitHub) :

```dotenv
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=une_longue_chaine_aleatoire
DATABASE_URL="mysql://UTILISATEUR:MOT_DE_PASSE@HOTE:3306/canopees?serverVersion=11.4.9-MariaDB&charset=utf8mb4"
CORS_ALLOW_ORIGIN='^https://canopees\.fr$'
DEFAULT_URI=https://api.canopees.fr
```

- `APP_ENV=prod` : désactive la barre de debug et les messages d'erreur détaillés.
- `APP_SECRET` : clé secrète propre au serveur. La générer avec `php -r "echo bin2hex(random_bytes(16));"`.
- `CORS_ALLOW_ORIGIN` : seul le site React a le droit d'appeler l'API depuis un navigateur.
- `serverVersion` : à adapter à la version de la base de l'hébergeur.

### 2.3 Installer les dépendances

```bash
composer install --no-dev --optimize-autoloader
```

`--no-dev` n'installe pas les outils de développement (fixtures, maker, profiler). La commande `doctrine:fixtures:load` **n'existe donc pas en production** : impossible d'effacer la base par erreur.

Si le serveur est sous **Apache**, ajouter le fichier `.htaccess` de Symfony :

```bash
composer require symfony/apache-pack
```

### 2.4 Mettre en place la base de données

Les fixtures n'étant pas disponibles en production, le contenu de départ vient de la base de développement.

1. **Sur le poste de développement** : phpMyAdmin → base `canopees` → **Exporter** (format SQL). Le fichier contient les tables, le contenu et l'historique des migrations.
2. **Sur l'hébergement** : créer une base vide (`utf8mb4_unicode_ci`), puis phpMyAdmin → **Importer** le fichier.
3. Vérifier que la structure est à jour :

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

S'il n'y a rien à faire, la commande l'indique : la base importée est déjà à jour.

### 2.5 Envoyer les images

La base ne contient que le **nom** des images. Les fichiers sont dans `public/uploads/` et ne sont pas versionnés avec le code : copier ce dossier sur le serveur (FTP ou `scp`).

Le serveur web doit pouvoir **écrire** dans ce dossier (ajout de photos depuis l'admin) :

```bash
chmod -R 775 public/uploads
```

### 2.6 Préparer les fichiers publics et le cache

```bash
php bin/console assets:install public       # styles et scripts d'EasyAdmin et d'API Platform
php bin/console asset-map:compile           # fichiers de assets/ (AssetMapper)
php bin/console cache:clear                 # cache de production
```

Le serveur web doit pointer sur le dossier **`public/`** (jamais sur la racine du projet, qui contient `.env.local`).

### 2.7 Créer les vrais comptes administrateurs

La base importée contient les comptes de développement, dont le mot de passe est connu. Les supprimer (phpMyAdmin, table `user`), puis créer les vrais comptes :

```bash
php bin/console app:creer-compte
```

La commande demande l'email, le prénom, le nom et le mot de passe (masqué pendant la saisie), refuse un email déjà utilisé et un mot de passe de moins de 8 caractères, puis enregistre le compte avec le rôle `ROLE_ADMIN` et un mot de passe haché. Chaque associé choisit lui-même son mot de passe.

Sans accès SSH : garder les comptes importés et faire changer **immédiatement** le mot de passe de chacun via **Mon profil** dans l'administration.

---

## 3. Déployer le site React (`canopees`)

### 3.1 Construire le site (sur le poste du développeur)

Créer `canopees/.env.production` avec l'adresse de l'API en ligne :

```dotenv
VITE_API_URL=https://api.canopees.fr
```

Puis :

```bash
npm ci
npm run build
```

Le dossier **`dist/`** contient le site prêt à être mis en ligne (HTML, CSS, JavaScript, images).

### 3.2 Envoyer les fichiers

Copier le **contenu** de `dist/` à la racine du domaine `canopees.fr` (FTP ou `scp`).

### 3.3 Faire fonctionner les adresses des pages

Le site est une application d'une seule page : c'est React qui affiche `/tarifs`, `/contact`… Le serveur doit donc renvoyer `index.html` pour toute adresse qui n'est pas un fichier. Sous Apache, ajouter ce fichier **`.htaccess`** à côté de `index.html` :

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.html [L]
```

Sans cette règle, recharger la page `/tarifs` donnerait une erreur 404 du serveur.

---

## 4. Vérifications après la mise en ligne

| Test | Résultat attendu |
|---|---|
| `https://api.canopees.fr/api/prestations` | liste JSON des prestations |
| `https://api.canopees.fr/api/messages` (GET) | erreur 405 : les messages ne sont pas lisibles publiquement |
| `https://api.canopees.fr/admin` sans être connecté | redirection vers la page de connexion |
| 6 mauvais mots de passe de suite | connexion bloquée pendant 1 minute |
| Pages du site React, avec rechargement (F5) | pages affichées, images chargées |
| Formulaire de contact | « Message envoyé », le message apparaît dans **Messages reçus** |
| Modification d'une prestation dans l'admin | changement visible sur le site après rechargement |
| Adresse inexistante (`/abc`) | page « Page introuvable » |
| Cadenas HTTPS dans le navigateur | présent sur les deux adresses |

---

## 5. Mettre à jour le site plus tard

**API** (après une modification du code) :

```bash
git pull
composer install --no-dev --optimize-autoloader
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console cache:clear
```

**Site React** : modifier le code, relancer `npm run build`, renvoyer le contenu de `dist/`.

Le **contenu** (textes, prix, photos) ne demande aucun déploiement : Bob et Tom le modifient directement dans l'administration.

## 6. Sauvegardes

À faire régulièrement (au minimum une fois par semaine) :

- la base de données : export SQL depuis phpMyAdmin, ou `mysqldump canopees > sauvegarde.sql` ;
- le dossier `public/uploads/` (les photos envoyées par Bob et Tom).

Le code, lui, est déjà sauvegardé sur GitHub.

## 7. Sécurité en production

- `APP_ENV=prod` : aucune information technique affichée en cas d'erreur.
- `.env.local` reste hors du dossier public et hors de GitHub.
- HTTPS obligatoire : les mots de passe de l'admin ne circulent jamais en clair.
- Accès à `/admin` réservé au rôle `ROLE_ADMIN`, formulaire de connexion protégé par jeton CSRF, tentatives limitées à 5 par minute.
- Mots de passe hachés (bcrypt) ; aucune création de compte possible depuis le site.
- API en lecture seule, sauf l'envoi de messages, qui est validé côté serveur (champs obligatoires, format email et code postal, consentement RGPD).
- Dépendances à jour : vérifier avec `composer audit` avant chaque mise en ligne.
