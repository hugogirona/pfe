# Giggr

Plateforme web qui connecte les musiciens et artistes passionnés — trouver un groupe, organiser des jam sessions, recruter des collaborateurs.



## Prérequis

- PHP 8.5+
- Composer
- Node.js 24+ et npm
- MySQL 8.4+



## Installation

```bash
# 1. Cloner le dépôt
git clone <url-du-repo>
cd pfe/giggr

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances JS
npm install

# 4. Copier le fichier d'environnement
cp .env.example .env

# 5. Générer la clé d'application
php artisan key:generate
```



## Configuration

Éditer `.env` et renseigner les paramètres de base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=giggr
DB_USERNAME=root
DB_PASSWORD=
```



## Base de données

```bash
# Créer les tables et insérer les données de test
php artisan migrate --seed
```



## Lancer le projet

```bash
# Terminal 1 — serveur Laravel
php artisan serve

# Terminal 2 — compilation des assets
npm run dev
```

L'application est accessible sur `http://localhost:8000`.

Le back-office Filament est accessible sur `http://localhost:8000/admin`.



## Technologies utilisées

| Technologie                   | Rôle                      |
| ----------------------------- | ------------------------- |
| **Laravel**                   | Framework back-end        |
| **Livewire**                  | Composants dynamiques     |
| **Alpine.js**                 | Interactivité côté client |
| **Tailwind CSS**              | Styles                    |
| **WebSockets** (Laravel Echo) | Messagerie en temps réel  |
| **MySQL**                     | Base de données           |
| **Filament**                  | Back-office admin         |





---

## Présentation du projet et cahier des charges

**Giggr** est une plateforme web qui connecte les musiciens amateurs d'une zone géographique. Les utilisateurs peuvent découvrir d'autres musiciens, publier et consulter des annonces, et communiquer via messagerie en temps réel.

**Le problème**

Les musiciens cherchent des collaborateurs via des groupes Facebook dispersés, des forums peu actifs ou le bouche-à-oreille. Il n'existe pas de plateforme centralisée, moderne et facile d'utilisation pour ce besoin spécifique en Belgique.

**La solution**

Giggr offre un espace dédié où les artistes peuvent :

- Créer un profil musical complet (instruments, genres, bio, disponibilités)
- Rechercher d'autres musiciens selon des critères géographiques et musicaux
- Publier et consulter des annonces pour recruter ou rejoindre un groupe
- Échanger via une messagerie en temps réel



## Parcours utilisateurs

### Persona 1 — Léa, 24 ans, guitariste

**Contexte** : Léa joue de la guitare depuis 5 ans et cherche à intégrer un groupe de rock alternatif.

**Parcours : Trouver un groupe**

1. Léa arrive sur la page d'accueil via Google
2. Elle clique sur "Voir les annonces" et arrive sur `/explorer`
3. Elle filtre par instrument "Guitare" et ville "Bruxelles"
4. Elle consulte les annonces et trouve "Groupe de rock alternatif cherche guitariste"
5. Elle clique sur l'annonce et lit les détails
6. Elle clique sur "Contacter" — le panneau de messagerie s'ouvre
7. Elle envoie un message au groupe
8. Le groupe lui répond, ils organisent une rencontre

### Persona 2 — Marc, 32 ans, batteur

**Contexte** : Marc cherche des musiciens pour jouer régulièrement.

**Parcours : Publier une annonce**

1. Marc est connecté, il clique sur "Publier une annonce"
2. Il remplit le formulaire : titre, type "Je cherche", instruments, ville, description
3. L'annonce est publiée et visible sur `/explorer` et sur son profil `/profil/{id}`
4. Des musiciens intéressés lui envoient un message via le panneau de messagerie
5. Marc répond et organise une session

### Persona 3 — Sophie, 28 ans, chanteuse

**Contexte** : Sophie cherche des musiciens pour former un groupe de pop.

**Parcours : Trouver des musiciens**

1. Sophie arrive sur `/explorer`
2. Elle bascule sur l'onglet "Musiciens"
3. Elle filtre par instrument et ville
4. Elle consulte plusieurs profils
5. Sur chaque profil qui l'intéresse, elle clique "Contacter"
6. Elle envoie des messages personnalisés depuis le panneau de messagerie
7. Trois musiciens répondent positivement, ils organisent une répétition



## Pages

| Page                    | Accès                                             |
| ----------------------- | ------------------------------------------------- |
| Accueil                 | Public                                            |
| Explorer                | Public (contacter → invitation à créer un compte) |
| Profil public           | Public (contacter → invitation à créer un compte) |
| Détail annonce          | Public (contacter → invitation à créer un compte) |
| Publier une annonce     | Connecté                                          |
| Modifier une annonce    | Connecté (propriétaire)                           |
| Mon profil              | Connecté                                          |
| Modifier mon profil     | Connecté                                          |
| Messagerie              | Connecté                                          |
| Connexion / Inscription | Public                                            |
| Contact                 | Public                                            |

---

### Accueil

- Présentation de la plateforme
- Barre de recherche rapide
- CTA vers `/explorer`

---

### Explorer

- Filtres : ville, rayon, instruments
- Onglet **Musiciens** : liste des profils correspondant aux filtres
- Onglet **Annonces** : liste des annonces correspondant aux filtres
- Le contenu est visible sans compte, mais cliquer sur "Contacter" redirige vers `/connexion` avec une invitation à créer un compte

---

### Profil public

- Informations du musicien : nom, âge, ville, bio, instruments, genres, disponibilités, statut
- Annonces actives publiées par cet utilisateur
- Bouton "Contacter" : visible mais redirige vers `/connexion` si non connecté

---

### Mon profil

- Vue de son propre profil public
- Bouton "Modifier" pour accéder au formulaire d'édition

### Modifier mon profil

- Formulaire : nom, âge, ville, bio, instruments, genres, disponibilités, statut, avatar

---

### Détail d'une annonce

- Toutes les informations de l'annonce
- Fiche de l'auteur avec lien vers son profil
- Bouton "Contacter l'auteur" : visible mais redirige vers `/connexion` si non connecté

---

### Publier / Modifier une annonce

- Formulaire : titre, type (Je cherche / Je propose), description, ville, rayon, instruments, genres

---

### Messagerie (overlay)

- Accessible depuis l'icône dans la navigation ou les boutons "Contacter"
- Liste des conversations
- Vue d'une conversation avec envoi de messages en temps réel

---

### Connexion / Inscription (/connexion)

- Onglet connexion : email, mot de passe
- Onglet inscription : prénom, nom, email, mot de passe

---

### Contact

- Formulaire de contact : nom, email, sujet, message
