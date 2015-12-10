<?php
defined('TYPO3_MODE') or die();

return array(
    'ctrl' => array(
        'title'	=> 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:table',
        'label' => 'forward_url',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'type' => 'type',
        'typeicon_column' => 'type',
        'typeicons' => array (
            '0' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('url_forwarding') . 'Resources/Public/Icons/TCA/Redirect/Type_Internal.png',
            '1' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('url_forwarding') . 'Resources/Public/Icons/TCA/Redirect/Type_External.png',
            '2' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('url_forwarding') . 'Resources/Public/Icons/TCA/Redirect/Type_File.png'
        ),
        'useColumnsForDefaultValues' => 'type',
        'dividers2tabs' => TRUE,
        'default_sortby' => 'ORDER BY forward_url',
        'versioningWS' => 2,
        'versioning_followPages' => TRUE,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime'
        ),
        'searchFields' => 'forward_url, internal_page, http_status',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('url_forwarding') . 'Resources/Public/Icons/TCA/Redirect/Redirect.png'
    ),
    'interface' => array(
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
    ),
    'types' => array(
        '0' => array(
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
        ),
        '1' => array(
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
        ),
        '2' => array(
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
        ),
    ),
    'palettes' => array(
        'general' => array(
            'showitem' => 'type, sys_language_uid',
            'canNotCollapse' => true
        ),
        'access' => array(
            'showitem' => 'starttime, endtime',
            'canNotCollapse' => true
        )
    ),
    'columns' => array(
        'sys_language_uid' => array(
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
                )
            )
        ),
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('', 0)
                ),
                'foreign_table' => 'tx_urlforwarding_domain_model_redirect',
                'foreign_table_where' => 'AND tx_urlforwarding_domain_model_redirect.pid=###CURRENT_PID### AND tx_urlforwarding_domain_model_redirect.sys_language_uid IN (-1,0)'
            )
        ),
        'l10n_diffsource' => array(
            'config' => array(
                'type' => 'passthrough'
            )
        ),
        't3ver_label' => array(
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'max' => 255
            )
        ),
        'hidden' => array(
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check'
            )
        ),
        'starttime' => array(
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                )
            )
        ),
        'endtime' => array(
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                )
            )
        ),
        'type' => array(
            'exclude' => false,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:type',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:type.0', 0),
                    array('LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:type.1', 1),
                    array('LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:type.2', 2)
                ),
                'size' => 1,
                'maxitems' => 1
            )
        ),
        'http_status' => array(
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:http_status',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array('LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:http_status.301', 301),
                    array('LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:http_status.302', 302),
                    array('LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:http_status.303', 303),
                    array('LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:http_status.307', 307)
                ),
                'size' => 1,
                'maxitems' => 1,
                'default' => 302
            )
        ),
        'forward_url' => array(
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:forward_url',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim, required, nospace'
            )
        ),
        'internal_page' => array(
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:internal_page',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1,
                'eval' => '',
                'show_thumbs' => '1',
                'wizards' => array(
                    'suggest' => array(
                        'type' => 'suggest',
                        'default' => array(
                            'searchWholePhrase' => 1,
                            'maxPathTitleLength' => 40,
                            'maxItemsInResultList' => 5
                        ),
                        'pages' => array(
                            'searchCondition' => 'doktype NOT IN (3,4,6,7,199,254,255)'
                        )
                    )
                )
            )
        ),
        'external_url' => array(
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:external_url',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim, required',
                'softref' => 'substitute'
            )
        ),
        'internal_file' => array(
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:internal_file',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'file_reference',
                'allowed' => 'jpg, jpeg, gif, png, pdf, doc, docx, xls, xlsx, ppt, pptx',
                'show_thumbs' => 1,
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1
            )
        ),
        'domain' => array(
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:domain',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_domain',
                'MM' => 'tx_urlforwarding_domain_mm',
                'size' => 5,
                'autoSizeMax' => 10,
                'maxitems' => 10
            )
        ),
        'requested_by' => array(
            'exclude' => true,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:requested_by',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            )
        ),
        'request_date' => array(
            'exclude' => true,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:request_date',
            'config' => array(
                'size' => '10',
                'max' => '20',
                'type' => 'input',
                'eval' => 'date'
            )
        ),
        'request_note' => array(
            'exclude' => true,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:request_note',
            'config' => array(
                'type' => 'text'
            )
        ),
        'counter' => array(
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:counter',
            'config' => array(
                'type' => 'none',
                'size' => 5
            )
        ),
        'last_hit' => array(
            'exclude' => false,
            'label' => 'LLL:EXT:url_forwarding/Resources/Private/Language/TCA/Redirect.xlf:last_hit',
            'config' => array(
                'type' => 'input',
                'size' => 16,
                'readOnly' => true,
                'eval' => 'date'
            )
        )
    )
);