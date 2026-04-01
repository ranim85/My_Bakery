<div align="center">
  <img src="https://img.icons8.com/color/150/000000/bread.png" alt="My Bakery Logo" style="margin-bottom: 20px;">
  
  # 🥖 My Bakery ERP 
  **Un Système de Gestion et de Caisse pour la Boulangerie Familiale**
  
  [![Symfony](https://img.shields.io/badge/Symfony-6.4-black?style=for-the-badge&logo=symfony)](#)
  [![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](#)
  [![MySQL](https://img.shields.io/badge/MySQL-Databases-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](#)
  [![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](#)
</div>

<br>

## 📖 L'Histoire du Projet (Le "Pourquoi")

Ce projet est né d'un constat et d'un besoin profondément personnel : **la gestion de la boulangerie familiale.** 

Comme beaucoup de commerces de proximité traditionnels, notre entreprise familiale souffrait de processus manuels dépassés : facturation sur papier, cahiers de notes perdus, difficulté à traquer les matières premières achetées, et surtout, **un manque de visibilité sur les écarts financiers en fin de journée**. Il était devenu très difficile de savoir exactement ce qui avait été produit, ce qui avait été vendu, et pourquoi l'argent dans le tiroir-caisse (Espèce) ne correspondait pas toujours aux prévisions. De plus, les roulements de vendeurs entre les équipes du Matin et du Soir causaient continuellement des conflits de données.

En tant qu'étudiant développeur, j'ai décidé de prendre le problème à bras-le-corps en construisant **My Bakery**, un logiciel sur-mesure pour digitaliser, sécuriser et fluidifier la gestion de l'entreprise familiale.

---

## 🚀 La Solution & Fonctionnalités (Le "Comment")

My Bakery n'est pas une simple application de saisie, c'est **un algorithme métier** conçu pour détecter et prévenir les pertes de revenus.

### 👥 1. Gestion des Équipes (Architecture Multi-Shifts)
- **Logique "Matin" ou "Soir"** : Les vendeurs déclarent explicitement leur service lors de leur connexion. Les données, les ventes et les erreurs sont strictement séparées entre le vendeur du matin et le vendeur du soir.
- **Droit à l'erreur (Undo)** : Un vendeur peut annuler sa saisie avant de clôturer sa caisse pour éviter qu'une simple faute de frappe ne fausse la comptabilité de la journée.
- **Clôture Numérique** : Le vendeur prend une photo de son "Tiroir Caisse" ou "Bloc-Notes" en fin de journée pour valider son service. La photo est stockée en sécurité sur le serveur.

### 📊 2. Dashboard Manager & Détection des Fraudes
- **Algorithme d'Écarts** : Le système calcule les revenus théoriques *(Quantité Vendue × Prix)* et les compare automatiquement à la *(Somme Déclarée)* en espèce.
- **Alertes Rouges/Jaunes** : Le Dashboard alerte immédiatement l'Administrateur visuellement s'il y a un **Déficit (Manquant)** ou un **Surplus**. Il précise la Date, l'Heure, et l'Identité du Vendeur (Matin ou Soir) qui a fauté.
- **Filtres Dynamiques :** Analyse comptable ultra-rapide par période (Aujourd'hui, Ce Mois, Cette Année, ou Date précise).

### 🛠️ 3. Gestion Ressources Humaines (Admin)
- L'Administrateur peut depuis son portail Créer de nouveaux vendeurs (Workers), les Licencier (Suppression sécurisée du compte pour couper les accès), ou forcer la réinitialisation de leurs mots de passe via le système de Hachage asynchrone sécurisé de Symfony.

---

## 🛠️ Stack Technique & Architecture

- **Framework Backend :** Symfony 6.4 (PHP)
- **Base de Données :** MySQL (Doctrine ORM pour les requêtes dynamiques)
- **Authentification :** Système Form Login Natif Symfony `(SecurityBundle)` + Hashage de pointe pour employés.
- **Frontend / UI :** Twig, Bootstrap 5, FontAwesome, Thème UI/UX "Bakery" sur mesure (Tonalités marron, dorées, mode lumineux de type point de vente).
- **Manipulation Fichiers :** Service d'Upload des reçus/factures avec bypass de limitation `fileinfo` via `getClientOriginalExtension()`.

---

## 💻 Installation Rapide (Local)

Si un autre développeur (ou un professeur) souhaite lancer le projet localement :

1. **Cloner le répertoire** :
```bash
git clone https://github.com/votre-compte/My_Bakery.git
cd My_Bakery
```

2. **Installer les dépendances PHP** :
```bash
composer install
```

3. **Environnement MySQL** :
- Vérifiez que votre serveur local (ex: XAMPP / WAMP) est allumé.
- Assurez-vous d'avoir accès à MySQL sur le port `3306`. La ligne dans votre fichier `.env` doit pointer vers votre configuration.

4. **Préparer la Base de Données** :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. **Créer le premier utilisateur et Lancer le Serveur** :
Chargez les données d'essai (Fixtures) et démarrez.
```bash
php bin/console doctrine:fixtures:load
symfony server:start
```

*L'application est configurée pour fonctionner sous le domaine par défaut `http://127.0.0.1:8000`.*

---

## 📜 Licence
Ce projet est développé de manière personnelle et propriétaire en tant que solution sur-mesure pour entreprise familiale. Tous droits réservés.
