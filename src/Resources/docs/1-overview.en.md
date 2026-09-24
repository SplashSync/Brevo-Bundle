---
lang: en
permalink: overview
title: Brevo Connector
description: Synchronize your customers with your Brevo contact lists, with their attributes and their subscriptions.
updated: 2026-09-24
---

### Overview

Brevo (formerly SendInBlue) is an e-mail marketing and customer relationship platform.
The Brevo connector links your Brevo account to Splash through the Brevo API V3: the customers
of your shops and your ERP become **contacts of a Brevo list**, and stay up to date there.

Nothing needs to be installed on the Brevo side: the connection is fully managed by Splash,
using your API key.

### Synchronized objects

| Object | Role |
|---|---|
| **Customer** | A contact of your Brevo list: e-mail, attributes and the lists it belongs to |

### How it works

- A customer written in Splash is **created or updated** in your Brevo list, identified by
  its e-mail address.
- The **attributes** of your Brevo account (first name, last name, company, and every attribute
  you defined yourself) are exposed as fields: they map to your other applications like any
  other field.
- The **lists** a contact belongs to are readable and writable: a contact can be moved from one
  list to another from Splash.
- Brevo **notifies Splash** whenever a contact changes on its side, through webhooks the
  connector configures for you.

### Good to know

- The connector works on **one list at a time**: the list chosen in the connection settings is
  the one contacts are written to.
- Contacts **deleted in Brevo** are reported to Splash, but Splash never deletes a customer of
  your shop on its own.
- Phone numbers are formatted with the **default country** of the connection, for the numbers
  that carry no international prefix.

### Getting started

1. **Install the connector** and check the connection (*Getting started* section).
2. Choose your **list** and adjust the options (*Configuration* section).
3. Read how **contacts** are synchronized (*Usage* section).
