<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Ramsey\Uuid\Uuid;

function imageUrl($slug = '', $width = NULL, $height = NULL, $priority = 'width'): string
{
    $url = base_url("assets/images/{$slug}?priority={$priority}");

    if (!is_null($width))
    {
        $url .= "&width={$width}";
    }

    if (!is_null($height))
    {
        $url .= "&height={$height}";
    }

    return $url;
}

function numbering(array $array = [], int $start = 0): array
{
    foreach ($array as $key => $value):

        $start++;
        $array[$key]['no'] = number_format($start, 0, ',', '.');

    endforeach;

    return $array;
}

function prettyPrint($var): void
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function cannotAccessModule($status = 'error', $message = 'Anda tidak memiliki izin untuk mengakses halaman ini.'): ResponseInterface
{
    $response = Services::response();

    return $response->setStatusCode(403)->setJSON([
        'status'  => $status,
        'message' => $message
    ]);
}

function createRandomID(): string
{
    // return random ID
    return Uuid::uuid4()->toString();
}