<?php

namespace Deployer;

task('deploy-ci', [

    'deploy:info',
    'deploy:check_remote',
    'deploy:setup',
    'deploy:release',
    'file:upload_build',
    'deploy:shared',
    'deploy:writable',
    'typo3:cache:warmup:system',
    'typo3:extension:setup',
    'deploy:symlink',
    'cache:clear_php_cli',
    'cache:clear_php_http',
    'typo3:cache:flush:pages',
    'deploy:cleanup',
    'deploy:success',

])->desc('Deploy your TYPO3 (CI)');

fail('deploy-ci', 'deploy:failed');