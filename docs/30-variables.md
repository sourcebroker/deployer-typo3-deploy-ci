# Variables

## General (`ci/provider/gitlab/config/100-variables.yaml`)

- `PHP` PHP version for backend test and build.
- `NODE` Node.js version for frontend test and build.
- `TEST_TRIGGER_BY_CI_COMMIT_BRANCH` Regexp for branches that trigger the pipeline for test only.
- `TEST_TRIGGER_BY_CI_COMMIT_TAG` Regexp for tags that trigger the pipeline for test only.
- `DEPLOY_TRIGGER_BY_CI_COMMIT_TAG` Regexp for tags that trigger deployment.

## Backend (`ci/provider/gitlab/config/110-variables-backend.yaml`)

- `BACKEND_COMMAND_TEST` Command to run backend tests.
- `BACKEND_COMMAND_BUILD` Command to build the backend.
- `BACKEND_IMAGE` Docker image for the backend.
- `BACKEND_FOLDER_BUILD_*` Paths for backend build artifacts.

## Frontend (`ci/provider/gitlab/config/120-variables-frontend.yaml`)

- `FRONTEND_COMMAND_TESTS` Command to run frontend tests.
- `FRONTEND_COMMAND_BUILD` Command to build the frontend.
- `FRONTEND_IMAGE` Docker image for the frontend.
- `FRONTEND_FOLDER_BUILD_*` Paths for frontend build artifacts.

## GitLab (`ci/provider/gitlab/config/130-variables-gitlab.yaml`)

- `FF_USE_FASTZIP` Enable fast zip for artifacts.
- `ARTIFACT_COMPRESSION_LEVEL` Compression level for artifacts.
- `CACHE_COMPRESSION_LEVEL` Compression level for cache.
- `TRANSFER_METER_FREQUENCY` Frequency of transfer meter updates.
- `DOCKER_DRIVER` Docker driver to use.
- `DOCKER_BUILDKIT` Enable Docker BuildKit.
- `BUILDKIT_INLINE_CACHE` Enable inline cache for BuildKit.
- `COMPOSE_DOCKER_CLI_BUILD` Enable Docker CLI build for Compose.

## Deployer

- `DEPLOYER_SELECTOR_FOR_BRANCH` Mapping of GitLab branch to Deployer selector. Collection of
  `branch:deployer_selector` pairs separated by commas. Example: `develop:staging,main:preprod`.
- `DEPLOYER_SELECTOR_FOR_TAG` Deployer selector used when a tag is pushed. Example: `prod`.
- `DEPLOYER_OPTIONS` Additional options for Deployer.

## Deploy job hooks (`ci/provider/gitlab/config/600-deploy.yaml`)

The `deploy` job exposes two complementary hook mechanisms at each key point.

### `!reference` hooks (recommended for multi-step YAML blocks)

Override the hidden job in your `.gitlab-ci.yml` to inject or replace steps:

| Hidden job                    | Type        | Position                 |
|-------------------------------|-------------|--------------------------|
| `.deploy_before_script_start` | additive    | before rsync install     |
| `.deploy_rsync_install`       | replacement | rsync installation logic |
| `.deploy_ssh_setup`           | replacement | SSH agent setup          |
| `.deploy_before_script_end`   | additive    | after SSH setup          |
| `.deploy_script_start`        | additive    | before `dep deploy`      |
| `.deploy_script`              | replacement | main deployer invocation |
| `.deploy_script_end`          | additive    | after `dep deploy`       |

**Additive** hooks have `script: []` by default (no-op). **Replacement** hooks contain the default implementation —
override to replace entirely.

Example — connect to WireGuard VPN before deploy:

```yaml
.deploy_before_script_start:
  script:
    - apk add wireguard-tools iproute2 --update
    - *configureWireGuard
```

Example — replace SSH setup entirely (e.g. certificate-based auth):

```yaml
.deploy_ssh_setup:
  script:
    - eval $(ssh-agent -s)
    - echo "$SSH_CERT" > ~/.ssh/id_rsa && chmod 600 ~/.ssh/id_rsa
    - mkdir -p ~/.ssh && chmod 700 ~/.ssh
    - echo -e "Host *\n\tStrictHostKeyChecking no\n\tUserKnownHostsFile /dev/null" >> ~/.ssh/config
```

### Variable hooks (for simple one-liners via GitLab UI)

Set these CI/CD variables to inject a shell command without touching `.gitlab-ci.yml`:

- `DEPLOY_BEFORE_SCRIPT_START` — executed **before** rsync install.
- `DEPLOY_BEFORE_SCRIPT_END` — executed **after** SSH setup.
- `DEPLOY_SCRIPT_START` — executed **before** `dep deploy`.
- `DEPLOY_SCRIPT_END` — executed **after** `dep deploy` completes.
- `DEPLOY_RSYNC_SKIP` — set to any non-empty value to skip rsync installation.

Example — send a Slack notification after deploy:

```yaml
variables:
  DEPLOY_SCRIPT_END: 'curl -X POST -d "payload={\"text\":\"Deployed!\"}" $SLACK_WEBHOOK_URL'
```

Or with `!reference` (preferred for multi-line or complex commands):

```yaml
.deploy_script_end:
  script:
    - curl -X POST -d "payload={\"text\":\"Deployed!\"}" $SLACK_WEBHOOK_URL
```
