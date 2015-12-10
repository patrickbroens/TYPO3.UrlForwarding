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
use \TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;

/**
 * When adding a new element, check if the redirect is already there within a certain domain.
 */
class TceMain
{
    /**
     * Flash message queue
     *
     * @var \TYPO3\CMS\Core\Messaging\FlashMessageQueue
     */
    protected $flashMessageQueue;

    /**
     * Process the data array and check if there are stored redirects
     * which are covering the same 'forward_url' and domain(s)
     *
     * $referringObject->datamap['tx_urlforwarding_domain_model_redirect'] contains the changes made to records
     *
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $referringObject The object calling this hook
     * @return void
     */
    public function processDatamap_beforeStart(\TYPO3\CMS\Core\DataHandling\DataHandler $referringObject)
    {
        if (!isset($referringObject->datamap['tx_urlforwarding_domain_model_redirect'])) {
            return;
        }

        $allowed = true;

        foreach ($referringObject->datamap['tx_urlforwarding_domain_model_redirect'] as $uidEditedRecord => $editedRecord) {

            $editedRecord['domain'] = trim($editedRecord['domain'], ',');

            $equalRecords = $this->getEqualRecords($uidEditedRecord, $editedRecord);

            // Does the url exist in another record
            if (!empty($equalRecords)) {

                // The edited record does not have domains assigned, so not allowed
                if (empty($editedRecord['domain'])) {
                    $allowed = false;

                    // Lets test on domains
                } else {
                    foreach ($equalRecords as $equalRecord) {
                        if ($equalRecord['domainUids'] === null) {
                            $allowed = false;
                            break;
                        } else {
                            $editedRecordDomainUids = GeneralUtility::intExplode(',', $editedRecord['domain']);
                            $equalRecordDomainUids = GeneralUtility::intExplode(',', $equalRecord['domainUids']);

                            $equalDomainUids = array_intersect($editedRecordDomainUids, $equalRecordDomainUids);

                            if (!empty($equalDomainUids)) {
                                $allowed = false;
                                break;
                            }
                        }
                    }
                }
            }

            if (!$allowed) {
                unset($referringObject->datamap['tx_urlforwarding_domain_model_redirect'][$uidEditedRecord]);

                /** @var FlashMessage $flashMessage */
                $flashMessage = GeneralUtility::makeInstance(
                    FlashMessage::class,
                    'A redirect with the name "' . htmlspecialchars($editedRecord['forward_url']) . '" is already covering the same domain. This record has not been stored.',
                    'An error occured',
                    FlashMessage::ERROR,
                    true
                );
                $this->getFlashMessageQueue()->addMessage($flashMessage);
            }
        }

    }

    /**
     * Get records with the same "forward_url"
     *
     * @param int $uidEditedRecord The uid of the edited record
     * @param array $editedRecord The fields of the edited record
     * @return mixed
     */
    protected function getEqualRecords($uidEditedRecord, $editedRecord)
    {
        $pathQuoted = $this->getDatabaseConnection()->fullQuoteStr((string)$editedRecord['forward_url'],
            'tx_urlforwarding_domain_model_redirect');
        $whereUid = '';

        if (strpos($uidEditedRecord, 'NEW') === false) {
            $whereUid = ' AND uid<>' . $uidEditedRecord;
        }

        return $this->getDatabaseConnection()->exec_SELECTgetRows(
            '
                tx_urlforwarding_domain_model_redirect.uid,
                GROUP_CONCAT(tx_urlforwarding_domain_mm.uid_foreign SEPARATOR \',\') AS domainUids
            ',
            '
                tx_urlforwarding_domain_model_redirect
                LEFT JOIN tx_urlforwarding_domain_mm
                ON tx_urlforwarding_domain_mm.uid_local = tx_urlforwarding_domain_model_redirect.uid
            ',
            '
                TRIM(BOTH \'/\' FROM tx_urlforwarding_domain_model_redirect.forward_url)=' . $pathQuoted . '
                AND tx_urlforwarding_domain_model_redirect.deleted<>1
                ' . $whereUid . '
            ',
            '
                tx_urlforwarding_domain_model_redirect.uid
            '
        );
    }

    /**
     * Get the Flash Message Queue
     *
     * @return \TYPO3\CMS\Core\Messaging\FlashMessageQueue
     */
    protected function getFlashMessageQueue()
    {
        if (!isset($this->flashMessageQueue)) {
            /** @var FlashMessageService $service */
            $service = GeneralUtility::makeInstance(FlashMessageService::class);
            $this->flashMessageQueue = $service->getMessageQueueByIdentifier();
        }
        return $this->flashMessageQueue;
    }

    /**
     * Gets database instance
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}