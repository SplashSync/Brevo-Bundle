---
lang: fr
permalink: start/install
title: Installer le connecteur Brevo
description: Clé d'API, création de la connexion dans Splash, choix de la liste, vérifications et activation des webhooks.
updated: 2026-09-24
---

### Prérequis

- Un compte **Brevo**, avec au moins une liste de contacts.
- Une **clé d'API Brevo V3**, créée depuis votre compte Brevo dans *SMTP & API*.
- Un compte **Splash Sync Premium** actif.

> [!CAUTION]
> Une clé d'API donne un **accès complet** à votre compte Brevo. Ne la partagez pas, et ne
> l'envoyez jamais par e-mail.

### Étape 1 — Créer la connexion Brevo

Depuis votre compte Splash, ajoutez une nouvelle connexion **Brevo API V3**.

Le connecteur dialogue directement avec l'API Brevo : il n'y a rien à installer côté Brevo.

### Étape 2 — Saisir votre clé d'API

Renseignez le champ de la connexion :

| Champ | Contenu |
|---|---|
| **API Key V3** | La clé d'API de votre compte Brevo |

Enregistrez. Le connecteur appelle Brevo, lit votre compte et **charge vos listes de contacts**.

> [!NOTE]
> Le sélecteur de liste n'apparaît qu'une fois une clé valide enregistrée : Splash doit d'abord
> lire vos listes dans Brevo pour pouvoir vous les proposer.

### Étape 3 — Choisir la liste à synchroniser

Éditez à nouveau la connexion. Un sélecteur **Liste** affiche désormais vos listes Brevo :
choisissez celle où vos clients doivent être écrits.

Le **Pays par défaut** est facultatif. Il sert à formater les numéros de téléphone qui ne
portent pas d'indicatif international.

### Étape 4 — Vérifier la connexion

Lancez l'auto-test **Configuration du connecteur Brevo**. Il vérifie que Splash joint l'API
Brevo avec votre clé, et qu'une liste est bien sélectionnée.

Si le test échoue, vérifiez d'abord la clé d'API : c'est la cause la plus fréquente.

### Étape 5 — Activer les webhooks

Les webhooks permettent à Brevo de **notifier Splash en temps réel** dès qu'un contact change.
Sans eux, les modifications faites dans Brevo ne remontent pas automatiquement.

Dans le bloc **Mise à jour des WebHooks** de votre connexion, lancez la mise à jour. Le
connecteur crée ou met à jour les webhooks nécessaires dans votre compte Brevo.

Le bloc doit alors afficher **La configuration des WebHooks est valide**.

> [!TIP]
> Relancez la mise à jour si le bloc signale qu'une configuration est nécessaire, par exemple
> après un changement de clé d'API.

### Et ensuite ?

Votre connexion est prête. Consultez les **Options du connecteur** pour ajuster la liste et le
pays par défaut, puis activez la synchronisation de vos clients.
