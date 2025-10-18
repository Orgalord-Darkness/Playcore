🎮 Playcore — API Symfony de gestion de jeux vidéo

Playcore est une application Symfony orientée API REST, sans interface HTML. Elle permet de gérer une base de données de jeux vidéo, éditeurs, catégories et utilisateurs. L’API est documentée avec Swagger via NelmioApiDocBundle, et sécurisée par JWT grâce à LexikJWTAuthenticationBundle.

🧩 Présentation de l’application
🔧 Technologies principales

Symfony

Doctrine ORM

JWT Authentication (LexikJWTAuthenticationBundle)

NelmioApiDocBundle pour Swagger

Symfony Mailer

Scheduler (cron ou SchedulerBundle)

🗃️ Les entités

L’application repose sur 4 entités principales :

Entité	Attributs clés
VideoGame	id, title, releaseDate, description, coverImage, editor, categories
Editor	id, name, country
Category	id, name
User	id, username, email, password, roles, subcription_to_newsletter
🔗 Relations entre entités

VideoGame possède un Editor (ManyToOne)

VideoGame possède plusieurs Categories (ManyToMany)

Category est liée à plusieurs VideoGame (ManyToMany inversée)

Table pivot : video_game_category

⚙️ Fonctionnement global
📡 API CRUD

Chaque entité dispose de son propre controller exposant les routes CRUD.

Les routes sont documentées et testables dans Swagger.

La pagination est implémentée avec une méthode findAllWithPagination() côté repository, personnalisable via un paramètre ?page=....

🔐 Sécurité

Authentification par JWT (LexikJWT).

Les routes de type POST, PUT, DELETE sont protégées par rôle ROLE_ADMIN.

En cas d’absence ou d’invalidité du token, un message "Pas de JWT" est retourné.

📬 Newsletter hebdomadaire automatisée

Un scheduler exécute une commande chaque lundi à 8h30 pour envoyer aux utilisateurs abonnés (subscribe_to_newsletter = true) la liste des jeux vidéo qui sortent dans les 7 prochains jours.

📎 Intégration des images de couverture

Les images (coverImage) sont jointes de façon invisible dans le mail via des CID.

🧠 Comprendre le CID (Content-ID)

Un CID permet d’afficher une image directement dans un mail HTML :

<img src="cid:mon_image_unique">


L’image est jointe au mail (mais non visible comme pièce jointe). Exemple d’intégration dans le code :

$cid = uniqid('vg_', true);
$email->embedFromPath($imagePath, $cid);


Puis utilisée dans Twig :

<img src="cid:{{ cid }}">


✅ Cela contourne le problème des images externes qui ne s’affichent pas dans les clients mail.

🛠️ Commandes personnalisées
Commande	Description
php bin/console app:send-mail	Envoie un mail sans images de couverture
php bin/console app:send-newsletter	Envoie la newsletter avec les images jointes
📤 Gestion des fichiers uploadés (coverImage)

Lors de l’upload d’image de couverture :

🧾 Fichier original
$coverImage->getClientOriginalName(); // ex: the-legend-of-zelda.jpg


⚠️ Ne pas utiliser ce nom tel quel (peut contenir des espaces ou caractères spéciaux).

🪛 Nom sans extension
pathinfo($filename, PATHINFO_FILENAME); // ex: the-legend-of-zelda

🚧 Difficultés rencontrées & solutions
🔁 Problèmes de relation circulaire

Lors de la sérialisation des entités, des boucles infinies apparaissaient à cause des relations bidirectionnelles.

✅ Résolu avec :

#[MaxDepth(1)]


Plus d'infos : Serializer Symfony – MaxDepth

📦 Incompatibilité multipart/form-data et désérialisation

Lors de la création/mise à jour de VideoGame avec une image :

Le Content-Type: multipart/form-data n’est pas compatible avec la désérialisation automatique de Symfony.

✅ Solutions mises en place :

Create :

Pas de désérialisation automatique

Utilisation directe des setters dans le controller

Update :

Route updateVideoGame pour les données simples (désérialisées)

Route updateVideoGameCoverImage pour mettre à jour uniquement l’image

📂 Structure des controllers

Chaque entité possède un controller avec ses routes CRUD

MailerController permet d’afficher un aperçu visuel de la newsletter envoyée (newsletter2.html.twig)

AuthController gère la route de connexion JWT

📚 Documentation utile

🔒 Sécurité via @IsGranted

📤 Symfony Mailer - Envoi de mails

📘 Serializer Symfony - MaxDepth

📄 NelmioApiDoc - Documentation Swagger

🚀 Démarrage rapide
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console lexik:jwt:generate-keypair

🧪 Tests via Swagger

L’API est testable via Swagger à l’adresse :

/api/doc

👨‍💻 Auteur

Projet réalisé par [Ton nom ou pseudo]
