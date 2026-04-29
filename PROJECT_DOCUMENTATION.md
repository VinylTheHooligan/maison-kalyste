# Documentation Complète - Maison Kalyste

## Table des matières
1. [Vue d'ensemble du projet](#vue-densemble)
2. [Stack technique](#stack-technique)
3. [Architecture du projet](#architecture)
4. [Entités et modèle de données](#entités)
5. [Contrôleurs et endpoints](#contrôleurs)
6. [Sécurité](#sécurité)
7. [Configuration](#configuration)
8. [Fonctionnalités principales](#fonctionnalités)
9. [Enums et états](#enums)
10. [Base de données](#base-de-données)
11. [Authentification et autorisations](#authentification)

---

## Vue d'ensemble

**Projet:** Maison Kalyste  
**Type:** Application e-commerce avec gestion de catalogue produits  
**Framework:** Symfony 7.4  
**PHP:** >=8.2  
**Base de données:** PostgreSQL 16 (Docker)  
**Date de dernière mise à jour:** Avril 2026

### Objectif du projet
Maison Kalyste est une plateforme e-commerce permettant:
- Consulter et acheter des produits (produits vintage)
- Gérer un panier et passer des commandes
- Authentifier les utilisateurs et gérer leurs profils
- Gérer les adresses de livraison et de facturation
- Traiter les paiements
- Gérer les coupons de réduction
- S'inscrire à une newsletter
- Soumettre des messages de contact

---

## Stack technique

### Backend
- **Framework principal:** Symfony 7.4
- **ORM:** Doctrine 3.6 avec mapping par attributs
- **Migration:** Doctrine Migrations 3.7
- **Validation:** Symfony Validator
- **Sécurité:** Symfony Security Bundle 7.4
- **Mailer:** Symfony Mailer 7.4
- **Messenger:** Symfony Messenger (queue asynchrone)
- **Notifier:** Symfony Notifier 7.4
- **Rate Limiter:** Symfony Rate Limiter 7.4

### Frontend
- **Moteur de templates:** Twig 3.0
- **Framework JS:** Stimulus 2.35 (Symfony UX Stimulus Bundle)
- **Turbo:** Symfony UX Turbo 2.35 (navigation AJAX)
- **Tailwind CSS:** Symfonycasts Tailwind Bundle 0.12
- **Asset Management:** Symfony Asset Mapper 7.4
- **Controllers JS:** Stimulus controllers dans `/assets/controllers/`

### Base de données
- **PostgreSQL 16** (container Docker)
- **Stratégie de versioning:** Doctrine Migrations
- **Transactions:** Support des savepoints

### Outils de développement
- **Profiler web:** Symfony Web Profiler (dev/test)
- **Debugging:** Symfony DebugBundle
- **Testing:** PHPUnit avec Zenstruck Foundry
- **Fixtures:** Doctrine Fixtures Bundle
- **Code generation:** Symfony Maker Bundle

### Infrastructure
- **Docker Compose:** Pour orchestration locale
- **Configuration:** Variables d'environnement (.env)

---

## Architecture

### Structure des dossiers

```
.
├── assets/                  # Ressources frontend
│   ├── app.js              # Point d'entrée principal
│   ├── stimulus_bootstrap.js
│   ├── controllers/        # Stimulus controllers
│   │   ├── carousel_controller.js
│   │   ├── cart_controller.js
│   │   ├── csrf_protection_controller.js
│   │   └── hello_controller.js
│   ├── styles/             # Feuilles de styles
│   │   └── app.css
│   └── vendor/             # Dépendances frontend
│
├── bin/
│   ├── console             # Commandes Symfony
│   └── phpunit             # Runner de tests
│
├── config/                 # Configuration Symfony
│   ├── bundles.php         # Bundles activés
│   ├── routes.yaml         # Routage principal
│   ├── services.yaml       # Services
│   ├── preload.php
│   ├── reference.php
│   ├── packages/           # Configuration spécifique des packages
│   │   ├── asset_mapper.yaml
│   │   ├── cache.yaml
│   │   ├── csrf.yaml
│   │   ├── debug.yaml
│   │   ├── doctrine.yaml
│   │   ├── doctrine_migrations.yaml
│   │   ├── framework.yaml
│   │   ├── mailer.yaml
│   │   ├── messenger.yaml
│   │   ├── monolog.yaml
│   │   ├── notifier.yaml
│   │   ├── rate_limiter.yaml
│   │   ├── routing.yaml
│   │   ├── security.yaml
│   │   ├── symfonycasts_tailwind.yaml
│   │   ├── translation.yaml
│   │   ├── twig.yaml
│   │   ├── ux_turbo.yaml
│   │   ├── validator.yaml
│   │   ├── web_profiler.yaml
│   │   └── zenstruck_foundry.yaml
│   └── routes/
│       ├── framework.yaml
│       ├── security.yaml
│       └── web_profiler.yaml
│
├── migrations/             # Doctrine migrations
│   ├── Version20250423101317.php
│   ├── Version20250424092333.php
│   ├── Version20250426165108.php
│   └── Version20250429130342.php
│
├── public/                 # Dossier public (web root)
│   ├── index.php          # Point d'entrée
│   ├── font/
│   └── image/
│
├── src/                    # Code source (PSR-4: App\)
│   ├── Kernel.php         # Kernel Symfony
│   ├── Controller/        # Contrôleurs
│   │   ├── HomeController.php
│   │   ├── SecurityController.php
│   │   ├── InformationsController.php
│   │   └── NewsletterController.php
│   ├── Entity/            # Entités Doctrine
│   ├── Enum/              # Enums PHP
│   ├── Factory/           # Factories (Foundry)
│   ├── Form/              # Form types
│   ├── Repository/        # Repositories Doctrine
│   └── Story/             # Stories (Foundry)
│
├── templates/             # Templates Twig
│   ├── base.html.twig     # Template de base
│   ├── _header.html.twig
│   ├── _footer.html.twig
│   ├── home/
│   ├── informations/
│   ├── newsletter/
│   ├── security/
│   └── returns/
│
├── tests/                 # Tests
│   └── bootstrap.php
│
├── translations/          # Fichiers de traduction
├── var/                   # Fichiers variables (cache, logs)
├── vendor/                # Dépendances Composer
│
├── composer.json          # Configuration Composer
├── phpunit.dist.xml       # Configuration PHPUnit
├── importmap.php          # Map d'imports JavaScript
├── compose.yaml           # Docker Compose
├── compose.override.yaml  # Docker Compose override
└── .env                   # Configuration d'environnement
```

---

## Entités

### Modèle de données

Voici le diagramme des relations entre entités:

#### 1. **User** (Utilisateur)
L'entité principale pour l'authentification et la gestion des utilisateurs.

**Champs:**
- `id` (int, PK)
- `email` (string, unique) - Identifiant de connexion
- `password` (string) - Mot de passe hashé
- `firstName` (string) - Prénom
- `lastName` (string) - Nom de famille
- `roles` (array) - Rôles de l'utilisateur
- `isVerified` (bool) - Email vérifié?
- `createdAt` (DateTimeImmutable)
- `updatedAt` (DateTimeImmutable, nullable)
- `lastLoginAt` (DateTimeImmutable, nullable)

**Relations:**
- `OneToMany` → `Order` (Commandes)
- `OneToMany` → `Address` (Adresses)
- `OneToOne` → `Cart` (Panier)

---

#### 2. **Product** (Produit)
Représente les produits disponibles à la vente.

**Champs:**
- `id` (int, PK)
- `sku` (string, unique) - Code produit
- `name` (string) - Nom du produit
- `slug` (string, unique) - URL slug
- `description` (text) - Description détaillée
- `price` (int) - Prix en centimes
- `stockQuantity` (int) - Quantité en stock
- `inStock` (bool) - Disponible?
- `featured` (bool) - Produit en vedette?
- `attributes` (array, nullable) - Attributs JSON
- `createdAt` (DateTimeImmutable)
- `updatedAt` (DateTimeImmutable, nullable)

**Relations:**
- `ManyToOne` → `Category` (Catégorie)
- `OneToMany` → `ProductImage` (Images)
- `OneToMany` → `InventoryMovement` (Mouvements d'inventaire)

**Indexes:**
- Index sur `name`
- Index sur `category_id`
- Unique constraint sur `sku`
- Unique constraint sur `slug`

---

#### 3. **Category** (Catégorie)
Catégorisation des produits (possiblement hiérarchique).

**Champs:**
- `id` (int, PK)
- `name` (string) - Nom de la catégorie
- `description` (text, nullable)
- `slug` (string, unique)
- `parentId` (int, nullable) - ID de la catégorie parente
- `createdAt` (DateTimeImmutable)
- `updatedAt` (DateTimeImmutable, nullable)

**Relations:**
- `OneToMany` → `Product` (Produits)

**Indexes:**
- Index sur `name`

---

#### 4. **Cart** (Panier)
Représente le panier d'un utilisateur.

**Champs:**
- `id` (int, PK)
- `status` (CartStatus enum) - État du panier
- `createdAt` (DateTimeImmutable)
- `updatedAt` (DateTimeImmutable, nullable)

**Statuts possibles (CartStatus):**
- `ACTIVE` - Panier actif
- `ABANDONNED` - Panier abandonné
- `CONVERTED` - Converti en commande

**Relations:**
- `OneToOne` ← `User` (mappedBy: 'cart')
- `OneToMany` → `CartItem` (Articles du panier)

---

#### 5. **CartItem** (Article du panier)
Articles contenus dans un panier.

**Champs:**
- `id` (int, PK)
- `quantity` (int) - Quantité
- `unitPrice` (int) - Prix unitaire en centimes
- `createdAt` (DateTimeImmutable)

**Relations:**
- `ManyToOne` → `Cart` (Panier)

---

#### 6. **Order** (Commande)
Représente une commande passée par un utilisateur.

**Champs:**
- `id` (int, PK)
- `orderNumber` (string, unique) - Numéro de commande
- `status` (OrderStatus enum) - État de la commande
- `subtotal` (int) - Sous-total en centimes
- `shipping` (int) - Frais de port en centimes
- `tax` (int) - Taxes en centimes
- `total` (int) - Total en centimes
- `shippingAddressSnapshot` (array) - Adresse de livraison (snapshot)
- `billingAddressSnapshot` (array) - Adresse de facturation (snapshot)
- `createdAt` (DateTimeImmutable)
- `updatedAt` (DateTimeImmutable, nullable)

**Statuts possibles (OrderStatus):**
- `PENDING` - En attente
- `PROCESSING` - Traitement
- `PAID` - Payée
- `SHIPPED` - Expédiée
- `DELIVERED` - Livrée
- `CANCELLED` - Annulée
- `REFUNDED` - Remboursée

**Relations:**
- `ManyToOne` → `User` (Propriétaire)
- `OneToMany` → `OrderItem` (Articles)
- `OneToOne` → `Payment` (Paiement)

---

#### 7. **OrderItem** (Article de commande)
Articles contenus dans une commande.

**Champs:**
- `id` (int, PK)
- `sku` (string) - Code produit
- `quantity` (int) - Quantité
- `unitPrice` (int) - Prix unitaire en centimes
- `totalPrice` (int) - Prix total en centimes
- `productId` (int, nullable) - ID du produit (peut être supprimé)
- `productName` (string) - Nom du produit (snapshot)

**Relations:**
- `ManyToOne` → `Order` (Commande)

---

#### 8. **Payment** (Paiement)
Enregistrement des paiements associés à une commande.

**Champs:**
- `id` (int, PK)
- `provider` (string) - Fournisseur de paiement (ex: Stripe)
- `providerPaymentId` (string) - ID unique chez le fournisseur
- `status` (string) - État du paiement
- `amount` (int) - Montant en centimes
- `metadata` (array, nullable) - Métadonnées JSON
- `rawResponse` (array, nullable) - Réponse brute du fournisseur
- `paidAt` (DateTimeImmutable, nullable)
- `createdAt` (DateTimeImmutable)

**Statuts possibles (PaymentStatus):**
- `PENDING` - En attente
- `SUCCEEDED` - Réussi
- `FAILED` - Échoué
- `REFUNDED` - Remboursé

**Relations:**
- `OneToOne` ← `Order` (mappedBy: 'payment')

---

#### 9. **Address** (Adresse)
Adresses de livraison et de facturation des utilisateurs.

**Champs:**
- `id` (int, PK)
- `fullName` (string) - Nom complet
- `phone` (string) - Numéro de téléphone
- `line1` (string) - Ligne 1 de l'adresse
- `line2` (string, nullable) - Ligne 2 (appartement, etc.)
- `city` (string) - Ville
- `postalCode` (string) - Code postal
- `isDefaultShipping` (bool) - Adresse de livraison par défaut?
- `isDefaultBilling` (bool) - Adresse de facturation par défaut?
- `createdAt` (DateTimeImmutable)
- `updatedAt` (DateTimeImmutable, nullable)

**Relations:**
- `ManyToOne` → `User` (Propriétaire)

---

#### 10. **Coupon** (Coupon de réduction)
Gestion des codes de réduction et coupons.

**Champs:**
- `id` (int, PK)
- `code` (string, unique) - Code du coupon
- `type` (string) - Type (percentage, fixed, etc.)
- `value` (int) - Valeur (en % ou en centimes)
- `usageLimit` (int, nullable) - Limite d'utilisation
- `usedCount` (int) - Nombre d'utilisations
- `startsAt` (DateTimeImmutable, nullable)
- `expiresAt` (DateTimeImmutable, nullable)
- `active` (bool) - Coupon actif?
- `conditions` (array, nullable) - Conditions JSON

---

#### 11. **InventoryMovement** (Mouvement d'inventaire)
Enregistrement des mouvements de stock.

**Champs:**
- `id` (int, PK)
- `change` (int) - Changement de quantité
- `reason` (string) - Raison du changement
- `reference` (string, nullable) - Référence (numéro de commande, etc.)
- `createdAt` (DateTimeImmutable)

**Relations:**
- `ManyToOne` → `Product` (Produit affecté)

**Raisons possibles (InventoryMovementReason):**
- `SALE` - Vente
- `REFUND` - Remboursement
- `CANCELLED_ORDER` - Commande annulée
- `RETURN` - Retour
- `RESTOCK` - Réapprovisionment
- `MANUAL_ADJUSTMENT` - Ajustement manuel
- `INVENTORY_COUNT` - Inventaire
- `DAMAGE` - Dégât
- `LOST` - Perdu
- `STOLEN` - Volé
- `INITIAL_STOCK` - Stock initial
- `MIGRATION` - Migration

---

#### 12. **ProductImage** (Image de produit)
Images associées aux produits.

**Champs:**
- `id` (int, PK)
- `url` (string) - URL de l'image
- `alt` (string, nullable) - Texte alternatif
- `position` (int) - Position de tri
- `mimeType` (string) - Type MIME
- `createdAt` (DateTimeImmutable)

**Relations:**
- `ManyToOne` → `Product` (Produit)

---

#### 13. **Slide** (Diaporama)
Diaporama de la page d'accueil.

**Champs:**
- `id` (int, PK)
- `image` (string) - URL de l'image
- `title` (string, nullable) - Titre
- `description` (string, nullable) - Description
- `link` (string, nullable) - Lien cible
- `hasLink` (bool) - Possède un lien?
- `cta` (string) - Call-to-action (bouton)

---

#### 14. **NewsletterSubscriber** (Abonné newsletter)
Abonnés à la newsletter.

**Champs:**
- `id` (int, PK)
- `email` (string, unique) - Adresse email
- `subscribedAt` (DateTimeImmutable) - Date d'inscription

---

#### 15. **ContactMessage** (Message de contact)
Messages reçus via le formulaire de contact.

**Champs:**
- `id` (int, PK)
- `email` (string) - Email de l'expéditeur
- `name` (string) - Nom de l'expéditeur
- `topic` (ContactTopic enum) - Sujet du message
- `message` (text) - Corps du message
- `isProcessed` (bool) - Traité?
- `createdAt` (DateTimeImmutable)

**Sujets possibles (ContactTopic):**
- Voir le code source pour les valeurs exactes

---

## Contrôleurs

### 1. **HomeController**
Point d'entrée principal du site.

**Route:** `GET /`  
**Nom:** `app_home`  
**Méthode:** `index(SlideRepository $slideRepository)`

**Actions:**
- Récupère tous les slides du diaporama
- Rend le template `home/index.html.twig`

```php
#[Route('/', name: 'app_home')]
public function index(SlideRepository $slideRepository): Response
{
    $slides = $slideRepository->findAll();
    return $this->render('home/index.html.twig', [
        'slides' => $slides,
    ]);
}
```

---

### 2. **SecurityController**
Gestion de l'authentification utilisateur.

#### Login
**Route:** `GET/POST /login`  
**Nom:** `app_login`

**Fonctionnalités:**
- Formulaire de connexion
- Affiche les erreurs d'authentification
- CSRF protection activée
- Récupère le dernier username entré

```php
#[Route(path: '/login', name: 'app_login')]
public function login(AuthenticationUtils $authenticationUtils): Response
{
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error,
    ]);
}
```

#### Logout
**Route:** `GET /logout`  
**Nom:** `app_logout`

**Fonctionnalités:**
- Terminaison de session
- Interceptée par le firewall (ne contient que `throw new LogicException`)

```php
#[Route(path: '/logout', name: 'app_logout')]
public function logout(): void
{
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
}
```

---

### 3. **InformationsController**
Pages d'informations légales et de contact.

**Groupe de routes:** `/informations` (nom: `app_informations`)

#### Pages légales

##### Legal
**Route:** `GET /informations/legal`  
**Nom:** `app_informations_legal`  
Rend: `informations/legal.html.twig`

##### CGU (Conditions Générales d'Utilisation)
**Route:** `GET /informations/cgu`  
**Nom:** `app_informations_cgu`  
Rend: `informations/cgu.html.twig` / `informations/returns.html.twig`

Note: Le rendu "returns.html.twig" semble être un bug (devrait être "cgu.html.twig")

##### CGV (Conditions Générales de Vente)
**Route:** `GET /informations/cgv`  
**Nom:** `app_informations_cgv`  
Rend: `informations/cgv.html.twig`

##### Privacy (Politique de confidentialité)
**Route:** `GET /informations/privacy`  
**Nom:** `app_informations_privacy`  
Rend: `informations/privacy.html.twig`

##### Returns (Politique de retour)
**Route:** `GET /informations/returns`  
**Nom:** `app_informations_returns`  
Rend: `returns/privacy.html.twig`

#### Formulaire de contact

**Route:** `GET/POST /informations/contact`  
**Nom:** `app_informations_contact`

**Fonctionnalités:**
- Formulaire de contact avec validation
- **Rate limiting:** Protection contre les abus (limite d'appels par IP)
- **Honeypot:** Champ "website" caché pour détecter les bots
- Enregistrement du message en base de données
- Messages flash de confirmation/erreur

**Paramètres de la méthode:**
- `Request $request` - Requête HTTP
- `EntityManagerInterface $em` - Gestionnaire d'entités
- `RateLimiterFactory $contactFormLimiter` - Factory de rate limiter

```php
#[Route('/contact', name: '_contact')]
public function contact(Request $request, EntityManagerInterface $em, 
                        RateLimiterFactory $contactFormLimiter): Response
{
    $limiter = $contactFormLimiter->create($request->getClientIp());
    if (!$limiter->consume(1)->isAccepted()) {
        throw new TooManyRequestsHttpException(null, 'Trop de tentatives...');
    }

    $contact = new ContactMessage();
    $form = $this->createForm(ContactMessageType::class, $contact);
    $form->handleRequest($request);

    // Honeypot check
    if ($form->has('website') && $form->get('website')->getData()) {
        return $this->redirectToRoute('app_home');
    }

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($contact);
        $em->flush();
        // ...
    }
    // ...
}
```

---

### 4. **NewsletterController**
Gestion des abonnements à la newsletter.

**Groupe de routes:** `/newsletter` (nom: `app_newsletter`)

#### Subscribe
**Route:** `POST /newsletter/subscribe`  
**Nom:** `app_newsletter_subscribe`

**Fonctionnalités:**
- Inscription à la newsletter
- Validation de l'email
- Vérification des doublons
- Messages flash de succès/erreur
- Redirection vers la page d'accueil

**Logique:**
1. Récupère l'email du formulaire
2. Vérifie que l'email n'existe pas déjà
3. Crée une nouvelle instance de `NewsletterSubscriber`
4. Persiste en base de données
5. Affiche un message de confirmation

```php
#[Route('/subscribe', name: '_subscribe')]
public function index(Request $request, EntityManagerInterface $em): Response
{
    $email = $request->request->get('email');

    if (!$email) {
        $this->addFlash('error', 'Veuillez entrer un mail valide.');
        return $this->redirectToRoute('app_home');
    }

    $existing = $em->getRepository(NewsletterSubscriber::class)
                   ->findOneBy(['email' => $email]);

    if ($existing) {
        $this->addFlash('error', 'Veuillez entrer un mail valide.');
        return $this->redirectToRoute('app_home');
    }

    $subscriber = new NewsletterSubscriber();
    $subscriber->setEmail($email);
    $em->persist($subscriber);
    $em->flush();

    $this->addFlash('success', 'Merci ! Vous êtes maintenant inscrit à la newsletter.');
    return $this->redirectToRoute('app_home');
}
```

---

## Sécurité

### Authentification

#### Firewall Configuration
Défini dans `config/packages/security.yaml`:

```yaml
firewalls:
    dev:
        pattern: ^/(_profiler|_wdt|assets|build)/
        security: false
    main:
        lazy: true
        provider: app_user_provider
        form_login:
            login_path: app_login
            check_path: app_login
            enable_csrf: true
        logout:
            path: app_logout
```

**Détails:**
- **dev firewall:** Désactive la sécurité pour les outils de développement et assets
- **main firewall:** Firewall principal avec authentification par formulaire
  - **Lazy loading:** Les utilisateurs ne sont chargés que s'il y en a besoin
  - **Form login:** Login via `/login` avec POST vers la même URL
  - **CSRF protection:** Activée par défaut
  - **Logout:** Déconnexion via `/logout`

#### User Provider
```yaml
providers:
    app_user_provider:
        entity:
            class: App\Entity\User
            property: email
```

Les utilisateurs se connectent avec leur **email** comme identifiant unique.

#### Password Hashing
```yaml
password_hashers:
    Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
```

Utilise l'algorithme **bcrypt** par défaut (auto-détecté selon la version PHP).

### Permissions d'accès

**Access control configuration:**
```yaml
access_control:
    # - { path: ^/admin, roles: ROLE_ADMIN }
    # - { path: ^/profile, roles: ROLE_USER }
```

Actuellement **désactivé** (commenté). Les rôles doivent être implémentés selon les besoins.

### Protection CSRF

- **Activée globalement** dans `framework.yaml`
- **Form login:** CSRF validation activée
- **Contact form:** CSRF protection automatique Symfony

### Rate Limiting

Implémenté sur le formulaire de contact via `RateLimiterFactory`:

```php
$limiter = $contactFormLimiter->create($request->getClientIp());
if (!$limiter->consume(1)->isAccepted()) {
    throw new TooManyRequestsHttpException();
}
```

### Protection Anti-Bot

**Honeypot pattern** dans le formulaire de contact:
- Champ invisible "website"
- Si rempli → redirection vers home (présumé bot)

---

## Configuration

### Framework
**Fichier:** `config/packages/framework.yaml`

```yaml
framework:
    secret: '%env(APP_SECRET)%'
    session: true
```

- **Secret:** Utilisé pour CSRF tokens, etc.
- **Session:** Activée automatiquement lors de la lecture/écriture

### Doctrine ORM
**Fichier:** `config/packages/doctrine.yaml`

```yaml
doctrine:
    dbal:
        url: '%env(resolve:DATABASE_URL)%'
        use_savepoints: true
    orm:
        auto_generate_proxy_classes: true
        enable_lazy_ghost_objects: true
        naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
        auto_mapping: true
```

**Stratégies:**
- **Naming:** Underscore number aware (MyEntity → my_entity)
- **Lazy loading:** Objets fantômes pour optimisation
- **Auto-mapping:** Scan automatique de `src/Entity`

### Mailer
**Fichier:** `config/packages/mailer.yaml`

```yaml
framework:
    mailer:
        dsn: '%env(MAILER_DSN)%'
```

**Par défaut:** `null://null` (non configuré)

### Messenger (Queue asynchrone)
**Fichier:** `config/packages/messenger.yaml`

```yaml
framework:
    messenger:
        failure_transport: failed
        transports:
            async:
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%'
                retry_strategy:
                    max_retries: 3
                    multiplier: 2
            failed: 'doctrine://default?queue_name=failed'
        routing:
            Symfony\Component\Mailer\Messenger\SendEmailMessage: async
            Symfony\Component\Notifier\Message\ChatMessage: async
            Symfony\Component\Notifier\Message\SmsMessage: async
```

**Transports:**
- **async:** Par défaut Doctrine (modifiable en Redis/RabbitMQ)
- **failed:** Queue pour les messages échoués

### Rate Limiter
Configuration pour le formulaire de contact (à vérifier dans le code):

Limite les requêtes par IP pour éviter les abus.

---

## Fonctionnalités principales

### 1. Gestion du catalogue produits

**Entités impliquées:**
- `Product`
- `Category`
- `ProductImage`

**Fonctionnalités:**
- Affichage des produits par catégorie
- Images multiples par produit
- SKU unique et slug pour URL-friendliness
- Attributs JSON flexibles
- Gestion du stock

### 2. Panier d'achat

**Entités impliquées:**
- `Cart`
- `CartItem`
- `User`

**Statuts du panier:**
- ACTIVE - Panier en cours
- ABANDONNED - Panier abandonné (pour analyses)
- CONVERTED - Converti en commande

**Fonctionnalités:**
- Association 1:1 avec User
- Timestamps (createdAt, updatedAt)
- Articles avec quantité et prix

### 3. Gestion des commandes

**Entités impliquées:**
- `Order`
- `OrderItem`
- `Payment`

**Statuts de commande:**
- PENDING
- PROCESSING
- PAID
- SHIPPED
- DELIVERED
- CANCELLED
- REFUNDED

**Snapshots d'adresses:**
- Adresses de livraison et facturation sauvegardées avec la commande
- Permet l'historique correct même si l'adresse est modifiée

### 4. Gestion des paiements

**Entités impliquées:**
- `Payment`
- `Order`

**Fournisseurs supportés:**
- Abstraction générique (provider)
- Métadonnées et réponses brutes stockées

**Statuts de paiement:**
- PENDING
- SUCCEEDED
- FAILED
- REFUNDED

### 5. Gestion des utilisateurs

**Entités impliquées:**
- `User`
- `Address`

**Fonctionnalités:**
- Inscription/Login par email
- Rôles et permissions
- Adresses multiples (livraison/facturation)
- Historique des commandes

### 6. Coupons et réductions

**Entité impliquée:**
- `Coupon`

**Types de coupons:**
- Pourcentage (%)
- Montant fixe (€)

**Gestion:**
- Limites d'utilisation
- Dates d'expiration
- Conditions d'application (JSON)
- Suivi d'usage

### 7. Gestion d'inventaire

**Entité impliquée:**
- `InventoryMovement`

**Raisons de mouvement:**
- Ventes, retours, remboursements
- Réapprovisionnement manuel
- Ajustements et inventaires
- Dommages, pertes, vols
- Stock initial et migrations

**Historique complet:**
- Traçabilité de tous les mouvements
- Timestamps et références

### 8. Newsletter

**Entité impliquée:**
- `NewsletterSubscriber`

**Fonctionnalités:**
- Inscription simple par email
- Validation d'unicité
- Timestamps

### 9. Formulaire de contact

**Entité impliquée:**
- `ContactMessage`

**Sécurité:**
- Rate limiting par IP
- Honeypot anti-bot
- CSRF protection

**Sujets de message:**
- Enum `ContactTopic`

**Traitement:**
- Flag `isProcessed` pour suivi du traitement

---

## Enums

### CartStatus
```php
enum CartStatus: string
{
    case ACTIVE = 'active';
    case ABANDONNED = 'abandoned';  // Note: typo? (abandoned)
    case CONVERTED = 'converted';
}
```

### OrderStatus
```php
enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PAID = 'paid';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
}
```

### PaymentStatus
```php
enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCEEDED = 'succeeded';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
}
```

### InventoryMovementReason
```php
enum InventoryMovementReason: string
{
    case SALE = 'sale';
    case REFUND = 'refund';
    case CANCELLED_ORDER = 'cancelled_order';
    case RETURN = 'return';
    case RESTOCK = 'restock';
    case MANUAL_ADJUSTMENT = 'manual_adjustment';
    case INVENTORY_COUNT = 'inventory_count';
    case DAMAGE = 'damage';
    case LOST = 'lost';
    case STOLEN = 'stolen';
    case INITIAL_STOCK = 'initial_stock';
    case MIGRATION = 'migration';
}
```

### ContactTopic
(À vérifier dans `src/Enum/ContactTopic.php`)

---

## Base de données

### PostgreSQL avec Docker

**Configuration (compose.yaml):**
```yaml
services:
    database:
        image: postgres:${POSTGRES_VERSION:-16}-alpine
        environment:
            POSTGRES_DB: ${POSTGRES_DB:-app}
            POSTGRES_PASSWORD: ${POSTGRES_PASSWORD:-!ChangeMe!}
            POSTGRES_USER: ${POSTGRES_USER:-app}
        healthcheck:
            test: ["CMD", "pg_isready", "-d", "${POSTGRES_DB:-app}", "-U", "${POSTGRES_USER:-app}"]
            timeout: 5s
            retries: 5
            start_period: 60s
        volumes:
            - database_data:/var/lib/postgresql/data:rw

volumes:
    database_data:
```

**Variables d'environnement (.env):**
```
DATABASE_URL=postgresql://app:!ChangeMe!@localhost:5432/app?serverVersion=16&charset=utf8
```

### Migrations

**Emplacements:** `migrations/` (4 migrations actuelles)

**Versions:**
- `Version20250423101317`
- `Version20250424092333`
- `Version20250426165108`
- `Version20250429130342`

**Commandes:**
```bash
# Créer une migration
bin/console make:migration

# Exécuter les migrations
bin/console doctrine:migrations:migrate

# Status des migrations
bin/console doctrine:migrations:status

# Rollback
bin/console doctrine:migrations:execute --down VersionXXX
```

### Configuration Doctrine

**Naming strategy:** Underscore Number Aware
- `MyEntity` → `my_entity`
- `myField1` → `my_field_1`

**Lazy loading:** Activé (objets fantômes)

**Savepoints:** Activés pour les transactions imbriquées

---

## Authentification et autorisations

### Authentification

1. **User connects** via `/login`
2. **Form login** envoie POST avec email et password
3. **Security provider** cherche l'utilisateur par email
4. **Password hashing** (bcrypt) vérifie le mot de passe
5. **Session créée** avec les données utilisateur

### Implémentation UserInterface

La classe `User` implémente:
- `UserInterface` - Interface de base Symfony
- `PasswordAuthenticatedUserInterface` - Pour les mots de passe

**Méthodes implémentées:**
- `getRoles()` - Retourne les rôles
- `getPassword()` - Retourne le hash du mot de passe
- `eraseCredentials()` - Nettoie les données sensibles
- `getUserIdentifier()` - Retourne l'email

### Rôles

**Système de rôles:**
- Stockés dans `User.roles` (array)
- Format: `ROLE_*`

**Rôles par défaut (none actuellement):**
- Implémentation commentée dans `access_control`

**Rôles suggérés:**
- `ROLE_ADMIN` - Administrateur
- `ROLE_USER` - Utilisateur enregistré
- `ROLE_CUSTOMER` - Client avec commandes

### Autorisations

Actuellement **aucune restriction** activée:

```yaml
access_control:
    # - { path: ^/admin, roles: ROLE_ADMIN }
    # - { path: ^/profile, roles: ROLE_USER }
```

**À implémenter:**
- Route `/admin/*` requiert `ROLE_ADMIN`
- Route `/profile/*` requiert `ROLE_USER`
- Vérifications dans les contrôleurs avec `#[IsGranted(...)]`

---

## Frontend

### Stimulus Controllers

Emplacements: `assets/controllers/`

**Controllers actuels:**
1. **carousel_controller.js** - Gestion du carrousel (diaporama)
2. **cart_controller.js** - Gestion du panier
3. **csrf_protection_controller.js** - Protection CSRF
4. **hello_controller.js** - Contrôleur d'exemple

### Turbo

Utilisation de **Symfony UX Turbo** pour navigation AJAX:
- Chargement rapide des pages
- Redirection sans rechargement
- Installation: `symfony/ux-turbo: ^2.35`

### Tailwind CSS

Style framework CSS avec config Symfonycasts:
- Installation: `symfonycasts/tailwind-bundle: ^0.12.0`
- Build en temps réel
- Purge automatique en production

### Asset Mapper

Gestion des ressources frontend avec `importmap.php`:
- Import ES6 modules
- No build step complexe
- Compatible avec Stimulus et Turbo

---

## Développement

### Commandes principales

```bash
# Installation
composer install
docker-compose up -d

# Migrations
bin/console make:migration -n "Description"
bin/console doctrine:migrations:migrate

# Créer des entités
bin/console make:entity

# Créer des contrôleurs
bin/console make:controller NomController

# Tests
bin/console test
php bin/phpunit

# Serveur local
symfony serve

# Fixtures/Factories (avec Foundry)
bin/console doctrine:fixtures:load
```

### Structure des tests

```
tests/
├── bootstrap.php
```

Utilise **PHPUnit** avec **Zenstruck Foundry** pour les factories.

### Fixtures

Avec **DoctrineFixturesBundle** (dev):
- Fichier: `src/DataFixtures/AppFixtures.php`
- Chargement: `doctrine:fixtures:load`

---

## Déploiement

### Variables d'environnement essentielles

```env
# Application
APP_ENV=prod
APP_SECRET=your-secret-key

# Base de données
DATABASE_URL=postgresql://user:pass@host:5432/dbname

# Mailer
MAILER_DSN=smtp://user:pass@smtp.host:port

# Messenger
MESSENGER_TRANSPORT_DSN=amqp://user:pass@localhost:5672/%2f/messages

# Routing
DEFAULT_URI=https://your-domain.com
```

### Production

**Optimisations:**
- Cache auto-generation désactivée
- Proxy directory configuré
- Pool de cache Doctrine
- Assets installés et compilés

```bash
composer install --no-dev --optimize-autoloader
APP_ENV=prod bin/console cache:clear
APP_ENV=prod bin/console assets:install public
```

---

## Conventions et bonnes pratiques

### Nommage

- **Entités:** PascalCase (`User`, `Product`, `Order`)
- **Propriétés:** camelCase (`firstName`, `stockQuantity`)
- **Routes:** kebab-case (`/informations/contact`)
- **Templates:** snake_case (`login.html.twig`)
- **Contrôleurs:** PascalCase + Controller suffix (`HomeController`)

### Doctrine Attributes

- Mapping par attributs PHP (pas YAML)
- Constraints de validation intégrées
- Index et unique constraints déclarés

### Entités

- Use `DateTimeImmutable` pour les dates (immutabilité)
- Snapshots pour les données historiques (adresses, articles)
- Enums pour les statuts et états

### Sécurité

- CSRF activée par défaut
- Rate limiting pour les formulaires sensibles
- Honeypot anti-bot
- Email comme identifiant unique
- Mots de passe hashés (bcrypt)

---

## Prochaines étapes suggérées

1. **Implémentation des rôles:**
   - Ajouter `ROLE_ADMIN` et `ROLE_CUSTOMER`
   - Protéger les routes d'admin
   - Dashboard utilisateur

2. **API REST:**
   - Ajouter API Platform
   - Endpoints pour le panier et commandes
   - Authentification JWT/OAuth

3. **Paiement:**
   - Intégration Stripe/PayPal
   - Gestion complète du workflow
   - Webhooks pour confirmation

4. **Email:**
   - Confirmation d'inscripion
   - Confirmation de commande
   - Newsletter

5. **Admin panel:**
   - EasyAdmin pour gestion CRUD
   - Dashboard avec statistiques
   - Gestion du stock et commandes

6. **Recherche:**
   - Elasticsearch ou recherche simple
   - Filtres par catégorie, prix, etc.

7. **Performance:**
   - Redis pour le cache session
   - Lazy loading optimisé
   - Pagination

---

## Fichiers clés à connaître

| Fichier | Purpose |
|---------|---------|
| `config/bundles.php` | Bundles activés |
| `config/packages/security.yaml` | Configuration sécurité |
| `config/routes.yaml` | Routage principal |
| `src/Kernel.php` | Kernel Symfony |
| `src/Entity/` | Modèles de données |
| `src/Controller/` | Contrôleurs |
| `templates/` | Templates Twig |
| `assets/` | Frontend (Stimulus, CSS) |
| `migrations/` | Migrations Doctrine |
| `composer.json` | Dépendances PHP |
| `.env` | Variables d'environnement |
| `compose.yaml` | Configuration Docker |

---

**Document généré:** Avril 2026  
**Dernière mise à jour:** Configuration observée à partir du code source
