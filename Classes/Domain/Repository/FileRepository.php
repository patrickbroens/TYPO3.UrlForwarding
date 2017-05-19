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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dbal\Database\DatabaseConnection;

/**
 * File repository
 *
 * Not using the core File Repository, since we have no TSFE available
 */
class FileRepository
{
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
     * Find a single file relation
     *
     * @param string $tableName The table name
     * @param string $fieldName The field name
     * @param int $uid The uid
     * @return null|File
     */
    public function findFileByRelation(string $tableName, string $fieldName, int $uid)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file_reference');

        $res = $queryBuilder
            ->select('file.*')
            ->from('sys_file_reference', 'reference')
            ->join(
                'reference',
                'sys_file',
                'file',
                $queryBuilder->expr()->eq(
                    'file.uid',
                    $queryBuilder->quoteIdentifier('reference.uid_local')
                )
            )
            ->where(
                $queryBuilder->expr()->eq(
                    'uid_foreign',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'tablenames',
                    $queryBuilder->createNamedParameter($tableName, \PDO::PARAM_STR)
                ),
                $queryBuilder->expr()->eq(
                    'fieldname',
                    $queryBuilder->createNamedParameter($fieldName, \PDO::PARAM_STR)
                )
            )
            ->execute();

        return $this->getFile($res->fetch());
    }

    /**
     * Get the file
     *
     * @param array $data The file data
     * @return null|File
     */
    protected function getFile(array $data)
    {
        $file = null;

        if ($data) {
            /** @var ResourceFactory $resourceFactory */
            $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

            $file = $resourceFactory->getFileObject($data['uid'], $data);
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
     * @param string $table The table
     * @return QueryBuilder
     */
    protected static function getQueryBuilderForTable(string $table)
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
    }
}