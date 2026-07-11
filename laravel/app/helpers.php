<?php

use App\Enums\Request\RequestNameEnumInterface;

/**
 *
 * @param RequestNameEnumInterface $req
 * @return string
 */
function reqN(RequestNameEnumInterface $req, string $add = ''): string
{
    return $req->value.$add;
}
