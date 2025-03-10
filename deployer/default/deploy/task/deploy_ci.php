<?php

namespace Deployer;

task('deploy-ci', [

    // Standard Deployer task.
    'deploy:info',

    // Standard Deployer task.
    'deploy:setup',

    // Standard Deployer task.
    'deploy:lock',

    // Standard Deployer task.
    'deploy:release',

    // Standard Deployer task.
    'file:upload_build',

    // Standard Deployer task.
    'deploy:shared',

    // Standard Deployer task.
    'deploy:writable',

    // TYPO3 special task: warm up system cache
    'typo3:cache:warmup:system',

    // TYPO3 special task: set up extension with database update schema
    'typo3:extension:setup',

    // Standard Deployer task.
    'deploy:symlink',

    // sourcebroker/deployer-extended special task.
    // read more on https://github.com/sourcebroker/deployer-extended#cache-clear-php-cli
    'cache:clear_php_cli',

    // sourcebroker/deployer-extended special task.
    // read more on https://github.com/sourcebroker/deployer-extended#cache-clear-php-http
    'cache:clear_php_http',

    // TYPO3 special task: flush cache for pages.
    'typo3:cache:flush:pages',

    // Standard Deployer task.
    'deploy:unlock',

    // Standard Deployer task.
    'deploy:cleanup',

    // Standard Deployer task.
    'deploy:success',

])->desc('Deploy your TYPO3 (CI)');

fail('deploy-ci', 'deploy:failed');
after('deploy:failed', 'deploy:unlock');
