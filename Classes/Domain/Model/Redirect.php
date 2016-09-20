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
     * The URL to be forwarded
     *
     * @var string
     */
    protected $forwardUrl;

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
     * The path to replace
     *
     * @var string
     */
    protected $path;

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
     * @param string $forwardUrl The URL to be forwarded
     * @param int $internalPage Uid of the internal page
     * @param string $externalUrl External URL
     * @param string $internalFile Path to the internal file
     * @param string $path The path to replace
     * @param int $httpStatus The HTTP status
     */
    public function __construct(
        $languageUid,
        $type,
        $forwardUrl,
        $internalPage,
        $externalUrl,
        $internalFile,
        $path,
        $httpStatus
    ) {
        $this->languageUid = (int) $languageUid;
        $this->type = (int) $type;
        $this->forwardUrl = (string) $forwardUrl;
        $this->internalPage = (int) $internalPage;
        $this->externalUrl = (string) $externalUrl;
        $this->internalFile = (string) $internalFile;
        $this->path = (string) $path;
        $this->httpStatus = (int) $httpStatus;
    }

    /**
     * Returns the URL, depending on the type of redirect
     *
     * @param string $scheme The scheme
     * @param string $host The host
     * @param string $oldPath The old path
     * @return null|string
     */
    public function getUrl($scheme = 'http', $host = '', $oldPath = '')
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
            // Path
            case 3:
                $url = $this->constructUrlForPath($scheme, $host, $oldPath);
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
     * Constructs a new path by replacing the old path with the new one
     *
     * @param string $scheme The scheme
     * @param string $host The host
     * @param string $oldPath The old complete path
     * @return string The url with the replaced part of the path
     */
    protected function constructUrlForPath($scheme, $host, $oldPath)
    {
        $needle = '/'.preg_quote($this->path, '/').'/';

        $newPath = preg_replace($needle, trim($this->forwardUrl, '/'), $oldPath, 1);

        return $scheme . '://' . $host . '/' . $newPath;
    }


    /**
     * Initialize the TypoScript frontend controller
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