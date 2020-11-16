<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
;

return PhpCsFixer\Config::create()
    ->setIndent('    ')
    ->setRules([
        '@Symfony'               => true,
        'binary_operator_spaces' => [
            'align_double_arrow' => true,
            'align_equals'       => true,
        ],
        'declare_equal_normalize' => [
            'space' => 'single',
        ],
        'phpdoc_align'                       => true,
        'single_blank_line_before_namespace' => false,
        'method_chaining_indentation'        => true,
        'no_blank_lines_before_namespace'    => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'return_type_declaration' => [
            'space_before' => 'one',
        ],
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'ternary_to_null_coalescing'      => true,
        'align_multiline_comment'         => true,
        'array_indentation'               => true,
        'combine_consecutive_issets'      => true,
        'combine_consecutive_unsets'      => true,
        'no_unused_imports'               => true,
        'ordered_imports'                 => true,
        'no_trailing_whitespace'          => true,
        'braces'                          => true,
        'simplified_null_return'          => false,
        'short_scalar_cast'               => true,
        'phpdoc_scalar'                   => true,
        'no_leading_import_slash'         => false,
        'phpdoc_summary'                  => false,
        'phpdoc_separation'               => false,
        'phpdoc_var_without_name'         => false,
        'phpdoc_single_line_var_spacing'  => false,
        'phpdoc_to_comment'               => false,
        'trim_array_spaces'               => false,
    ])
    ->setRiskyAllowed(false)
    ->setUsingCache(false)
    ->setFinder($finder)
;
