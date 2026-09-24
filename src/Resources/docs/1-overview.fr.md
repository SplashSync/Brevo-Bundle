---
lang: fr
permalink: overview
title: Connecteur Brevo
description: Synchronisez vos clients avec vos listes de contacts Brevo, avec leurs attributs et leurs abonnements.
updated: 2026-09-24
---

### Présentation

Brevo (anciennement SendInBlue) est une plateforme d'e-mail marketing et de relation client.
Le connecteur Brevo relie votre compte Brevo à Splash via l'API Brevo V3 : les clients de vos
boutiques et de votre ERP deviennent des **contacts d'une liste Brevo**, et y restent à jour.

Rien n'est à installer côté Brevo : la connexion est entièrement gérée par Splash, à partir de
votre clé d'API.

### Objets synchronisés

| Objet | Rôle |
|---|---|
| **Client** | Un contact de votre liste Brevo : e-mail, attributs et listes d'appartenance |

### Fonctionnement

- Un client écrit dans Splash est **créé ou mis à jour** dans votre liste Brevo, identifié par
  son adresse e-mail.
- Les **attributs** de votre compte Brevo (prénom, nom, société, et chacun des attributs que
  vous avez définis) sont exposés comme des champs : ils se mappent avec vos autres
  applications comme n'importe quel autre champ.
- Les **listes** auxquelles appartient un contact sont lisibles et modifiables : un contact peut
  être déplacé d'une liste à l'autre depuis Splash.
- Brevo **notifie Splash** dès qu'un contact change de son côté, via des webhooks que le
  connecteur configure pour vous.

### Bon à savoir

- Le connecteur travaille sur **une liste à la fois** : celle choisie dans les paramètres de la
  connexion est celle où les contacts sont écrits.
- Les contacts **supprimés dans Brevo** sont signalés à Splash, mais Splash ne supprime jamais
  de lui-même un client de votre boutique.
- Les numéros de téléphone sont formatés avec le **pays par défaut** de la connexion, pour ceux
  qui ne portent pas d'indicatif international.

### Pour démarrer

1. **Installez le connecteur** et vérifiez la connexion (section *Démarrer*).
2. Choisissez votre **liste** et ajustez les options (section *Configuration*).
3. Lisez comment les **contacts** sont synchronisés (section *Utilisation*).
