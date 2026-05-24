# Deployer Tasks

The configuration files are located in the `deployer/default` directory.

- **Cache Management**:
    - `typo3:cache:flush:pages` Flushes TYPO3 CMS page cache.
    - `typo3:cache:warmup:system` Warms up TYPO3 CMS system cache.

- **Extension Management**:
    - `typo3:extension:setup` Sets up TYPO3 CMS extensions.

- **Language Management**:
    - `typo3:language:update` Updates TYPO3 CMS languages.

- **Deployment**:
    - `deploy:upload_build` Uploads the build to the server.
    - `deploy-ci` Main deployment task for continuous integration.
