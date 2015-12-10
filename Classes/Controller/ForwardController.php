<?php
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

use PatrickBroens\UrlForwarding\Domain\Repository\RedirectRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;


class ForwardController
{

    /**
     * Redirect repository
     *
     * @var RedirectRepository
     */
    protected $redirectRepository;

    /**
     * inject redirect repository
     */
    public function __construct()
    {
        $this->redirectRepository = GeneralUtility::makeInstance(RedirectRepository::class);
    }

    /**
     * Check if a redirect exists and forward according to the redirects url and status
     *
     * @return void
     */
    public function forwardIfExists()
    {
        /** @var \PatrickBroens\UrlForwarding\Domain\Model\Redirect $redirect */
        $redirect = false;

        $request = parse_url(GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));

        $path = trim($request['path'], '/');
        $host = $request['host'];

        if ($path != '') {
            $redirect = $this->redirectRepository->findByPathAndDomain($host, $path);
        }

        if ($redirect) {
            header($redirect->getHttpStatus());
            header('Location: ' . $redirect->getUrl());
            exit();
        }
    }
}