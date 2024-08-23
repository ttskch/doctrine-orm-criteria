<?php

$finder = (new PhpCsFixer\Finder())
    ->in('lib')
    ->in('src')
    ->in('tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'phpdoc_summary' => false,
        'nullable_type_declaration_for_default_null_value' => true,
        'ordered_interfaces' => true,
        'ordered_traits' => true,
        'yoda_style' => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
