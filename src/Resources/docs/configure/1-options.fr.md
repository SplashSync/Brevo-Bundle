---
lang: fr
permalink: configure/options
title: Options du connecteur
description: Liste de contacts, pays par défaut pour les téléphones, et configuration des webhooks.
updated: 2026-09-24
---

### Connexion à l'API

| Option | Rôle |
|---|---|
| **API Key V3** | La clé avec laquelle Splash joint votre compte Brevo |

Changer la clé recharge vos listes au prochain enregistrement. Vérifiez ensuite la liste
sélectionnée : elle peut ne plus exister dans le nouveau compte.

### Liste

| Option | Rôle |
|---|---|
| **Liste** | La liste Brevo où sont écrits les contacts |

Le sélecteur propose les listes lues dans votre compte Brevo. Il n'apparaît qu'une fois une clé
d'API valide enregistrée.

> [!IMPORTANT]
> Changer de liste ne **déplace pas** les contacts déjà synchronisés. Les précédents restent
> dans leur liste, et seul ce que Splash écrit ensuite part dans la nouvelle.

### Pays par défaut

| Option | Rôle |
|---|---|
| **Pays par défaut** | Code pays (ISO 3166-1) utilisé pour formater les numéros de téléphone |

Brevo stocke les numéros au format international. Un numéro saisi sans indicatif est complété
avec ce pays.

Laissez-le vide si vos contacts portent toujours un indicatif international.

### Webhooks

Le bloc **Mise à jour des WebHooks** indique si Brevo peut notifier Splash :

- **La configuration des WebHooks est valide** — rien à faire.
- **Une mise à jour de la configuration est nécessaire** — lancez la mise à jour pour les créer
  ou les réparer.

Les webhooks sont attachés au compte, pas à la liste : ils suivent la clé d'API.

### Auto-test

L'auto-test **Configuration du connecteur Brevo** vérifie, dans l'ordre, que la clé d'API est
renseignée, que Brevo répond, et qu'une liste est sélectionnée. Lancez-le après chaque
modification de la connexion.
