<?php
defined('TYPO3_MODE') or die();

return [
    'ctrl' => [
        'title' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:table',
        'label' => 'forward_url',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'type' => 'type',
        'typeicon_column' => 'type',
        'typeicon_classes' => [
            'default' => 'mimetypes-x-urlforwarding-redirect',
            '0' => 'mimetypes-x-urlforwarding-internal',
            '1' => 'mimetypes-x-urlforwarding-external',
            '2' => 'mimetypes-x-urlforwarding-file'
        ],
        'useColumnsForDefaultValues' => 'type',
        'dividers2tabs' => true,
        'default_sortby' => 'ORDER BY forward_url',
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime'
        ],
        'searchFields' => 'forward_url, internal_page, http_status'
    ],
    'interface' => [
        'showRecordFieldList' => '
            sys_language_uid,
            hidden,
            type,
            forward_url,
            internal_page,
            external_url,
            internal_file,
            http_status,
            domain,
            requested_by,
            request_date,
            request_note,
            counter,
            last_hit
        '
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --palette--;;general,
                forward_url,
                internal_page,
                http_status,
                domain,
            --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,
                hidden,
                --palette--;;access,
            --div--;LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:tab.request,
                requested_by,
                request_date,
                request_note,
            --div--;LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:tab.statistics,
                counter,
                last_hit
        '
        ],
        '1' => [
            'showitem' => '
                type,
                forward_url,
                external_url,
                http_status,
                domain,
            --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,
                hidden,
                --palette--;;access,
            --div--;LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:tab.request,
                requested_by,
                request_date,
                request_note,
            --div--;LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:tab.statistics,
                counter,
                last_hit
        '
        ],
        '2' => [
            'showitem' => '
                type,
                forward_url,
                internal_file,
                http_status,
                domain,
            --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,
                hidden,
                --palette--;;access,
            --div--;LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:tab.request,
                requested_by,
                request_date,
                request_note,
            --div--;LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:tab.statistics,
                counter,
                last_hit
        '
        ],
    ],
    'palettes' => [
        'general' => [
            'showitem' => 'type, sys_language_uid',
            'canNotCollapse' => true
        ],
        'access' => [
            'showitem' => 'starttime, endtime',
            'canNotCollapse' => true
        ]
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0]
                ]
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0]
                ],
                'foreign_table' => 'tx_urlforwarding_domain_model_redirect',
                'foreign_table_where' => 'AND tx_urlforwarding_domain_model_redirect.pid=###CURRENT_PID### AND tx_urlforwarding_domain_model_redirect.sys_language_uid IN (-1,0)'
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check'
            ]
        ],
        'starttime' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ]
            ]
        ],
        'endtime' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ]
            ]
        ],
        'type' => [
            'exclude' => false,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:type.0', 0],
                    ['LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:type.1', 1],
                    ['LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:type.2', 2]
                ],
                'size' => 1,
                'maxitems' => 1
            ]
        ],
        'http_status' => [
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:http_status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:http_status.301', 301],
                    ['LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:http_status.302', 302],
                    ['LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:http_status.303', 303],
                    ['LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:http_status.307', 307]
                ],
                'size' => 1,
                'maxitems' => 1,
                'default' => 302
            ]
        ],
        'forward_url' => [
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:forward_url',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim, required, nospace'
            ]
        ],
        'internal_page' => [
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:internal_page',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1,
                'eval' => '',
                'show_thumbs' => '1',
                'wizards' => [
                    'suggest' => [
                        'type' => 'suggest',
                        'default' => [
                            'searchWholePhrase' => 1,
                            'maxPathTitleLength' => 40,
                            'maxItemsInResultList' => 5
                        ],
                        'pages' => [
                            'searchCondition' => 'doktype NOT IN (3,4,6,7,199,254,255)'
                        ]
                    ]
                ]
            ]
        ],
        'external_url' => [
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:external_url',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim, required',
                'softref' => 'substitute'
            ]
        ],
        'internal_file' => [
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:internal_file',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file_reference',
                'allowed' => 'jpg, jpeg, gif, png, pdf, doc, docx, xls, xlsx, ppt, pptx',
                'show_thumbs' => 1,
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1
            ]
        ],
        'domain' => [
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:domain',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_domain',
                'MM' => 'tx_urlforwarding_domain_mm',
                'size' => 5,
                'autoSizeMax' => 10,
                'maxitems' => 10
            ]
        ],
        'requested_by' => [
            'exclude' => true,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:requested_by',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ]
        ],
        'request_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:request_date',
            'config' => [
                'size' => '10',
                'max' => '20',
                'type' => 'input',
                'eval' => 'date'
            ]
        ],
        'request_note' => [
            'exclude' => true,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:request_note',
            'config' => [
                'type' => 'text'
            ]
        ],
        'counter' => [
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:counter',
            'config' => [
                'type' => 'none',
                'size' => 5
            ]
        ],
        'last_hit' => [
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:last_hit',
            'config' => [
                'type' => 'input',
                'size' => 16,
                'readOnly' => true,
                'eval' => 'date'
            ]
        ]
    ]
];