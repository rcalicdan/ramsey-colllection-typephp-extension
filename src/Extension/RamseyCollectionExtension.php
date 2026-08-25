<?php

declare(strict_types=1);

namespace Rcalicdan\RamseyExtension\Extension;

use TypePHP\Extension\ExtensionInterface;

class RamseyCollectionExtension implements ExtensionInterface
{
    /**
     * Returns configuration overrides provided by this extension.
     * Whitelists the vendor source files and registers the covariant stub definitions.
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return [
            'include' => [
                'vendor/ramsey/collection/src/**',
            ],
        ];
    }
}