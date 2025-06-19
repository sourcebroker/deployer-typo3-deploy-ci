<?php

namespace Deployer;

task('deploy', [

    // Standard Deployer task.
    'deploy:info',

    // Standard Deployer task.
    'deploy:setup',

    // Standard Deployer task.
    'deploy:lock',

    // Standard Deployer task.
    'deploy:release',

    // deployer-typo3-deploy-ci task.
    'file:upload_build',

    // Standard Deployer task.
    'deploy:shared',

    // Standard Deployer task.
    'deploy:writable',

    // deployer-typo3-deploy-ci task.
    'typo3:cache:warmup:system',

    // deployer-typo3-deploy-ci task.
    'typo3:extension:setup',

    // Standard Deployer task.
    'deploy:symlink',

    // deployer-typo3-deploy-ci task.
    'typo3:cache:flush:pages',

    // Standard Deployer task.
    'deploy:unlock',

    // Standard Deployer task.
    'deploy:cleanup',

    // Standard Deployer task.
    'deploy:success',

])->desc('Deploy your TYPO3 (CI)');

fail('deploy', 'deploy:failed');
after('deploy:failed', 'deploy:unlock');
