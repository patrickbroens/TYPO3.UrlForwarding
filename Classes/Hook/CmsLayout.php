<?php
namespace PatrickBroens\UrlForwarding\Hook;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use PatrickBroens\UrlForwarding\Domain\Repository\RedirectRepository;

/**
 * Hook for cms layout
 */
class CmsLayout
{
    /**
     * Check if a page has an internal redirect and add the redirect to the title
     *
     * @param array $parameters Array of 'table name / uid'
     * @param object|null $referringObject The referring object (mostly not passed)
     * @return string The addition for the page title
     */
    public function addRedirectToPageTitle(array $parameters, $referringObject)
    {
        $redirectWarning = '';

        // Early return when the table is not 'pages'
        if ($parameters[0] !== 'pages') {
            return $redirectWarning;
        }

        $internalRedirect = $this->getRedirectRepository()->findInternalRedirectByTargetPage((int)$parameters[1]);

        if ($internalRedirect) {
            $redirectText = $this->getLanguageService()->sL(
                'LLL:EXT:url_forwarding/Resources/Private/Language/Backend.xlf:internal_redirect'
            );

            $redirectWarning .= ' <strong>'
                . $redirectText . ':'
                . ' ' . htmlspecialchars($internalRedirect['forward_url'])
                . ' (' . $internalRedirect['uid'] . ') 
                . </strong>';
        }

        return $redirectWarning;
    }

    /**
     * Get the redirect repository
     *
     * @return RedirectRepository
     */
    protected function getRedirectRepository()
    {
        return GeneralUtility::makeInstance(RedirectRepository::class);
    }

    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}