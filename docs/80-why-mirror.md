# Why mirror this repository

This repository contributes two distinct things to your project: **CI YAML files** and a
**Composer package**. Their security characteristics are different, and understanding the
difference explains why mirroring matters — and where it matters most.

## CI YAML files

When your `.gitlab-ci.yml` includes CI configuration from a remote project:

```yaml
include:
  - project: 'sourcebroker/deployer-typo3-deploy-ci'
    ref: '2.0.0'
    file: '/ci/provider/gitlab/main.yaml'
```

GitLab fetches and executes that configuration as if you had written it yourself. The included
YAML can:

- Load **any Docker image**, including malicious ones
- Execute **arbitrary shell commands** in your CI runner
- Read and exfiltrate **all CI/CD variables** — including `SSH_PRIVATE_KEY` and any other secrets
- Modify what gets built and deployed to your servers

If anyone gains write access to the source repository and pushes a malicious commit — even onto
an existing tag — that code runs in your pipeline the next time it triggers. **There is no hash
verification for GitLab CI includes.** The `ref:` is resolved at pipeline time against whatever
the repository currently contains.

**This is why mirroring is critical for CI YAML files.** Once you mirror the repository to your
own GitLab instance, you control exactly when and whether upstream changes are adopted. An
attacker compromising the upstream repository cannot affect your pipelines without also
compromising your mirror.

## Composer package

The PHP deployer tasks in this package also execute code — they run on your CI runner and
connect via SSH to your target servers. However, Composer provides a meaningful security
guarantee that GitLab CI includes do not.

`composer.lock` stores a **content hash** for every installed package. If an attacker
force-pushes a malicious commit onto an existing tag, the content hash changes. When you next
run `composer install`, Composer computes the hash of the downloaded archive and compares it
against `composer.lock`. If they differ, the install fails with an error. A compromised version
of an existing tag therefore **cannot be silently installed** as long as you commit and use
`composer.lock`.

A brand-new malicious release (a new tag) also cannot be picked up automatically — it would
require you to explicitly run `composer update`, which you control.

**Mirroring for the Composer package is therefore optional.** It is useful for keeping the
Composer version in sync with the CI `ref:` version (they are configured independently and can
drift), but it is not a security requirement in the same way it is for CI YAML files.

## Summary

| | CI YAML files | Composer package |
|---|---|---|
| Attack via force-pushed tag | Immediately effective — no hash check | Blocked — `composer.lock` hash mismatch |
| Attack via new release | Effective if `ref:` uses a branch or `latest` | Only if you run `composer update` |
| Primary protection | **Mirror to your own GitLab** | **Commit and use `composer.lock`** |
| Mirroring required? | Yes | No — but useful for version consistency |
