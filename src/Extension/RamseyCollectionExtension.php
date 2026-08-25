<?php

declare(strict_types=1);

namespace Rcalicdan\RamseyExtension\Extension;

use TypePHP\Extension\ExtensionInterface;

final class RamseyCollectionExtension implements ExtensionInterface
{
    /**
     * Returns configuration overrides provided by this extension.
     * Selectively whitelists only the generic collection and map classes,
     * skipping non-generic exceptions, traits, and enums.
     *
     * @return array{include?: list<string>, stubs?: list<string>}
     */
    public function getConfig(): array
    {
        return [
            'include' => [
                'vendor/ramsey/collection/src/*.php',
                'vendor/ramsey/collection/src/Map/*.php',
            ],
        ];
    }
}