<?php

namespace Deployer;

Deployer::get()->tasks->remove('deploy');

task('deploy', function () {
    throw new \Exception('Deploy is configured at CI level.');
});

task('dpeloy', ['deploy']);
