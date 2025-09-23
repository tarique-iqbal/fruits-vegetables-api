<?php

declare(strict_types=1);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'blank_line_between_import_groups' => false,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/config')
            ->in(__DIR__ . '/public')
            ->in(__DIR__ . '/tests')
    );
