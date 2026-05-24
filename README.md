deployer-typo3-deploy-ci
========================

[![Packagist](http://img.shields.io/packagist/v/sourcebroker/deployer-typo3-deploy-ci.svg?style=flat)](https://packagist.org/packages/sourcebroker/deployer-typo3-deploy-ci) [![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat)](https://packagist.org/packages/sourcebroker/deployer-typo3-deploy-ci)

## Quick Introduction

A ready-to-use GitLab CI/CD pipeline for TYPO3 CMS projects. It covers the full deployment
cycle — backend and frontend testing, building, and deploying via [Deployer](https://deployer.org/).

The goal is a near-empty `gitlab-ci.yml` in each project, with all pipeline logic living here and
shared across every project that includes it. With dozens of projects this is the only approach
that stays maintainable.

**How to use it:**

[Mirror this repository](docs/85-gitlab-mirror-setup-free.md) to your own GitLab group (whether
on gitlab.com or self-hosted) and include it via GitLab's native `project:` syntax — your
project's `gitlab-ci.yml` then only needs the include and project-specific variable overrides:

```yaml
include:
  - project: 'my-group/deployer-typo3-deploy-ci'
    ref: '2.0.0'
    file: '/ci/provider/gitlab/main.yaml'
```

If you manage multiple projects for the same company, add a second intermediate repo with
company-wide defaults:

```yaml
include:
  - project: 'my-group/deployer-typo3-deploy-ci'
    ref: '2.0.0'
    file: '/ci/provider/gitlab/main.yaml'
  - project: 'my-group/my-company-ci-overrides'
    ref: '1.0.0'
    file: '/ci/provider/gitlab/overrides.yaml'
```

## Documentation

- [Installation](docs/10-installation.md)
- [Stages](docs/20-stages.md)
- [Variables](docs/30-variables.md)
- [Deployer Tasks](docs/40-deployer-tasks.md)
- [Own repo for overwrites](docs/50-own-repo-overwrites.md)
- [Example configs](docs/60-example-configs.md)
- [Why mirror](docs/80-why-mirror.md)
- [Setting up a GitLab mirror](docs/85-gitlab-mirror-setup-free.md)
