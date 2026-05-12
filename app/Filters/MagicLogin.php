<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class MagicLogin implements FilterInterface
{
    /**
     * Force users who just logged in via a magic link to set a password
     * before they can access anything else.
     *
     * Shield's MagicLinkController sets a `magicLogin` tempdata session flag
     * after a successful verification. This filter watches for that flag and
     * locks navigation to the set-password flow until the user clears it by
     * saving a new password (which removes the tempdata).
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = uri_string();

        // Only if magic login active
        if (session('magicLogin')) {

            // allow set-password page + submit + logout
            $allowed = [
                'set-password',
                'set-password/save',
                'logout',
            ];

            if (! in_array($uri, $allowed, true)) {
                return redirect()->to('/set-password');
            }
        }
    }

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
