---
lang: en
permalink: doc/contacts
title: Contacts
description: How customers become Brevo contacts, which attributes are exposed, and how lists are handled.
updated: 2026-09-24
---

### Identification

A contact is identified by its **e-mail address**. Writing a customer whose e-mail already
exists in Brevo updates that contact, it never creates a duplicate.

Changing the e-mail address of a customer therefore **moves** the contact: Brevo sees a new
identity.

### Exposed fields

| Field | Direction | Notes |
|---|---|---|
| **E-mail** | read & write | The identifier of the contact |
| **Attributes** | read & write | Every attribute defined in your Brevo account |
| **Lists** | read & write | The lists the contact belongs to |

### Attributes

Brevo attributes are not a fixed list: they are the ones **you defined** in your account, plus
the ones Brevo creates by default, such as `FIRSTNAME`, `LASTNAME` or `SMS`.

The connector reads them from your account and exposes each one as a field. They appear in your
mappings under their Brevo name, and their type follows the one declared in Brevo: text, number,
date or boolean.

> [!TIP]
> Add an attribute in Brevo, then reload your connection: the new field is available for
> mapping right away.

### Lists

The **Lists** field carries the lists the contact belongs to. It can be read to know where a
contact is, and written to move it.

The list chosen in the connection settings is the one used when a contact is **created**. The
`Lists` field lets you go further, for contacts that must belong to several lists.

### What is never done

- Splash **never deletes** a Brevo contact when a customer is deleted in your shop.
- Brevo **transactional data** — campaigns, statistics, e-mail history — is not synchronized.
- Contacts are not imported in bulk: they reach Brevo as your other applications write them.
