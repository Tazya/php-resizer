<?php
$includedDirs = [
    __DIR__ . '/src',
    __DIR__ . '/Tests',
];

$excludedDirs = [
    __DIR__ . '/Tests/_helpers/_generated',
];

$finder = PhpCsFixer\Finder::create();

foreach ($includedDirs as $dir) {
    if (file_exists($dir) && is_dir($dir)) {
        $finder->in($dir);
    }
}

foreach ($excludedDirs as $dir) {
    if (file_exists($dir) && is_dir($dir)) {
        $finder->exclude($dir);
    }
}

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2'                                 => true,
        'binary_operator_spaces'                => [
            'operators' => [
                '=>'  => 'align',
                '='   => 'align',
                '+='  => 'align',
                '-='  => 'align',
                '*='  => 'align',
                '/='  => 'align',
                '%='  => 'align',
                '.='  => 'align',
                '&='  => 'align',
                '|='  => 'align',
                '^='  => 'align',
                '<<=' => 'align',
                '>>=' => 'align',
            ],
        ],
        'phpdoc_align'                          => true,
        'concat_space'                          => [
            'spacing' => 'one',
        ],
        'array_syntax'                          => [
            'syntax' => 'short',
        ],
        'no_blank_lines_before_namespace'       => true,
        'no_unused_imports'                     => true,
        'ordered_imports'                       => true,
        'no_trailing_whitespace'                => true,
        'braces'                                => false,
        'simplified_null_return'                => false,
        'short_scalar_cast'                     => true,
        'phpdoc_scalar'                         => true,
        'no_leading_import_slash'               => false,
        'phpdoc_summary'                        => false,
        'phpdoc_separation'                     => false,
        'no_blank_lines_after_phpdoc'           => true,
        'no_blank_lines_after_class_opening'    => true,
        'no_whitespace_in_blank_line'           => true,
        'phpdoc_add_missing_param_annotation'   => true,
        'no_whitespace_before_comma_in_array'   => true,
        'no_trailing_comma_in_singleline_array' => true,
        'trailing_comma_in_multiline_array'     => true,
        'no_leading_namespace_whitespace'       => true,
        'no_empty_comment'                      => true,
        'no_empty_statement'                    => true,
        'method_separation'                     => true,
        'declare_equal_normalize'               => true,
        'blank_line_before_return'              => true,
        'unary_operator_spaces'                 => true,
    ])
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
    ->setFinder($finder)
;