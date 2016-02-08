<?php
$EM_CONF['url_forwarding'] = [
    'title' => 'URL Forwarding',
    'description' => 'Redirects (301, 302, 303, 307) to internal/external page or file, based on domain and path',
    'category' => 'fe',
    'state' => 'stable',
    'author' => 'Patrick Broens',
    'author_email' => 'patrick@patrickbroens.nl',
    'version' => '1.1.1',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-7.6.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ],
    'autoload' => [
        'psr-4' => ['PatrickBroens\\UrlForwarding\\' => 'Classes']
    ]
];