---
lang: fr
permalink: doc/contacts
title: Contacts
description: Comment les clients deviennent des contacts Brevo, quels attributs sont exposés, et comment les listes sont gérées.
updated: 2026-09-24
---

### Identification

Un contact est identifié par son **adresse e-mail**. Écrire un client dont l'e-mail existe déjà
dans Brevo met à jour ce contact, cela ne crée jamais de doublon.

Changer l'adresse e-mail d'un client **déplace** donc le contact : Brevo y voit une nouvelle
identité.

### Champs exposés

| Champ | Sens | Remarques |
|---|---|---|
| **E-mail** | lecture & écriture | L'identifiant du contact |
| **Attributs** | lecture & écriture | Chaque attribut défini dans votre compte Brevo |
| **Listes** | lecture & écriture | Les listes auxquelles appartient le contact |

### Attributs

Les attributs Brevo ne sont pas une liste figée : ce sont ceux que **vous avez définis** dans
votre compte, plus ceux que Brevo crée par défaut, comme `FIRSTNAME`, `LASTNAME` ou `SMS`.

Le connecteur les lit dans votre compte et expose chacun comme un champ. Ils apparaissent dans
vos mappings sous leur nom Brevo, et leur type suit celui déclaré dans Brevo : texte, nombre,
date ou booléen.

> [!TIP]
> Ajoutez un attribut dans Brevo, puis rechargez votre connexion : le nouveau champ est
> immédiatement disponible pour le mapping.

### Listes

Le champ **Listes** porte les listes auxquelles appartient le contact. Il se lit pour savoir où
est un contact, et s'écrit pour le déplacer.

La liste choisie dans les paramètres de la connexion est celle utilisée à la **création** d'un
contact. Le champ `Listes` permet d'aller plus loin, pour les contacts qui doivent appartenir à
plusieurs listes.

### Ce qui n'est jamais fait

- Splash ne **supprime jamais** un contact Brevo quand un client est supprimé dans votre
  boutique.
- Les **données transactionnelles** de Brevo — campagnes, statistiques, historique d'e-mails —
  ne sont pas synchronisées.
- Les contacts ne sont pas importés en masse : ils arrivent dans Brevo au fur et à mesure que
  vos autres applications les écrivent.
