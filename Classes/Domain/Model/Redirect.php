<?php
namespace PatrickBroens\UrlForwarding\Domain\Model;

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
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * A redirect
 */
class Redirect
{
    /**
     * The HTTP status headers
     *
     * @var array
     */
    protected static $httpStatusHeaders = [
        301 => 'HTTP/1.1 301 Moved Permanently',
        302 => 'HTTP/1.1 302 Found',
        303 => 'HTTP/1.1 303 See Other',
        307 => 'HTTP/1.1 307 Temporary Redirect'
    ];

    /**
     * The language uid
     *
     * @var int
     */
    protected $languageUid;

    /**
     * The type
     *
     * @var int
     */
    protected $type;

    /**
     * The scheme
     *
     * @var string
     */
    protected $scheme;

    /**
     * The host
     *
     * @var string
     */
    protected $host;

    /**
     * The internal page
     *
     * @var int
     */
    protected $internalPage;

    /**
     * The external url
     *
     * @var string
     */
    protected $externalUrl;

    /**
     * The internal file
     *
     * @var string
     */
    protected $internalFile;

    /**
     * The http status
     *
     * @var int
     */
    protected $httpStatus;

    /**
     * Constructs the Redirect
     *
     * @param int $languageUid The language uid
     * @param int $type The type of redirect
     * @param int $internalPage Uid of the internal page
     * @param string $externalUrl External URL
     * @param string $internalFile Path to the internal file
     * @param int $httpStatus The HTTP status
     */
    public function __construct(
        $languageUid,
        $type,
        $internalPage,
        $externalUrl,
        $internalFile,
        $httpStatus
    ) {
        $this->languageUid = (int) $languageUid;
        $this->type = (int) $type;
        $this->internalPage = (int) $internalPage;
        $this->externalUrl = (string) $externalUrl;
        $this->internalFile = (string) $internalFile;
        $this->httpStatus = (int) $httpStatus;
    }

    /**
     * Returns the URL, depending on the type of redirect
     *
     * @return null|string
     */
    public function getUrl()
    {
        $url = null;

        switch ($this->type) {
            // Internal page
            case 0:
                $url = $this->constructUrlForInternalPage();
                break;
            // External URL
            case 1:
                $url = $this->constructUrlForExternal();
                break;
            // Internal file
            case 2:
                $url = GeneralUtility::locationHeaderUrl($this->internalFile);
                break;
        }

        return $url;
    }

    /**
     * Returns the http status header
     *
     * @return int
     */
    public function getHttpStatus()
    {
        return self::$httpStatusHeaders[$this->httpStatus];
    }

    /**
     * Construct the URL for an internal page
     *
     * We need TSFE for that, but this is not available at this point
     * so we need to call it.
     *
     * @return string
     */
    protected function constructUrlForInternalPage()
    {
        \TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();

        $this->initializeTypoScriptFrontendController();

        return $this->getTypoScriptFrontendController()->cObj->typoLink_URL(
            [
                'parameter' => $this->internalPage,
                'forceAbsoluteUrl' => true,
                'additionalParams' => '&L=' . $this->languageUid
            ]
        );
    }

    /**
     * Constructs the external url
     *
     * Checks if scheme has been entered. If not, it will add http as scheme
     *
     * @return string
     */
    protected function constructUrlForExternal()
    {
        $url = $this->externalUrl;

        $parsed = parse_url($url);
        if (empty($parsed['scheme'])) {
            $url = 'http://' . ltrim($url, '/');
        }

        return $url;
    }


    /**
     * Initialize the TypoScript frontend controller
     *
     * @return void
     */
    protected function initializeTypoScriptFrontendController()
    {
        $this->setTypoScriptFrontendController();

        $this->getTypoScriptFrontendController()->connectToDB();
        $this->getTypoScriptFrontendController()->initFEuser();
        $this->getTypoScriptFrontendController()->fetch_the_id();
        $this->getTypoScriptFrontendController()->getPageAndRootline();
        $this->getTypoScriptFrontendController()->initTemplate();
        $this->getTypoScriptFrontendController()->tmpl->getFileName_backPath = PATH_site;
        $this->getTypoScriptFrontendController()->forceTemplateParsing = 1;
        $this->getTypoScriptFrontendController()->getConfigArray();
        $this->getTypoScriptFrontendController()->cObj = GeneralUtility::makeInstance(
            \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class
        );
    }

    /**
     * Set the TypoScript frontend controller
     */
    protected function setTypoScriptFrontendController()
    {
        $GLOBALS['TSFE'] = GeneralUtility::makeInstance(
            \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class,
            $GLOBALS['TYPO3_CONF_VARS'],
            $this->internalPage,
            0,
            1
        );
    }

    /**
     * Get the TypoScript frontend controller
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}