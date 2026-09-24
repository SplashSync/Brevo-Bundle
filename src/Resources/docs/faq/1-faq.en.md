---
lang: en
permalink: faq/questions
title: Frequently asked questions
description: Answers to common questions about the Brevo connector.
updated: 2026-09-24
---

## Frequently asked questions {.faq}

### The list selector is empty, or does not appear

Splash reads your lists from Brevo with your API key. If the selector is missing, the key is
either absent or refused: save a valid **API Key V3** first, then edit the connection again.

If the key is valid but your account holds no list, create one in Brevo.

### Changes made in Brevo are not reported to Splash

Check the **Update of WebHooks** block of your connection: it must report a valid configuration.
Otherwise, run the update.

Without valid webhooks, Brevo cannot notify Splash of its changes.

### A customer was not written to Brevo

Check that the customer carries an **e-mail address**: it is the identifier of a Brevo contact,
and a customer without one cannot be written.

Your Splash logs name the customers that were refused, and why.

### An attribute of my account is missing from the mappings

The connector reads the attributes of your Brevo account when the connection is loaded. Reload
your connection after adding an attribute in Brevo.

### I changed the list, and my contacts did not move

Changing the list only tells Splash where to write **from now on**. Contacts already created stay
in their previous list: move them from Brevo, or write their `Lists` field.

### Does the connector delete contacts?

No. Deleting a customer in your shop never deletes the Brevo contact. Deletions are done from
Brevo, and are then reported to Splash.
