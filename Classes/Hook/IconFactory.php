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
 * Hook for the icon factory
 */
class IconFactory
{
    /**
     * Check if a page has an internal redirect and change the overlay icon if needed
     *
     * @param string $table The table name for the icon
     * @param array $row The row of the record
     * @param array $status The status of the record
     * @param string $iconName The name of the overlay icon
     * @return string The name of the overlay icon
     */
    public function postOverlayPriorityLookup($table, $row, $status, &$iconName)
    {
        // Early return when the table is not 'pages'
        if ($table !== 'pages') {
            return $iconName;
        }

        $internalRedirect = $this->getRedirectRepository()->findInternalRedirectByTargetPage((int)$row['uid']);

        if ($internalRedirect && !$status['hidden']) {
            $iconName = 'extensions-url_forwarding-overlay-redirect';
        }

        return $iconName;
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
}