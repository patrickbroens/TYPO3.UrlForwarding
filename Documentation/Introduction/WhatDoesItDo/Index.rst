.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt



.. _what-does-it-do:

What does it do?
----------------

URL redirection, also called URL forwarding, is a World Wide Web technique for making a web page available under more
than one URL address. When a web browser attempts to open a URL that has been redirected, a page with a different URL is
opened.

URL redirection is done for various reasons:

#. for URL shortening;

#. to prevent broken links when web pages are moved;

#. to guide navigation into and out of a website;

#. for privacy protection;

#. and for less innocuous purposes such as phishing attacks

This extension lets you add these redirects which you can configure to your own needs.


But what about the extension RealURL?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

You would say, the extension RealUrl is already providing this functionality, but there are two differences:

#. It does not offer a backend module, but lets you add or edit the redirect records directly in the TYPO3 "List"
   module.

#. It determines if the given path is a redirect or an actual TYPO3 page directly at the first possible point when
   bootstrapping TYPO3. This means most of the core of TYPO3 will not be loaded when this determination takes place.


Features
^^^^^^^^

This extensions comes with some convenient features:

#. Redirect method: Not only can you redirect to "Internal pages", but also to an "Internal file" or an "External URL".

#. HTTP status: Redirect using the status "Moved permanently (301)", "Moved temporarily (302)", "See other location
   (303)" and "Temporary redirect (307)".

#. Limit a redirect to a domain. This is helpful when you have a multisite/multidomain setup.

#. Statistics: Counter with the amount of hits and when the last hit was on the redirect.

#. Request information: Fields to keep track who did the request for the redirect or what the reason for the redirect
   was. Especially helpful in big organizations.