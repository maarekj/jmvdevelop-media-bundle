<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ .'/src');

$config = new PhpCsFixer\Config();

$config->setRiskyAllowed(true);

$config->setRules(array(
    '@PSR2' => true,
    '@PSR12' => true,
    '@Symfony' => true,
    '@Symfony:risky' => true,
    '@PHP80Migration' => true,
    '@PHP80Migration:risky' => true,
    'declare_strict_types' => true,
    'use_arrow_functions' => false,
    'array_syntax' => ['syntax' => 'short'],
    'native_function_invocation' => [
        'include' => ['@internal', '@all'],
    ],
    'phpdoc_no_empty_return' => false,
    'phpdoc_to_comment' => false,
    'self_accessor' => false,
    'phpdoc_types_order' => false,
));

$config->setFinder($finder);

return $config;