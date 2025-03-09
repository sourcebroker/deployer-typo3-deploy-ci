<?php

namespace Deployer;

set('log_files', 'var/log/typo3_*.log');

$composerConfig = null;
if (file_exists('./composer.json')) {
    $composerContent = file_get_contents('./composer.json');
    if ($composerContent !== false) {
        $composerConfig = json_decode($composerContent, true);
    }
}

set('web_path', function () use ($composerConfig) {
    if (isset($composerConfig['extra']['typo3/cms']['web-dir'])) {
        return rtrim($composerConfig['extra']['typo3/cms']['web-dir'], '/') . '/';
    }

    return 'public/';
});

set('bin/typo3', function () use ($composerConfig) {
    $binDir = $composerConfig['config']['bin-dir'] ?? null;
    if ($binDir) {
        return file_exists($binDir . '/typo3cms') ? $binDir . '/typo3cms' : $binDir . '/typo3';
    }

    return file_exists('./vendor/bin/typo3cms') ? 'vendor/bin/typo3cms' : 'vendor/bin/typo3';
});

set('shared_files', [
    '.env'
]);

set('shared_dirs', function () {
    return [
        get('web_path') . 'fileadmin',
        get('web_path') . 'typo3temp/assets/_processed_',
        get('web_path') . 'typo3temp/assets/images',
        'var/charset',
        'var/lock',
        'var/log',
        'var/session',
    ];
});

set('writable_dirs', function () {
    return [
        get('web_path') . 'fileadmin',
        get('web_path') . 'typo3temp',
        'var',
    ];
});

set('clear_paths', [
    '.composer-cache',
    '.ddev',
    '.editorconfig',
    '.envrc',
    '.git',
    '.gitattributes',
    '.githooks',
    '.gitignore',
    '.gitlab-ci.yml',
    '.idea',
    '.php_cs',
    '.php-cs-fixer.php',
    'composer.phar',
    'dynamicReturnTypeMeta.json',
    'phive.xml',
    'phpcs.xml',
    'phpstan-baseline.neon',
    'phpstan.neon',
    'rector.php',
    'typoscript-lint.yml'
]);