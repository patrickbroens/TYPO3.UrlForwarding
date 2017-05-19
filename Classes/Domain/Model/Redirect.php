<?php
declare(strict_types = 1);
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

use TYPO3\CMS\Core\Resource\File;
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
    protected $languageUid = 0;

    /**
     * The type
     *
     * @var int
     */
    protected $type = 0;

    /**
     * The URL to be forwarded
     *
     * @var string
     */
    protected $forwardUrl = '';

    /**
     * The scheme
     *
     * @var string
     */
    protected $scheme = '';

    /**
     * The host
     *
     * @var string
     */
    protected $host = '';

    /**
     * The internal page
     *
     * @var int
     */
    protected $internalPage = 0;

    /**
     * The additional parameters
     *
     * @var string
     */
    protected $parameters = '';

    /**
     * The external url
     *
     * @var string
     */
    protected $externalUrl = '';

    /**
     * The internal file
     *
     * @var File
     */
    protected $internalFile;

    /**
     * The path to replace
     *
     * @var string
     */
    protected $path = '';

    /**
     * The http status
     *
     * @var int
     */
    protected $httpStatus = 0;

    /**
     * Constructs the Redirect
     *
     * @param int $languageUid The language uid
     * @param int $type The type of redirect
     * @param string $forwardUrl The URL to be forwarded
     * @param int $internalPage Uid of the internal page
     * @param string $parameters Additional parameters
     * @param string $externalUrl External URL
     * @param File|null $internalFile The internal file
     * @param string $path The path to replace
     * @param int $httpStatus The HTTP status
     */
    public function __construct(
        int $languageUid,
        int $type,
        string $forwardUrl,
        int $internalPage,
        string $parameters,
        string $externalUrl,
        $internalFile,
        string $path,
        int $httpStatus
    ) {
        $this->languageUid = $languageUid;
        $this->type = $type;
        $this->forwardUrl = $forwardUrl;
        $this->internalPage = $internalPage;
        $this->parameters = $parameters;
        $this->externalUrl = $externalUrl;
        $this->internalFile = $internalFile;
        $this->path = $path;
        $this->httpStatus = $httpStatus;
    }

    /**
     * Returns the URL, depending on the type of redirect
     *
     * @param string $scheme The scheme
     * @param string $host The host
     * @param string $oldPath The old path
     * @return null|string
     */
    public function getUrl(string $scheme = 'http', string $host = '', string $oldPath = '')
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
                $url = $this->constructUrlForInternalFile();
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
     * Returns true if the redirect has parameters
     *
     * @return bool
     */
    public function hasParameters()
    {
        return !empty($this->parameters);
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

        parse_str($this->parameters, $parameters);

        $parameters['L'] = $this->languageUid;

        return $this->getTypoScriptFrontendController()->cObj->typoLink_URL(
            [
                'parameter' => $this->internalPage,
                'forceAbsoluteUrl' => true,
                'additionalParams' => '&' . http_build_query($parameters)
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
     * Constructs the path to the internal file
     *
     * @return string
     */
    protected function constructUrlForInternalFile()
    {
        $url = '';

        if ($this->internalFile instanceof File) {
            $url = GeneralUtility::locationHeaderUrl($this->internalFile->getPublicUrl());
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
    protected function constructUrlForPath(string $scheme, string $host, string $oldPath)
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
            TypoScriptFrontendController::class,
            $GLOBALS['TYPO3_CONF_VARS'],
            $this->internalPage,
            0,
            1
        );
    }

    /**
     * Get the TypoScript frontend controller
     *
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}