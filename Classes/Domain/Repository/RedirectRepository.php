<?php
declare(strict_types = 1);
namespace PatrickBroens\UrlForwarding\Domain\Repository;

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

use PatrickBroens\UrlForwarding\Domain\Model\Redirect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Dbal\Database\DatabaseConnection;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Redirect repository
 */
class RedirectRepository
{
    /**
     * The table name
     */
    const TABLE_NAME = 'tx_urlforwarding_domain_model_redirect';

    /**
     * Constructor
     *
     * Database connection will come after the hook we use, so we need to connect
     *
     * @return void
     */
    public function __construct()
    {
        if (!$this->getDatabaseConnection()->isConnected()) {
            $this->getDatabaseConnection()->connectDB();
        }
    }

    /**
     * Find the redirect by path and possible limited domain
     *
     * @param string $host The host, in case of limiting
     * @param string $path The path to search for
     * @return Redirect|null
     */
    public function findByPathAndDomain(string $host, string $path)
    {
        $redirect = null;

        $pathQuoted = $this->getDatabaseConnection()->fullQuoteStr($path, 'tx_urlforwarding_domain_model_redirect');
        $hostQuoted = $this->getDatabaseConnection()->fullQuoteStr($host, 'sys_domain');

        $result = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
            '
                tx_urlforwarding_domain_model_redirect.uid,
                tx_urlforwarding_domain_model_redirect.sys_language_uid,
                tx_urlforwarding_domain_model_redirect.type,
                tx_urlforwarding_domain_model_redirect.forward_url,
                tx_urlforwarding_domain_model_redirect.internal_page,
                tx_urlforwarding_domain_model_redirect.parameters,
                tx_urlforwarding_domain_model_redirect.external_url,
                tx_urlforwarding_domain_model_redirect.internal_file,
                tx_urlforwarding_domain_model_redirect.path,
                tx_urlforwarding_domain_model_redirect.http_status                
            ',
            '
                tx_urlforwarding_domain_model_redirect
                LEFT JOIN tx_urlforwarding_domain_mm
                ON tx_urlforwarding_domain_mm.uid_local=tx_urlforwarding_domain_model_redirect.uid
                LEFT JOIN sys_domain
                ON sys_domain.uid=tx_urlforwarding_domain_mm.uid_foreign
            ',
            '
                (
                    (
                        TRIM(BOTH \'/\' FROM tx_urlforwarding_domain_model_redirect.forward_url)=' . $pathQuoted . '
                        AND tx_urlforwarding_domain_model_redirect.type IN (0,1,2)
                    )
                    OR (
                        LOCATE(
                            TRIM(BOTH \'/\' FROM tx_urlforwarding_domain_model_redirect.path), 
                            ' . $pathQuoted . '
                        ) = 1
                        AND tx_urlforwarding_domain_model_redirect.type=3
                    )
                )
                AND (
                    sys_domain.domainName IS null
                    OR (
                        sys_domain.domainName=' . $hostQuoted . '
                        AND sys_domain.hidden=0
                    )
                )
                AND (
                    tx_urlforwarding_domain_model_redirect.starttime<=' . (int)$GLOBALS['SIM_ACCESS_TIME'] . '
                )
                AND (
                    tx_urlforwarding_domain_model_redirect.endtime=0
                    OR tx_urlforwarding_domain_model_redirect.endtime>' . (int)$GLOBALS['SIM_ACCESS_TIME'] . '
                )
                AND tx_urlforwarding_domain_model_redirect.hidden=0
                AND tx_urlforwarding_domain_model_redirect.deleted=0
                AND tx_urlforwarding_domain_model_redirect.forward_url!=\'\'
            '
        );

        if ($result) {
            $this->updateCounterAndLastHit((int)$result['uid']);

            /** @var Redirect $redirect */
            $redirect = GeneralUtility::makeInstance(
                Redirect::class,
                (int)$result['sys_language_uid'],
                (int)$result['type'],
                (string)$result['forward_url'],
                (int)$result['internal_page'],
                (string)$result['parameters'],
                (string)$result['external_url'],
                $this->getInternalFile($result),
                (string)$result['path'],
                (int)$result['http_status']
            );
        }

        return $redirect;
    }

    /**
     * Insert a redirect
     *
     * Scope of this method is external use, like other extensions
     *
     * @param array $insertData
     */
    public function insert(array $insertData)
    {
        $queryBuilder = static::getQueryBuilderForTable(self::TABLE_NAME);

        $queryBuilder
            ->insert(self::TABLE_NAME)
            ->values($insertData)
            ->execute();
    }

    /**
     * Increment the counter with 1 and update the last_hit field with the current timestamp
     *
     * @param int $uid The uid of the redirect record
     */
    protected function updateCounterAndLastHit(int $uid)
    {
        $this->getDatabaseConnection()->sql_query('
            UPDATE tx_urlforwarding_domain_model_redirect
            SET counter=counter + 1, last_hit=' . (int)$GLOBALS['SIM_ACCESS_TIME'] . '
            WHERE uid=' . $uid . '
        ');
    }

    /**
     * Get records with the same "forward_url"
     *
     * @param string $uidEditedRecord The uid of the edited record. When new contains 'NEW'
     * @param array $editedRecord The fields of the edited record
     * @return mixed
     */
    public function getEqualRecords(string $uidEditedRecord, array $editedRecord)
    {
        $pathQuoted = $this->getDatabaseConnection()->fullQuoteStr((string)$editedRecord['forward_url'],
            'tx_urlforwarding_domain_model_redirect');
        $whereUid = '';

        if (strpos($uidEditedRecord, 'NEW') === false) {
            $whereUid = ' AND uid<>' . (int)$uidEditedRecord;
        }

        return $this->getDatabaseConnection()->exec_SELECTgetRows(
            '
                tx_urlforwarding_domain_model_redirect.uid,
                tx_urlforwarding_domain_model_redirect.type,
                GROUP_CONCAT(tx_urlforwarding_domain_mm.uid_foreign SEPARATOR \',\') AS domainUids,
                tx_urlforwarding_domain_model_redirect.parameters
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
     * Find a redirect by the internal type which has a certain target page
     *
     * @param int $pageUid The target page to search for
     * @return array|FALSE|NULL
     */
    public function findInternalRedirectByTargetPage(int $pageUid)
    {
        return $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
            '
                uid,
                forward_url,
                parameters
            ',
            'tx_urlforwarding_domain_model_redirect',
            '
                type=\'0\' 
                AND internal_page=\'' . $pageUid . '\'
                AND deleted=0
            '
        );
    }

    /**
     * Get the internal file
     *
     * @param array $result The result
     * @return null|File
     */
    protected function getInternalFile(array $result)
    {
        $file = null;

        if ((int)$result['internal_file'] === 1) {
            /** @var FileRepository $fileReferencesRepository */
            $fileRepository = GeneralUtility::makeInstance(FileRepository::class);

            $file = $fileRepository->findFileByRelation(
                'tx_urlforwarding_domain_model_redirect',
                'internal_file',
                (int)$result['uid']
            );
        }

        return $file;
    }

    /**
     * Gets database instance
     *
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Get the query builder for a table
     *
     * @param string $table
     * @return QueryBuilder
     */
    protected static function getQueryBuilderForTable($table)
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
    }
}