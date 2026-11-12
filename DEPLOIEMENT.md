# Déploiement du site Canopées

Le site est en ligne à l'adresse **https://canopees-mspr.alwaysdata.net**, chez l'hébergeur **alwaysdata** (offre gratuite).

Ce document décrit comment il a été mis en ligne, comment le mettre à jour, et comment le refaire sur un autre hébergement.

---

## 1. Architecture en production : une seule adresse

En développement, il y a deux serveurs : le site React (`npm run dev`, port 5173) et l'API Symfony (port 8000).

En production, **Symfony sert tout, à la même adresse** :

| Adresse | Ce qui répond |
|---|---|
| `/`, `/tarifs`, `/contact`, `/cgu`… | le site React construit (`public/index.html` + `public/static/`) |
| `/api/...` | l'API (API Platform) |
| `/admin`, `/login` | l'administration (EasyAdmin) |
| `/uploads/...` | les photos |

Comment :

- `npm run build` (projet `canopees`) produit le site dans `dist/`. Son contenu est copié dans `canopees-api/public/` et versionné avec l'API.
- `.env.production` (projet `canopees`) laisse `VITE_API_URL` vide : React appelle l'API en relatif (`/api/prestations`). Même adresse, donc **pas de CORS** à gérer en production.
- `src/Controller/SiteController.php` renvoie `public/index.html` pour toute adresse qui n'est ni `/api`, ni `/admin`, ni `/login` : React Router affiche ensuite la bonne page.
- `public/.htaccess` (recette `symfony/apache-pack`) envoie à Symfony toute adresse qui n'est pas un vrai fichier.
- `vite.config.js` place les fichiers construits dans `static/`, car `public/assets/` est utilisé par Symfony (AssetMapper).

Pourquoi ce choix : une seule mise en ligne au lieu de deux, pas de configuration CORS, et il fonctionne aussi chez les hébergeurs qui bloquent les appels entre deux adresses (InfinityFree, par exemple).

---

## 2. Prérequis de l'hébergement

| Besoin | Chez alwaysdata |
|---|---|
| PHP 8.4 minimum (Symfony 8) | PHP 8.4.27 |
| MariaDB ou MySQL, moteur InnoDB | MariaDB 11.8 |
| Accès SSH (pour `composer` et `php bin/console`) | `ssh-canopees-mspr.alwaysdata.net` |
| Choix du dossier public | oui (Web → Sites → Répertoire racine) |
| HTTPS | certificat fourni, HTTPS forcé |

---

## 3. Mise en ligne, étape par étape

### 3.1 Préparer le projet (sur le poste du développeur)

```bash
# Dans canopees (site React)
npm run build
# Copier le contenu de dist/ dans canopees-api/public/, puis commiter et pousser canopees-api
```

Exporter la base de développement : phpMyAdmin (ou `mariadb-dump`) → fichier `canopees.sql` (tables et contenu).

### 3.2 Configurer l'hébergement (panneau alwaysdata)

1. **Environnement → PHP** : version 8.4.
2. **Bases de données → MySQL** : créer la base `canopees-mspr_canopees` (hôte `mysql-canopees-mspr.alwaysdata.net`).
3. **Accès distant → SSH** : connexion par mot de passe active pour l'utilisateur `canopees-mspr`.

### 3.3 Installer le code (en SSH)

```bash
ssh canopees-mspr@ssh-canopees-mspr.alwaysdata.net
git clone https://github.com/Raykudev/canopees-api.git
cd canopees-api
```

Le projet est installé **à côté** du dossier `www` : seul son sous-dossier `public/` sera visible sur Internet.

### 3.4 Configurer la production (`.env.local`, jamais versionné)

```bash
php -r "echo bin2hex(random_bytes(16)).PHP_EOL;"   # génère la clé APP_SECRET
nano .env.local
```

```dotenv
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=<la clé générée>
DATABASE_URL="mysql://canopees-mspr:<mot de passe>@mysql-canopees-mspr.alwaysdata.net:3306/canopees-mspr_canopees?serverVersion=11.8.0-MariaDB&charset=utf8mb4"
CORS_ALLOW_ORIGIN='^https://canopees-mspr\.alwaysdata\.net$'
DEFAULT_URI=https://canopees-mspr.alwaysdata.net
```

Un mot de passe contenant `@ : / # ? &` doit être encodé dans `DATABASE_URL` (`@` devient `%40`).

### 3.5 Installer les dépendances

```bash
composer install --no-dev --optimize-autoloader
```

`--no-dev` n'installe pas les outils de développement : la commande `doctrine:fixtures:load`, qui vide la base, **n'existe pas en production**. Composer lance ensuite `cache:clear`, `assets:install public` et `importmap:install`.

### 3.6 Importer la base

phpMyAdmin d'alwaysdata → base `canopees-mspr_canopees` → **Importer** → `canopees.sql`.

Vérification en SSH (la base importée est déjà à jour) :

```bash
php bin/console doctrine:migrations:migrate --no-interaction
# [OK] Already at the latest version
php bin/console asset-map:compile
```

Les photos (`public/uploads/`) sont versionnées avec le code : elles sont arrivées avec le `git clone`.

### 3.7 Publier le site (panneau alwaysdata)

**Web → Sites** → modifier le site `canopees-mspr.alwaysdata.net` :

- Type : **PHP**
- Répertoire racine : **`canopees-api/public`**
- php.ini personnalisé, pour accepter les photos prises au téléphone :

```ini
upload_max_filesize = 8M
post_max_size = 10M
```

- **Forcer le HTTPS** : `http://` redirige vers `https://`.

### 3.8 Remplacer les comptes de développement

La base importée contenait les comptes de développement, dont un mot de passe écrit dans les fixtures (donc public sur GitHub).

1. phpMyAdmin → table `user` → suppression des deux comptes.
2. En SSH, création des vrais comptes, une fois par associé :

```bash
php bin/console app:creer-compte
```

La commande demande l'email, le prénom, le nom et le mot de passe (masqué pendant la saisie), refuse un email déjà utilisé et un mot de passe de moins de 8 caractères, puis enregistre le compte avec le rôle `ROLE_ADMIN` et un mot de passe haché.

---

## 4. Vérifications effectuées après la mise en ligne

| Test | Résultat |
|---|---|
| Pages du site (`/`, `/tarifs`, `/qui-sommes-nous`, `/mentions-legales`) | 200, contenu chargé depuis l'API |
| `GET /api/prestations` | 200, 5 prestations |
| `GET /api/messages` | 405 : les messages ne sont pas lisibles publiquement |
| `POST /api/messages` invalide | 422 avec la liste des erreurs |
| `/admin` sans être connecté | 302 vers la page de connexion |
| Anciens mots de passe de développement | refusés |
| `/_profiler` | 404 : outils de développement absents |
| `/.env.local`, `/composer.json` | non accessibles (page du site renvoyée) |
| `http://` | 301 vers `https://` |

---

## 5. Mettre à jour le site

**Modification du code de l'API** (sur le poste, puis en SSH) :

```bash
cd ~/canopees-api
git pull
composer install --no-dev --optimize-autoloader
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console asset-map:compile
php bin/console cache:clear
```

**Modification du site React** : `npm run build`, copie de `dist/` dans `canopees-api/public/`, commit, puis la même procédure `git pull` sur le serveur.

Après une mise à jour d'EasyAdmin, `assets:install public` (lancé par `composer install`) recopie ses styles ; sans cela, l'administration s'affiche sans mise en forme.

Le **contenu** (textes, prix, photos, messages) ne demande aucun déploiement : Bob et Tom le gèrent dans l'administration.

## 6. Sauvegardes

- Base de données : export SQL régulier depuis phpMyAdmin (alwaysdata propose aussi des sauvegardes automatiques, à vérifier dans le panneau, menu Avancé).
- Photos envoyées depuis l'administration : dossier `public/uploads/` (à récupérer par SFTP).
- Code : sur GitHub.

## 7. Sécurité en production

- `APP_ENV=prod` : aucune information technique affichée en cas d'erreur, pas de barre de debug.
- Seul `public/` est exposé ; `.env.local`, le code et `vendor/` restent hors d'atteinte.
- HTTPS forcé : les mots de passe de l'administration ne circulent jamais en clair.
- `/admin` réservé au rôle `ROLE_ADMIN`, connexion protégée par jeton CSRF et limitée à 5 tentatives par minute.
- Mots de passe hachés (bcrypt), comptes créés uniquement en SSH.
- API en lecture seule, sauf l'envoi de messages validé côté serveur.
- Dépendances vérifiées avec `composer audit` avant la mise en ligne.

## 8. Autre hébergement

La même procédure s'applique à tout hébergement PHP 8.4 avec SSH. Sans SSH (InfinityFree par exemple), il faut construire `vendor/` sur le poste et tout envoyer en FTP, ne pas oublier `assets:install`, créer les comptes autrement que par la commande, et vérifier que l'hébergeur n'ajoute pas de vérification anti-robot aux appels de l'API.
