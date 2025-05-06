# 🛍️ Backoffice 5sur5sejours

**Back-office web pour le site e-commerce [5sur5sejours.com](https://5sur5sejours.com)**, une plateforme spécialisée dans les voyages pour enfants.  
Ce projet a été développé dans le cadre du Projet de Fin d'Études (PFE) en 2024.

## ✨ Fonctionnalités principales

- 🔐 Authentification sécurisée (admin & support)
- 👥 Gestion des utilisateurs
- 📦 Gestion des produits et des commandes
- 🧾 Gestion des factures
- 🎯 Dashboard analytique en temps réel
- 📬 Système de notifications

## 🛠️ Technologies

- Backend : **Symfony 5+**, **Doctrine ORM**
- Frontend : **Twig**, **Bootstrap**
- Base de données : **MySQL**
- Autres : **Git**, **Composer**

## 📂 Structure du projet

├── src/
│ ├── Controller/
│ ├── Entity/
│ ├── Repository/
│ ├── Form/
│ └── Services/
├── templates/
├── public/
└── config/


## 🚀 Lancer le projet en local

```bash
git clone https://github.com/votre-username/backoffice-5sur5sejours.git
cd backoffice-5sur5sejours
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start

👩‍💻 Auteurs

    Mariem Jlassi

    AppsFactor — Support technique

© 2024 – Projet académique (non commercial)
