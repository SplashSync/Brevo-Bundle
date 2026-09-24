---
lang: en
permalink: start/install
title: Install the Brevo connector
description: API key, connection setup in Splash, list selection, checks and webhooks activation.
updated: 2026-09-24
---

### Requirements

- A **Brevo** account, with at least one contact list.
- A **Brevo API key V3**, created from your Brevo account under *SMTP & API*.
- An active **Splash Sync Premium** account.

> [!CAUTION]
> An API key grants **full access** to your Brevo account. Do not share it, and never send it
> by e-mail.

### Step 1 — Create the Brevo connection

From your Splash account, add a new **Brevo API V3** connection.

The connector talks directly to the Brevo API: there is nothing to install on the Brevo side.

### Step 2 — Enter your API key

Fill in the connection field:

| Field | Content |
|---|---|
| **API Key V3** | The API key of your Brevo account |

Save. The connector calls Brevo, reads your account and **loads your contact lists**.

> [!NOTE]
> The list selector only appears once a valid key has been saved: Splash has to read your lists
> from Brevo before it can offer them.

### Step 3 — Choose the list to synchronize

Edit the connection again. A **List** selector now shows your Brevo lists: pick the one your
customers must be written to.

The **Default Country** is optional. It is used to format phone numbers that carry no
international prefix.

### Step 4 — Check the connection

Run the **Brevo Connector Configuration** self-test. It checks that Splash can reach the Brevo
API with your key, and that a list is selected.

If the test fails, check the API key first: it is the most common cause of failure.

### Step 5 — Enable webhooks

Webhooks let Brevo **notify Splash in real time** whenever a contact changes. Without them,
changes made in Brevo are not automatically reported.

In the **Update of WebHooks** block of your connection, run the update. The connector creates or
updates the required webhooks in your Brevo account.

The block should then display **WebHook Configuration is Valid**.

> [!TIP]
> Run the update again if the block reports that a configuration is required, for example after
> changing your API key.

### What's next?

Your connection is ready. Read the **Connector options** to adjust the list and the default
country, then enable synchronization for your customers.
