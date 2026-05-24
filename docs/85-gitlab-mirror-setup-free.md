# Setting up a GitLab mirror on a self-hosted free GitLab instance

GitLab's built-in pull mirroring is a **Premium/Ultimate** feature. On the **free** tier you need
to implement mirroring yourself using two separate projects.

## Why two projects?

`git push --mirror` overwrites everything in the target repository — including any `.gitlab-ci.yml`
you might add there. The pipeline that does the mirroring must therefore live in a **separate**
project, not in the mirror repository itself.

## How it works

- **Mirror project** (`my-group/deployer-typo3-deploy-ci`) — receives the mirrored content.
  No `.gitlab-ci.yml`. Your other projects include CI config from here via `project:`.
- **Pipeline project** (`my-group/deployer-typo3-deploy-ci-pipeline`) — contains only a
  `.gitlab-ci.yml` that clones from GitHub and force-pushes into the mirror project.

## Steps

### 1. Create the mirror project

Create a new **empty** project on your GitLab instance: `my-group/deployer-typo3-deploy-ci`.
Do not initialize it with a README or any files.

### 2. Allow force push in the mirror project

In the **mirror project** go to **Settings → Repository → Protected branches**, find `main` and
set **Allowed to force push: ON**. Then go to **Protected tags** and remove tag protection
entirely — `git push --mirror` must be able to create and overwrite all tags.

### 3. Create an access token for the mirror project

In the **mirror project** go to **Settings → Access Tokens** and create a project access token
with the `write_repository` scope and **Developer** role. Copy the token value.

### 4. Create the pipeline project

Create a second new empty project: `my-group/deployer-typo3-deploy-ci-pipeline`.

### 5. Add the token as a CI/CD variable in the pipeline project

In the **pipeline project** go to **Settings → CI/CD → Variables** and add:

- `MIRROR_TOKEN` — the access token from step 2 (mask it)

### 6. Add a `.gitlab-ci.yml` to the pipeline project

```yaml
mirror:
  image:
    name: alpine/git
    entrypoint: [""]
  variables:
    MIRROR_TARGET: "my-group/deployer-typo3-deploy-ci"
  rules:
    - if: $CI_PIPELINE_SOURCE == "schedule"
    - if: $CI_PIPELINE_SOURCE == "web"
  script:
    - git clone --mirror https://github.com/sourcebroker/deployer-typo3-deploy-ci.git repo
    - cd repo
    - git push --mirror "https://oauth2:${MIRROR_TOKEN}@${CI_SERVER_HOST}/${MIRROR_TARGET}.git"
```

### 7. Run the mirror

> **Security note:** Mirroring means you are trusting external code. If the upstream GitHub
> repository is ever compromised, the mirror could pull malicious code into your instance —
> where it would be executed by every project that includes it. Use the pipeline below which
> splits fetch and push into two steps: fetch runs on a schedule, push requires a human to
> review the job log and click **play** manually.

Split the job into a `fetch` step (runs on schedule) and a `push` step (requires manual
approval), so a human reviews what will change before anything lands in the mirror:

```yaml
variables:
  MIRROR_SOURCE: "https://github.com/sourcebroker/deployer-typo3-deploy-ci.git"
  MIRROR_TARGET: "my-group/deployer-typo3-deploy-ci"

fetch:
  image:
    name: alpine/git
    entrypoint: [""]
  rules:
    - if: $CI_PIPELINE_SOURCE == "schedule"
  script:
    - git clone --mirror $MIRROR_SOURCE upstream
    - git clone --mirror "https://oauth2:${MIRROR_TOKEN}@${CI_SERVER_HOST}/${MIRROR_TARGET}.git" current
    - echo "=== New commits in upstream not yet in mirror ==="
    - git -C upstream log --oneline $(git -C current rev-parse HEAD)..HEAD || echo "Cannot compare — mirror may be empty"
    - echo "=== Tags that would be force-updated ==="
    - for tag in $(git -C upstream tag); do upstream_sha=$(git -C upstream rev-parse "$tag" 2>/dev/null); current_sha=$(git -C current rev-parse "$tag" 2>/dev/null); if [ -n "$current_sha" ] && [ "$upstream_sha" != "$current_sha" ]; then echo "CHANGED $tag $current_sha -> $upstream_sha"; fi; done
    - echo "=== Done. If no CHANGED lines above — all existing tags are intact, safe to push ==="
  artifacts:
    paths:
      - upstream/

push:
  image:
    name: alpine/git
    entrypoint: [""]
  needs: [fetch]
  when: manual
  script:
    - cd upstream
    - git push --mirror "https://oauth2:${MIRROR_TOKEN}@${CI_SERVER_HOST}/${MIRROR_TARGET}.git"
```

Schedule a run every Monday morning. When the schedule fires, open the **pipeline project** →
**CI/CD → Pipelines** → click the latest pipeline → click the `fetch` job to open its log.
Pay special attention to the **"Tags that would be force-updated"** section. A tag that already
exists in your mirror should never change its SHA. If any existing tag shows a different SHA it
means the upstream repository has rewritten history on that tag — a strong signal of a
compromise. Only click **play** on the `push` job if no existing tags were changed.

> **Why not use AI to review the diff automatically?** An attacker who controls the upstream
> repository can embed prompt injection text inside YAML comments or shell scripts — instructing
> the AI model to approve the changes. AI review of untrusted content gives a false sense of
> security and should not be used as a gate here.

### 8. Use the mirror in your projects

```yaml
include:
  - project: 'my-group/deployer-typo3-deploy-ci'
    ref: '2.0.0'
    file: '/ci/provider/gitlab/main.yaml'
```

Make sure the `ref` matches a tag that exists in your mirror. After bumping the version in your
project, run the mirror pipeline manually first to pull the new tag before the pipeline tries to
resolve it.
