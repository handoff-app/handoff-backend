<?php

namespace App\Listeners;

use App\Contracts\Services\Auth\TokenService;
use App\Events\FileDownloaded;

/**
 * Class RevokeToken
 * @package App\Listeners
 *
 * This class shouldn't queue, since we want the token to be revoked as soon as possible
 */
class RevokeToken
{
    /**
     * @var TokenService
     */
    private $tokenService;

    /**
     * Create the event listener.
     *
     * @param TokenService $tokenService
     */
    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Handle the event.
     * @todo if it turns out we're handling more than the FileDownloaded event, move to an event subscriber model
     * @param FileDownloaded $event
     */
    public function handle(FileDownloaded $event)
    {
        $this->tokenService->revoke($event->token);
    }
}
