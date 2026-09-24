---
lang: en
permalink: configure/options
title: Connector options
description: Contact list, default country for phone numbers, and webhooks configuration.
updated: 2026-09-24
---

### API connection

| Option | Role |
|---|---|
| **API Key V3** | The key Splash uses to reach your Brevo account |

Changing the key reloads your lists on the next save. Check the selected list afterwards: it may
no longer exist in the new account.

### List

| Option | Role |
|---|---|
| **List** | The Brevo list contacts are written to |

The selector offers the lists read from your Brevo account. It only appears once a valid API key
has been saved.

> [!IMPORTANT]
> Changing the list does **not** move the contacts already synchronized. The previous ones stay
> in their list, and only what Splash writes afterwards goes to the new one.

### Default country

| Option | Role |
|---|---|
| **Default Country** | Country code (ISO 3166-1) used to format phone numbers |

Brevo stores phone numbers in international format. A number written without a prefix is
completed with this country.

Leave it empty if your contacts always carry an international prefix.

### Webhooks

The **Update of WebHooks** block reports whether Brevo can notify Splash:

- **WebHook Configuration is Valid** — nothing to do.
- **Update of WebHooks Configuration Required** — run the update to create or repair them.

Webhooks are attached to the account, not to the list: they follow the API key.

### Self-test

The **Brevo Connector Configuration** self-test checks, in order, that the API key is filled in,
that Brevo answers, and that a list is selected. Run it after every change to the connection.
