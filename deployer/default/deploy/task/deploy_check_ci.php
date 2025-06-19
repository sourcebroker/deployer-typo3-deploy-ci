<?php

namespace Deployer;

use Deployer\Exception\GracefulShutdownException;

task('deploy:check_ci', function () {
    if (!getenv('CI')) {
        throw new GracefulShutdownException('This deploy task is supposed to be run in CI environment.');
    }
})->desc('Check if deployment is running in CI environment')->hidden();

after('deploy:info', 'deploy:check_ci');
