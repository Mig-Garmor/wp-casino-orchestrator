# wp-casino-orchestrator

Local and remote WordPress orchestration repo for a casino affiliate demo project.

This repository does not own the WordPress theme or plugin code directly. It mounts separate Git repositories into a running WordPress container.

## Repositories

- `wp-casino-orchestrator`
- `wp-casino-theme`
- `wp-casino-plugin`

Mounted paths:

```txt
repos/wp-casino-theme
  -> /var/www/html/wp-content/themes/wp-casino-theme

repos/wp-casino-plugin
  -> /var/www/html/wp-content/plugins/wp-casino-plugin
```
