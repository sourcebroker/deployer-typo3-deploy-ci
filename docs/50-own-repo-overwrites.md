# Own Repo for Overwrites

You may be interested in creating your own repo with values for overwriting variables of
`sourcebroker/deployer-typo3-deploy-ci`.

Good candidates for overwrites are `DEPLOY_TRIGGER_BY_CI_COMMIT_BRANCH`,
`DEPLOYER_SELECTOR_FOR_BRANCH`, `DEPLOYER_SELECTOR_FOR_TAG`, `FRONTEND_COMMAND_TESTS`,
`FRONTEND_COMMAND_BUILD`.

Then add your overrides as a second `project:` include:

```yaml
include:
  - project: 'my-group/deployer-typo3-deploy-ci'
    ref: '2.0.0'
    file: '/ci/provider/gitlab/main.yaml'
  - project: 'my-group/my-company-deployer-overrides'
    ref: '1.0.0'
    file: '/ci/provider/gitlab/overrides.yaml'
```
