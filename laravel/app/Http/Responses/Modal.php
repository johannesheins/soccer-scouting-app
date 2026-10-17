<?php

namespace App\Http\Responses;

use Emargareten\InertiaModal\Modal as BaseModal;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

/**
 * The base modal response pulls the flash data from the session while resolving
 * the shared props but never adds it to the page, so flashed toasts get lost.
 */
class Modal extends BaseModal
{
    protected function renderModal(): JsonResponse
    {
        $flash = Inertia::getFlashed();
        $response = parent::renderModal();

        if ($flash !== []) {
            $response->setData([...$response->getData(true), 'flash' => $flash]);
        }

        return $response;
    }
}
