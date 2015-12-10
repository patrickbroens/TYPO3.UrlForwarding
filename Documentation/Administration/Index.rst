.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt



.. _administration:

Administration
--------------

Administration of the redirects is very easy.


Make a new record
^^^^^^^^^^^^^^^^^

#. Go to the "List" module in the TYPO3 module menu on the left side.

#. Make a new page of the type "Folder" in the page tree and click on it.

#. At the top of the "List" module, click on the button to create a new record.

#. In the selector, click "Redirect", which you can find below the headline "URL Forwarding".


Edit the record
^^^^^^^^^^^^^^^

First you select what kind of redirect you want to have. This can be done in the "General" tab in the first field with
the label "Redirect to". Depending on the type of redirect you have just selected, the fields will change.


Field: Source
=============

In this field you fill in the path on which the redirect takes place. The domain or the transfer protocol (http/https)
should not be included. If you want the redirect to happen on http://domain.com/this/is/the/redirect/, you only enter
this/is/the/redirect/. Slashes in front or at the end of the path are not mandatory.


Field: HTTP Status
==================

Here you select what kind of header will be sent to the browser. This can be:

* Moved permanently (301)

* Moved temporarily (302)

* See other location (303)

* Temporary redirect (307)

Be aware that a permanent redirect will stay in the browser cache of each visitor until this cache is cleared. Use this
header wisely.

Field: Limit to domains
=======================

You can limit a redirect on one or more domains. If no domains are selected, the redirect will take place on all
domains.

When saving the record, the extension will check if the path (source) has been used already for another domain. If so,
the record will not be saved, but you get a warning.


Field: Language (Internal Page only)
====================================

If you have pages in multiple languages you want to select to which language variant of a page the redirect should
point. You can select to which language variant of an internal page the redirect has to go by using this field.


Field: Internal Page (Internal Page only)
=========================================

The page the redirect should point to.


Field: External URL (External URL only)
=======================================

The external URL the redirect should point to.


Field: Internal file (Internal file only)
=========================================

The file in the TYPO3 system the redirect should point to.


Tab: Request
============

Some fields for your administration. Especially convenient when you are working in a big organization. You can enter the
name of the person who did the request for the redirect, on which date and some notes, like the reason for the redirect.


Tab: Statistics
===============

Non-editable fields showing how many times the redirect has been requested and the date of the last hit.
