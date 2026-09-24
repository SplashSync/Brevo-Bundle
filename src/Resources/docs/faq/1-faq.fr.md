---
lang: fr
permalink: faq/questions
title: Questions fréquentes
description: Réponses aux questions courantes sur le connecteur Brevo.
updated: 2026-09-24
---

## Questions fréquentes {.faq}

### Le sélecteur de liste est vide, ou n'apparaît pas

Splash lit vos listes dans Brevo avec votre clé d'API. Si le sélecteur manque, la clé est
absente ou refusée : enregistrez d'abord une **API Key V3** valide, puis éditez à nouveau la
connexion.

Si la clé est valide mais que votre compte ne contient aucune liste, créez-en une dans Brevo.

### Les modifications faites dans Brevo ne remontent pas dans Splash

Vérifiez le bloc **Mise à jour des WebHooks** de votre connexion : il doit signaler une
configuration valide. Sinon, lancez la mise à jour.

Sans webhooks valides, Brevo ne peut pas notifier Splash de ses changements.

### Un client n'a pas été écrit dans Brevo

Vérifiez que le client porte une **adresse e-mail** : c'est l'identifiant d'un contact Brevo, et
un client qui n'en a pas ne peut pas être écrit.

Vos journaux Splash nomment les clients refusés, et pourquoi.

### Un attribut de mon compte manque dans les mappings

Le connecteur lit les attributs de votre compte Brevo au chargement de la connexion. Rechargez
votre connexion après avoir ajouté un attribut dans Brevo.

### J'ai changé de liste, et mes contacts n'ont pas bougé

Changer de liste indique seulement à Splash où écrire **à partir de maintenant**. Les contacts
déjà créés restent dans leur liste précédente : déplacez-les depuis Brevo, ou écrivez leur champ
`Listes`.

### Le connecteur supprime-t-il des contacts ?

Non. Supprimer un client dans votre boutique ne supprime jamais le contact Brevo. Les
suppressions se font depuis Brevo, et sont ensuite signalées à Splash.
