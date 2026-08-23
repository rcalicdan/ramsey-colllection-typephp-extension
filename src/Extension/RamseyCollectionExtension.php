<?php

declare(strict_types=1);

namespace App\Extension;

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
        $stubPath = str_replace('\\', '/', realpath(__DIR__ . '/../../stubs/ramsey-collection.stub.php') ?: '');

        return [
            'include' => [
                'vendor/ramsey/collection/src/**',
            ],
            'stubs' => [
                $stubPath !== '' ? $stubPath : 'stubs/ramsey-collection.stub.php',
            ],
        ];
    }
}