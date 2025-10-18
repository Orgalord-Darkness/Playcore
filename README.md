# 🎮 Playcore — Application Symfony de gestion de jeux vidéo
## 🧩 Présentation du projet

Playcore est une application développée avec Symfony, utilisant NelmioApiDoc / Swagger pour documenter et tester ses routes API.
C’est une application de gestion de jeux vidéo sans template d’affichage, reposant sur une API REST complète.

L’application permet :

🎮 De gérer les entités jeux vidéo, éditeurs, catégories et utilisateurs.

📅 D’envoyer automatiquement une newsletter chaque lundi à 8h30, présentant les jeux à venir dans les 7 prochains jours.

🖼️ D’afficher les images de couverture dans les e-mails grâce à l’intégration via CID (Content-ID).

## ⚙️ Fonctionnement général
### 🧱 Entités principales

VideoGame → id, title, releaseDate, description, coverImage, editor_id

Un éditeur

Plusieurs catégories

Editor → id, name, country

Plusieurs jeux vidéo

Category → id, name

Plusieurs jeux vidéo (relation N:N via la table pivot video_game_category)

User → id, username, email, password, roles, subscribe_to_newsletter

Peut être abonné à la newsletter

### 👨‍💻 Contrôleurs principaux

VideoGameController → CRUD des jeux vidéo.

EditorController → CRUD des éditeurs.

CategoryController → CRUD des catégories.

UserController → CRUD des utilisateurs.

MailerController → Prévisualisation du mail newsletter2.html.twig.

AuthController → Gestion du login et génération du token JWT.


### 🚀 API & Swagger

Toutes les routes CRUD sont exposées via Swagger (NelmioApiDocBundle).

Les données des requêtes create et update sont désérialisées (sauf exceptions).

Les routes read, update et delete sont réservées aux administrateurs.

L’authentification utilise LexikJWTAuthenticationBundle (JWT).

#### 🔐 Si un utilisateur non admin tente d’accéder à une route protégée, le message « Pas de JWT » est affiché.

### 🗓️ Tâches planifiées & newsletter

Un scheduler envoie chaque lundi à 8h30 un e-mail contenant :

La liste des jeux vidéo à venir dans les 7 prochains jours.

Les images de couverture intégrées via CID.

Ces e-mails sont envoyés uniquement aux utilisateurs ayant subscribe_to_newsletter = true.

Deux commandes console ont été créées :

php bin/console app:send-mail → Envoi d’un mail sans images.

php bin/console app:send-newsletter → Envoi de la newsletter complète avec images.


🧰 Gestion des fichiers uploadés
📸 Méthode getClientOriginalName()

$coverImage est une instance de UploadedFile.

getClientOriginalName() retourne le nom original du fichier envoyé (ex. elden-ring.jpg).

⚠️ Ce nom peut contenir des caractères spéciaux — il ne doit jamais être utilisé tel quel pour enregistrer le fichier.

🪶 Fonction pathinfo(..., PATHINFO_FILENAME)

Cette fonction PHP retourne le nom du fichier sans extension.
Exemple : pathinfo('elden-ring.jpg', PATHINFO_FILENAME) renvoie elden-ring.

## 🧠 Difficultés rencontrées
### 📂 Upload d’images (coverImage)

Le Content-Type multipart/form-data est incompatible avec la désérialisation automatique de Symfony.

✅ Solutions mises en place :

Pour create → utilisation directe des setters.

Pour update → création de deux routes distinctes :

updateVideoGame → pour les données simples.

updateVideoGameCoverImage → pour l’image uniquement.

### ✉️ Gestion des images dans les e-mails
Les images ne pouvaient pas être affiché dans le mail avec un simple src puisque le mail ne fait pas parti de l'application 

#### 🧠 Solution : Implémentation des images en pièces jointes cachées et Content ID (CID)  

📎 Image intégrée via CID
Exemple : <img src="cid:mon_image">

Le CID agit comme un identifiant unique pour une image mise en pièce jointe cachée.
L’image est envoyée en pièce jointe cachée, puis affichée dans le corps du mail via son identifiant donc el CID.

#### 🔍 Fonctionnement du code d’intégration

Récupère le chemin du dossier des images de couverture :
$coverImageDir = $this->params->get('cover_image_directory');

Pour chaque jeu, récupère le fichier image et construit son chemin complet.

Si l’image existe :

Génère un CID unique ($cid = uniqid('vg_', true)),

Intègre l’image au mail avec embedFromPath(),

Associe le CID à l’ID du jeu pour le template.

➡️ Résultat : Les images s’affichent correctement dans les e-mails, même en dehors de l’application.


### 🔁 Relations circulaires & pagination

Une méthode findAllWithPagination() dans chaque repository gère la pagination.

L’utilisateur peut changer la page via un paramètre dans la route.

Des erreurs de relations circulaires ont été résolues grâce à l’annotation :
@MaxDepth(1)
permettant de limiter les boucles de sérialisation.


### 📚 Documentations qui m'ont été utile

📘 Symfony Serializer (MaxDepth)
➡️ https://symfony.com/doc/current/serializer.html#serializer_handling-serialization-depth

🔒 Sécurité avec annotations IsGranted
➡️ https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/security.html#isgranted

📤 Symfony Mailer
➡️ https://symfony.com/doc/current/mailer.html#creating-sending-messages

## 🧾 Conclusion

Playcore est une application Symfony complète alliant :

#### 🧩 Gestion CRUD via API.

#### 🔐 Authentification sécurisée par JWT.

#### ✉️ Automatisation d’e-mails enrichis (images intégrées via CID).

#### ⚙️ Pagination et sérialisation optimisées.

Ce projet a permis d’approfondir :

L’utilisation du Mailer de Symfony.

La gestion avancée des fichiers uploadés.

Les relations entre entités et la prévention des boucles de sérialisation.
