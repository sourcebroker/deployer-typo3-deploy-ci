# Installation

> **Before you start:** [Mirror this repository](85-gitlab-mirror-setup-free.md) to your own
> GitLab instance before referencing it in your pipeline. CI YAML files execute arbitrary code
> with full access to your secrets — there is no hash verification for GitLab CI includes, so an
> upstream compromise would affect your pipelines immediately.

1. Install with composer:

   ```sh
   composer require sourcebroker/deployer-typo3-deploy-ci
   ```

   You can also install from your own GitLab mirror instead of packagist.org — see [Why mirror](80-why-mirror.md).

2. Create a file `gitlab-ci.yml` in the root of your project and put the content below.

   [Mirror this repository](85-gitlab-mirror-setup-free.md) to your own GitLab group first — this
   applies whether you use gitlab.com or a self-hosted instance. Including directly from a third-party
   project means executing code you do not control. Then reference your own mirror:

   ```yaml
   include:
     - project: 'my-group/deployer-typo3-deploy-ci'
       ref: '2.0.0'
       file: '/ci/provider/gitlab/main.yaml'
   ```

   Then add your project variables:

   ```yaml
   variables:
     PHP: '8.2'
     NODE: '20'
     DEPLOY_TRIGGER_BY_CI_COMMIT_BRANCH: /^(develop|main)$/
     DEPLOYER_SELECTOR_FOR_BRANCH: develop:staging,main:preprod
     DEPLOYER_SELECTOR_FOR_TAG: prod
   ```

   Adapt the tag version to match the version of `deployer-typo3-deploy-ci` installed in step 1.
   Use `composer show | grep 'sourcebroker/deployer-typo3-deploy-ci'` to check the installed version.

   > **Warning:** The CI `ref:` version (here) and the Composer version (step 1) are two completely
   > independent settings — even when both point to the same repository. When upgrading, you must
   > update **both places at the same time**, otherwise the CI jobs and the PHP deployer tasks will
   > be out of sync and may break the pipeline.

   If the pipeline does not start after pushing, check `TEST_TRIGGER_BY_CI_COMMIT_BRANCH` — the
   branch name must match its regexp.

3. **Backend test** — defined in `BACKEND_COMMAND_TEST`:
   ```sh
   composer install --prefer-dist --no-progress --no-interaction --optimize-autoloader && composer test
   ```
   Overwrite in `gitlab-ci.yml` or add a `test` script to `composer.json`.

4. **Frontend test** — defined in `FRONTEND_COMMAND_TESTS`:
   ```sh
   cd assets && npm ci && npm run test
   ```
   Override `FRONTEND_COMMAND_TESTS` to match your project's setup.

5. **Backend build** — defined in `BACKEND_COMMAND_BUILD`:
   ```sh
   composer install --prefer-dist --no-progress --no-interaction --optimize-autoloader --no-dev
   ```

6. **Frontend build** — defined in `FRONTEND_COMMAND_BUILD`:
   ```sh
   cd assets && npm ci && npm run production
   ```
   If you change the build command, also update `FRONTEND_FOLDER_BUILD_1`.

7. **Deploy** — add `SSH_PRIVATE_KEY` to your GitLab project CI/CD settings as a masked variable.
   Encode it with: `cat privatekey | base64 -w0` (macOS: `cat privatekey | base64 -b0`).

   If your SSH setup differs from the default, set `DEPLOY_SSH_SETUP` to replace the entire SSH
   setup block. Example using a GitLab **File** variable:

   ```yaml
   variables:
     DEPLOY_SSH_SETUP: |
       eval $(ssh-agent -s)
       ssh-add "$SSH_PRIVATE_KEY"
       mkdir -p ~/.ssh && chmod 700 ~/.ssh
       echo -e "Host *\n\tStrictHostKeyChecking no\n\tUserKnownHostsFile /dev/null" >> ~/.ssh/config
   ```

8. Define your deployer configuration in `deploy.php`. Example:

   ```php
   <?php

   namespace Deployer;

   require_once(__DIR__ . '/vendor/autoload.php');

   new \SourceBroker\DeployerLoader\Loader([
     ['get' => 'sourcebroker/deployer-typo3-deploy-ci'],
   ]);

   task('deploy:writable')->disable();

   host('prod')
       ->setHostname('vm-dev.example.com')
       ->setRemoteUser('project1')
       ->set('bin/php', '/usr/bin/php8.4')
       ->set('public_urls', ['https://t3base13.example.com'])
       ->set('deploy_path', '~/t3base13.example.com/prod');

   host('preprod')
       ->setHostname('vm-dev.example.com')
       ->setRemoteUser('project1')
       ->set('bin/php', '/usr/bin/php8.4')
       ->set('public_urls', ['https://preprod-t3base13.example.com'])
       ->set('deploy_path', '~/t3base13.example.com/preprod');

   host('staging')
       ->setHostname('vm-dev.example.com')
       ->setRemoteUser('project1')
       ->set('bin/php', '/usr/bin/php8.4')
       ->set('public_urls', ['https://staging-t3base13.example.com'])
       ->set('deploy_path', '~/t3base13.example.com/staging');
   ```

9. Push the changes to your repository and see the pipeline at your project.
