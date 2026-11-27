<?php

/**
 * Add-on badges survive every call to BadgeLibrary::all(), not only the first.
 *
 * all() used to cache the bundled set and return it unfiltered from the
 * second call on, so has() refused add-on badges on save.
 *
 * existing-checked: tests/checkout-captions-check.php - it stubs apply_filters for trust/badges only and never calls all() twice.
 *
 * Run: php tests/badge-library-filter-check.php
 */

declare(strict_types=1);

define('ABSPATH', __DIR__);

function apply_filters(string $hook, mixed $value, mixed ...$args): mixed
{
    if ('trust/badge_library' === $hook) {
        $value['gift_wrapping'] = ['label' => 'Gift wrapping', 'svg' => '<svg></svg>'];
    }
    return $value;
}

function __(string $text, string $domain): string
{
    return $text;
}

function _x(string $text, string $context, string $domain): string
{
    return $text;
}

require dirname(__DIR__) . '/src/Badges/BadgeLibrary.php';

use Trust\Badges\BadgeLibrary;

$first  = count(BadgeLibrary::all());
$second = count(BadgeLibrary::all());

if ($first !== $second || ! BadgeLibrary::has('gift_wrapping') || BadgeLibrary::label('gift_wrapping') !== 'Gift wrapping') {
    fwrite(STDERR, "FAIL: first call {$first} badges, second {$second}; add-on badge lost after the first call\n");
    exit(1);
}

echo "OK: add-on badge kept on every call ({$second} badges)\n";
