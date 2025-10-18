## 🎮 Présentation de l’application

**Playcore** est une application backend développée avec [Symfony](https://symfony.com/) qui expose une API RESTful pour la gestion d’une base de données de jeux vidéo.

🧩 Elle repose sur les principes suivants :

- 📦 Architecture sans interface graphique : toutes les interactions se font via des appels API.
- 📘 Documentation interactive des routes grâce à [Swagger via NelmioApiDocBundle](https://symfony.com/bundles/NelmioApiDocBundle/current/index.html).
- 🛠️ Tests et exploration des endpoints directement depuis l’interface Swagger.

Cette approche permet une intégration facile avec des frontends ou des services tiers, tout en assurant une structure claire et sécurisée côté serveur.

## 🚀 Fonctionnalités principales

Playcore offre un ensemble de fonctionnalités robustes pour la gestion d’une base de données de jeux vidéo via une API Symfony RESTful :

- 📚 **Gestion de 4 entités principales** :
  - `VideoGame`
  - `Editor`
  - `Category`
  - `User`

- 🔄 **CRUD complet** pour chaque entité via des routes API documentées avec [NelmioApiDocBundle](https://symfony.com/bundles/NelmioApiDocBundle/current/index.html)

- 🔐 **Sécurité par JWT** ([LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle)) :
  - Les routes `create`, `update`, `delete` sont accessibles uniquement aux administrateurs
  - Les utilisateurs non authentifiés ou non autorisés reçoivent une erreur

- 📩 **Newsletter hebdomadaire** :
  - Envoi automatique tous les lundis à 8h30
  - Contient les jeux vidéo à venir dans les 7 prochains jours

- 🖼️ **Images de couverture intégrées dans les mails** :
  - Utilisation de CID (Content-ID) avec `embedFromPath()` pour garantir l’affichage dans les clients mail
  - Les images sont jointes de manière invisible et référencées dans le HTML via `<img src="cid:...">`

- 📅 **Scheduler intégré** :
  - Planification de l’envoi des newsletters via une commande Symfony

---

## 🧩 Structure des entités

| Entité     | Attributs principaux                                                  |
|------------|------------------------------------------------------------------------|
| VideoGame  | id, title, releaseDate, description, coverImage, editor_id            |
| Editor     | id, name, country                                                     |
| Category   | id, name                                                              |
| User       | id, username, email, password, roles, subscribe_to_newsletter         |

---

## 🔗 Relations entre entités

- Un `VideoGame` est toujours associé à un `Editor`
- Un `VideoGame` peut appartenir à plusieurs `Categories` (relation N:N via la table pivot `video_game_category`)
- Une `Category` peut regrouper plusieurs `VideoGames`

## 📬 Fonctionnement du système de newsletter

Playcore intègre un système de newsletter automatisé destiné aux utilisateurs ayant activé l’option `subscribe_to_newsletter = true`.

### 🕒 Fréquence d’envoi

- 📅 Tous les lundis à **8h30**
- 📦 Contenu du mail :
  - Liste des jeux vidéo à venir dans les **7 prochains jours**
  - Images de couverture intégrées via **CID (Content-ID)** avec `embedFromPath()` pour garantir leur affichage dans les clients mail
  - Template stylisé directement avec des balises `<style>` dans le fichier Twig

## 🛠️ Commandes disponibles

L’application propose deux commandes Symfony pour gérer l’envoi des mails :

```bash
php bin/console app:send-mail
# Envoie un mail simple sans image de couverture

php bin/console app:send-newsletter
# Envoie la newsletter hebdomadaire avec les images de couverture intégrées via CID


## 🛠️ Difficultés rencontrées

### 📁 Gestion des images de couverture

L’envoi de fichiers via `multipart/form-data` est incompatible avec la désérialisation automatique dans Symfony.

**Solution mise en place :**

- Pour la création (`create`) :
  - Utilisation directe des setters dans le contrôleur

- Pour la mise à jour (`update`) :
  - Séparation en deux routes distinctes :
    - `updateVideoGame` : pour les données simples désérialisables
    - `updateVideoGameCoverImage` : pour la mise à jour de l’image

---

### 🔁 Relations circulaires

Des problèmes de sérialisation liés à des boucles infinies ont été rencontrés lors de l’exposition des entités liées.

**Solution :**

- Utilisation de l’attribut [`MaxDepth`](https://symfony.com/doc/current/serializer.html#serializer-handling-serialization-depth) dans le Serializer Symfony pour limiter la profondeur de sérialisation.

---

### 📄 Pagination

La récupération des entités se fait via une méthode personnalisée `findAllWithPagination` dans les repositories.

- Le numéro de page est passé dynamiquement en paramètre dans la route
- Permet une navigation fluide et optimisée des résultats


## 📚 Documentation utile

- [Sérialiseur Symfony – MaxDepth](https://symfony.com/doc/current/serializer.html#serializer-handling-serialization-depth)  
  Pour limiter les boucles de sérialisation dans les relations entitées imbriquées.

- [Sensio – Sécurité avec @IsGranted](https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/security.html#isgranted)  
  Pour restreindre l’accès aux routes selon les rôles utilisateur avec des annotations.

- [Symfony Mailer](https://symfony.com/doc/current/mailer.html#creating-sending-messages)  
  Pour créer et envoyer des e-mails, y compris avec des pièces jointes et des templates Twig.

- [NelmioApiDocBundle (Swagger)](https://symfony.com/bundles/NelmioApiDocBundle/current/index.html)  
  Pour documenter automatiquement les routes de l’API avec Swagger.

- [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle)  
  Pour sécuriser l’API avec des tokens JWT.
