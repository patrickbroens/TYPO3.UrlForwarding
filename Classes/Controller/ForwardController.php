<?php
declare(strict_types = 1);
namespace PatrickBroens\UrlForwarding\Controller;

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
use PatrickBroens\UrlForwarding\Domain\Repository\RedirectRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;

/**
 * Controller to handle the redirects
 */
class ForwardController
{
    /**
     * Redirect repository
     *
     * @var RedirectRepository
     */
    protected $redirectRepository;

    /**
     * Inject redirect repository
     */
    public function __construct()
    {
        $this->redirectRepository = GeneralUtility::makeInstance(RedirectRepository::class);
    }

    /**
     * Check if a redirect exists and forward according to the redirect url and status
     */
    public function forwardIfExists()
    {
        $url = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');

        $request = parse_url($url);

        $path = trim($request['path'], '/');
        $host = $request['host'];
        $scheme = $request['scheme'];

        if ($path !== '') {
            $redirect = $this->redirectRepository->findByPathAndDomain($host, $path);

            if ($redirect) {
                $this->redirect($redirect, $scheme, $host, $path);
            }
        }
    }

    /**
     * Redirect to the new URI
     *
     * @param Redirect $redirect The redirect record
     * @param string $scheme The scheme from the request
     * @param string $host The host from the request
     * @param string $path The path from the request
     */
    protected function redirect(Redirect $redirect, string $scheme, string $host, string $path)
    {
        HttpUtility::redirect($redirect->getUrl($scheme, $host, $path), $redirect->getHttpStatus());
    }
}