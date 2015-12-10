<?php
defined('TYPO3_MODE') or die();

// Check url forwarding before page rendering. This is the very first hook in TYPO3
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest']['url_forwarding'] = \PatrickBroens\UrlForwarding\Hook\IndexTs::class . '->preprocessRequest';

// Check if a redirect is already available for a domain
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['url_forwarding'] = \PatrickBroens\UrlForwarding\Hook\TceMain::class;