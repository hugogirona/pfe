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
# Lancer le serveur, la file d'attente, les logs et la compilation des assets
composer run dev
```

Cette commande démarre en parallèle :

- le serveur web (`php artisan serve`)
- le worker de file d'attente (`php artisan queue:listen`)
- le suivi des logs en direct (`php artisan pail`)
- la compilation des assets en mode watch (`npm run dev`)

L'application est accessible sur `http://localhost:8000`.

### Messagerie en temps réel (Laravel Reverb)

La messagerie s'appuie sur **Laravel Reverb** (serveur WebSocket). Pour
l'utiliser, installer puis démarrer le serveur Reverb :

```bash
# 1. Installer Reverb (renseigne les identifiants dans .env)
php artisan reverb:install

# 2. Démarrer le serveur WebSocket (dans un terminal dédié)
php artisan reverb:start
```

### File d'attente (jobs `ShouldQueue`)

Plusieurs traitements sont mis en file d'attente (`ShouldQueue`) : traitement
des images d'avatar et de média, envoi des e-mails (contact, vérification,
réinitialisation de mot de passe). Un worker doit donc tourner pour les
exécuter :

```bash
php artisan queue:work
```

> composer run dev` lance déjà un `queue:listen` ; cette commande n'est
> nécessaire que si vous démarrez le serveur seul (`php artisan serve`).



## Technologies utilisées

| Technologie                | Rôle                                   |
| -------------------------- | -------------------------------------- |
| **Laravel 13**             | Framework back-end                     |
| **Livewire 4**             | Composants dynamiques côté serveur     |
| **Alpine.js**              | Interactivité côté client              |
| **Tailwind CSS 4**         | Styles                                 |
| **Laravel Fortify**        | Authentification                       |
| **Laravel Reverb** + Echo  | Messagerie en temps réel (WebSockets)  |
| **Laravel Localization**   | Internationalisation (FR / EN / NL)    |
| **Intervention Image**     | Traitement des images (avatars)        |
| **Resend**                 | Envoi d'e-mails                        |
| **Pest 4**                 | Tests automatisés                      |
| **Vite**                   | Build des assets front-end             |
| **MySQL**                  | Base de données                        |





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



L'application est disponible en trois langues (FR / EN / NL), les URL étant
elles-mêmes traduites et préfixées par la locale (ex. `/fr/explorer`,
`/en/explore`).

| Page                          | Route (FR)                              | Accès                |
| ----------------------------- | --------------------------------------- | -------------------- |
| Accueil                       | `/`                                     | Public               |
| Explorer                      | `/explorer/{tab?}`                      | Public               |
| Profil                        | `/profil/{id}`                          | Connecté + vérifié   |
| Détail d'une annonce          | `/annonces/{id}`                        | Connecté + vérifié   |
| Paramètres du compte          | `/parametres/compte`                    | Connecté + vérifié   |
| Contact                       | `/contact`                              | Public               |
| Politique de confidentialité  | `/politique-de-confidentialite`         | Public               |
| Inscription                   | `/inscription`                          | Public (invité)      |
| Connexion                     | `/connexion`                            | Public (invité)      |
| Mot de passe oublié           | `/mot-de-passe-oublie`                  | Public (invité)      |
| Réinitialiser le mot de passe | `/reinitialiser-mot-de-passe/{token}`   | Public (invité)      |
| Vérification de l'e-mail      | `/verifier-email`                       | Connecté             |

La **messagerie**, la **publication / modification d'annonces** et
l'**édition du profil** ne sont pas des pages dédiées : ce sont des panneaux
et formulaires intégrés (overlay, modales, édition en ligne) chargés par
Livewire au sein des pages ci-dessus.

---

### Accueil

- Présentation de la plateforme
- Barre de recherche rapide
- CTA vers `/explorer`

---

### Explorer

- Filtres dans un tiroir (drawer) : ville, rayon, instruments, genres
- Onglet **Musiciens** : liste des profils correspondant aux filtres
- Onglet **Annonces** : liste des annonces correspondant aux filtres
- Pagination des résultats
- Le contenu est visible sans compte ; cliquer sur un profil, une annonce ou "Contacter" redirige vers `/connexion` avec une invitation à créer un compte

---

### Profil

- Informations du musicien : nom, ville, bio, instruments, genres, disponibilités, statut
- Galerie de médias (images, liens YouTube)
- Annonces publiées par cet utilisateur
- Actions sociales : suivre / demander en ami, bloquer
- Bouton "Contacter" qui ouvre la messagerie
- Sur son **propre** profil : édition en ligne (bio, avatar, instruments, genres, ajout de médias) directement sur la page

---

### Détail d'une annonce

- Toutes les informations de l'annonce : titre, type (Je cherche / Je propose), description, ville, rayon, instruments, genres
- Fiche de l'auteur avec lien vers son profil
- Annonces liées
- Bouton "Contacter l'auteur" qui ouvre la messagerie
- L'auteur peut publier / modifier son annonce via un formulaire intégré

---

### Paramètres du compte

- Informations personnelles : nom, ville, date de naissance, statut
- Confidentialité : visibilité du profil (public / privé)
- Modification de l'adresse e-mail et du mot de passe
- Suppression du compte

---

### Messagerie (overlay)

- Accessible depuis l'icône dans la navigation (avec badge de messages non lus) ou les boutons "Contacter"
- Liste des conversations
- Vue d'une conversation avec envoi de messages **en temps réel** (Laravel Reverb)

---

### Authentification

- **Inscription** : prénom, nom, email, mot de passe (protégée par honeypot anti-spam)
- **Connexion** : email, mot de passe
- **Vérification de l'e-mail** : saisie du code envoyé par e-mail après l'inscription
- **Mot de passe oublié / réinitialisation** : envoi d'un lien par e-mail puis définition d'un nouveau mot de passe

---

### Contact

- Formulaire de contact : nom, email, sujet, message (protégé par honeypot anti-spam)

---

### Politique de confidentialité

- Page légale statique présentant le traitement des données personnelles
