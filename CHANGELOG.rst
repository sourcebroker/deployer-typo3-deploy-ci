
Changelog
---------

main
-----

1) [BUGFIX] Fix remote inclusions.
2) [TASK] Refactor ``next`` ddev command.
3) [TASK][BREAKING] Rename main.yml file.

0.8.0
-----

1) [TASK][BREAKING]  Remove ``deploy:check_remote`` task to be more compatible with default Deployer settings.
   Fix order of ``deploy:setup``.

0.7.0
-----

1) [TASK] Remove dependency to ``helhum/typo3-console``.

0.6.0
-----

1) [TASK] Rename ``deployer/default/typo3cms`` to ``deployer/default/typo3``
2) [TASK] Add ``deploy:lock`` and ``deploy:unlock`` to ``deploy`` task. Add task comments.
3) [TASK] Remove not used ``set('composer_channel', 2);``. It must be set now explicitly in project deploy.php
    or custom deploy package and ``bin/composer`` override must be explicitly included with loader
    ``'path' => 'vendor/sourcebroker/deployer-extended/includes/composer.php'``
4) [TASK] Fix ``bin/typo3`` not having fallback to ``typo3cms`` when ``$composerConfig['config']['bin-dir']`` is set.
5) [TASK] Remove overwrite of ``user`` setting as Deployer default ``user`` applies now CI user name.

0.5.0
-----

1) [TASK] Bump ``sourcebroker/deployer-extended`` version.

0.4.2
-----

1) [TASK] Add REVISION so "dep releases [host]" can show the hash also.

0.4.1
-----

1) [TASK] Add "next" ddev command to bump version in gitlab CI file.


0.4.0
-----

1) [TASK] Remove not existing "allow_anonymous_stats" setting. Optimise conditions for web_path and bin/typo3.
2) [TASK][BREAKING] Move typo3:cache:warmup:system before typo3:extension:setup in deploy task. Remove deploy:clear_paths
   as file:upload_build should already exclude files from clear_paths.

0.3.0
-----

1) [TASK][BREAKING] Remove ``writable_mode``, ``default_timeout``, ``keep_releases`` out of standard configuration. Use default values from
   deployer or build you custom package to manage.

0.2.0
~~~~~

1) [TASK] Drop dependency to ``typo3/cms-core``. Extend dependency to ``helhum/typo3-console``.
2) [TASK] Add support for ``vendor/bin/typo3cms``.
3) [TASK] Add ``gitlab-ci.yml`` to ``clear_path``.

0.1.0
~~~~~

1) [TASK] Remove not needed loader config path. Reads from default ``config/loader.php``.
2) [TASK] Update dependency to ``sourcebroker/deployer-loader``.

0.0.1
~~~~~

1) Init version.
