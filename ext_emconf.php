<?php
$EM_CONF['url_forwarding'] = array(
    'title' => 'URL Forwarding',
    'description' => 'Redirects (301, 302, 303, 307) to internal/external page or file, based on domain and path',
    'category' => 'fe',
    'state' => 'stable',
    'author' => 'Patrick Broens',
    'author_email' => 'patrick@patrickbroens.nl',
    'version' => '1.1.0',
    'constraints' => array(
        'depends' => array(
            'typo3' => '7.6.0-7.6.99'
        ),
        'conflicts' => array(),
        'suggests' => array()
    ),
    'autoload' => array(
        'psr-4' => array('PatrickBroens\\UrlForwarding\\' => 'Classes')
    )
);