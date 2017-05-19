<?php
$EM_CONF['url_forwarding'] = [
    'title' => 'URL Forwarding',
    'description' => 'Redirects (301, 302, 303, 307) to internal/external page, file or partial path replacement, based on domain and path',
    'category' => 'fe',
    'state' => 'stable',
    'author' => 'Patrick Broens',
    'author_email' => 'patrick@patrickbroens.nl',
    'version' => '1.3.1',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ],
    'autoload' => [
        'psr-4' => ['PatrickBroens\\UrlForwarding\\' => 'Classes']
    ]
];