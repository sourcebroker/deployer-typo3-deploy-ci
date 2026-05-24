# Example Configs

## Few separate assets with separate build commands

```yaml
FRONTEND_COMMAND_BUILD: >
  cd ${CI_PROJECT_DIR}/assets-1 && npm ci && npm run production;
  cd ${CI_PROJECT_DIR}/assets-2 && npm ci && npm run production;
FRONTEND_FOLDER_BUILD_1: public/assets-1/frontend/build
FRONTEND_FOLDER_BUILD_2: public/assets-2/frontend/build
```

## How to disable the frontend or backend part?

Example when you would like to have only backend:

```yaml
deploy:
  needs:
    - job: test-backend
    - job: build-backend

test-frontend:
  rules:
    - when: never

build-frontend:
  rules:
    - when: never
```

## Few separate assets with separate build commands and different node versions

```yaml
build-frontend-assets3:
  stage: build
  image:
    name: thecodingmachine/php:${PHP}-v4-cli-node18
  retry:
    max: 2
  script:
    - bash -c "cd vendor/my_company/my_ext/Resources/Private/Assets && npm ci && npm run production"
  artifacts:
    paths:
      - public/assets/frontend/build-assets3
    expire_in: 15 min
  rules:
    - if: $CI_COMMIT_BRANCH && $CI_COMMIT_BRANCH =~ $DEPLOY_TRIGGER_BY_CI_COMMIT_BRANCH
    - if: $CI_COMMIT_TAG && $CI_COMMIT_TAG =~ $DEPLOY_TRIGGER_BY_CI_COMMIT_TAG

deploy:
  needs:
    - job: test-frontend
    - job: test-backend
    - job: build-frontend
    - job: build-backend
    - job: build-frontend-assets3
```
