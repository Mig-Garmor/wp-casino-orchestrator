# wp-casino-orchestrator

Local and remote WordPress orchestration repo for a casino affiliate demo project.

This repository does **not** own the WordPress theme or plugin code directly. It mounts separate Git repositories into a running WordPress container.

The orchestrator owns:

- Docker configuration
- WordPress runtime
- MySQL runtime
- WP-CLI helper scripts
- Local development setup
- Remote deployment setup
- Submodule references to the theme and plugin repositories

The theme and plugin live in their own repositories.

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

## Architecture

```txt
Browser
  -> WordPress container
    -> wp-content/themes/wp-casino-theme
       mounted from repos/wp-casino-theme

    -> wp-content/plugins/wp-casino-plugin
       mounted from repos/wp-casino-plugin

    -> MySQL container
```

Local development and remote deployment use the same core idea:

```txt
Git repositories on the host machine
  -> mounted into the WordPress container
```

Docker does not mount GitHub repositories directly. It mounts local folders. Those folders are Git repositories managed by submodules.

---

## Requirements

Install these before running the project:

- Docker Desktop
- Docker Compose v2
- Git
- SSH access to GitHub configured locally

Check Docker:

```bash
docker --version
docker compose version
```

Check GitHub SSH access:

```bash
ssh -T git@github.com
```

If SSH is not configured, Git submodule cloning will fail.

---

## Important note about submodules

The theme and plugin repositories must not be completely empty.

If a repository has no initial commit, adding it as a submodule can fail with an error like:

```txt
fatal: You are on a branch yet to be born
fatal: unable to checkout submodule
```

Before adding the theme/plugin repositories as submodules, make sure each one has at least one commit.

Example for each empty repo:

```bash
echo "# wp-casino-theme" > README.md
git add README.md
git commit -m "Initial commit"
git push origin main
```

Do the same for `wp-casino-plugin`.

---

## Project structure

Expected structure:

```txt
wp-casino-orchestrator/
  .env.example
  .gitignore
  .gitmodules
  README.md

  docker-compose.local.yml
  docker-compose.remote.yml

  repos/
    wp-casino-theme/
    wp-casino-plugin/

  scripts/
    bootstrap-local.sh
    bootstrap-remote.sh
    init-submodules.sh
    update-submodules.sh
    wp-local.sh
    wp-remote.sh
```

---

## Environment setup

Create a local `.env` file:

```bash
cp .env.example .env
```

Example `.env.example`:

```env
# Database
MYSQL_DATABASE=wordpress
MYSQL_USER=wordpress
MYSQL_PASSWORD=wordpress
MYSQL_ROOT_PASSWORD=root

# WordPress local
LOCAL_WORDPRESS_PORT=8080
LOCAL_WORDPRESS_URL=http://localhost:8080
LOCAL_WORDPRESS_TITLE=Casino Affiliate Local
LOCAL_WORDPRESS_ADMIN_USER=admin
LOCAL_WORDPRESS_ADMIN_PASSWORD=admin
LOCAL_WORDPRESS_ADMIN_EMAIL=admin@example.com

# WordPress remote
REMOTE_WORDPRESS_PORT=80
REMOTE_WORDPRESS_URL=https://example.com
REMOTE_WORDPRESS_TITLE=Casino Affiliate
REMOTE_WORDPRESS_ADMIN_USER=admin
REMOTE_WORDPRESS_ADMIN_PASSWORD=change-this-password
REMOTE_WORDPRESS_ADMIN_EMAIL=admin@example.com

# Mounted repo names
WP_THEME_SLUG=wp-casino-theme
WP_PLUGIN_SLUG=wp-casino-plugin
```

Do not commit `.env`.

---

## First-time orchestrator setup

Use this only when setting up the orchestrator repository for the first time.

From inside `wp-casino-orchestrator`:

```bash
mkdir -p repos
mkdir -p scripts
```

Add the theme repository as a submodule:

```bash
git submodule add git@github.com:Mig-Garmor/wp-casino-theme.git repos/wp-casino-theme
```

Add the plugin repository as a submodule:

```bash
git submodule add git@github.com:Mig-Garmor/wp-casino-plugin.git repos/wp-casino-plugin
```

Commit the submodule references:

```bash
git add .gitmodules repos/wp-casino-theme repos/wp-casino-plugin
git commit -m "Add theme and plugin submodules"
```

If your GitHub owner/org is different, replace `Mig-Garmor` with the correct GitHub account or organization.

---

## Local development setup

Clone the orchestrator with submodules:

```bash
git clone --recurse-submodules git@github.com:Mig-Garmor/wp-casino-orchestrator.git
cd wp-casino-orchestrator
```

If you already cloned without submodules, run:

```bash
git submodule update --init --recursive
```

Create the environment file:

```bash
cp .env.example .env
```

Make scripts executable:

```bash
chmod +x scripts/*.sh
```

Start the local WordPress environment:

```bash
./scripts/bootstrap-local.sh
```

Open WordPress:

```txt
http://localhost:8080
```

Open WordPress admin:

```txt
http://localhost:8080/wp-admin
```

Default local credentials:

```txt
Username: admin
Password: admin
```

These credentials are for local development only.

---

## Local development workflow

Edit theme files here:

```txt
repos/wp-casino-theme
```

Edit plugin files here:

```txt
repos/wp-casino-plugin
```

Because these folders are mounted into the WordPress container, changes are visible locally after refreshing the browser.

For PHP/template changes:

```txt
Save file -> refresh browser
```

For custom post type or rewrite changes:

```bash
./scripts/wp-local.sh rewrite flush
```

For plugin activation changes:

```bash
./scripts/wp-local.sh plugin deactivate wp-casino-plugin
./scripts/wp-local.sh plugin activate wp-casino-plugin
```

For checking active themes and plugins:

```bash
./scripts/wp-local.sh theme list
./scripts/wp-local.sh plugin list
```

---

## Useful local commands

Start local containers:

```bash
docker compose -f docker-compose.local.yml up -d
```

Stop local containers:

```bash
docker compose -f docker-compose.local.yml down
```

Stop and delete local volumes:

```bash
docker compose -f docker-compose.local.yml down -v
```

Use this when you want to fully reset the local WordPress database and uploads.

Rebuild local environment after reset:

```bash
./scripts/bootstrap-local.sh
```

View container logs:

```bash
docker compose -f docker-compose.local.yml logs -f
```

View WordPress logs only:

```bash
docker compose -f docker-compose.local.yml logs -f wordpress
```

Run WP-CLI locally:

```bash
./scripts/wp-local.sh plugin list
./scripts/wp-local.sh theme list
./scripts/wp-local.sh rewrite flush
```

---

## Working with submodules locally

The theme and plugin are real Git repositories inside `repos/`.

To work on the theme:

```bash
cd repos/wp-casino-theme
git checkout main
```

Make changes, then commit and push from inside the theme repo:

```bash
git add .
git commit -m "Update casino theme"
git push origin main
```

Then return to the orchestrator:

```bash
cd ../..
```

The orchestrator now sees that the submodule points to a new commit.

Commit the updated submodule pointer:

```bash
git add repos/wp-casino-theme
git commit -m "Update theme submodule pointer"
git push origin main
```

Same workflow for the plugin:

```bash
cd repos/wp-casino-plugin
git checkout main
git add .
git commit -m "Update casino plugin"
git push origin main

cd ../..
git add repos/wp-casino-plugin
git commit -m "Update plugin submodule pointer"
git push origin main
```

This matters because the orchestrator does not store the full theme/plugin code. It stores references to exact commits.

---

## Updating submodules

To initialize submodules:

```bash
./scripts/init-submodules.sh
```

To update submodules to their tracked remote branches:

```bash
./scripts/update-submodules.sh
```

Then review the result:

```bash
git status
```

Commit updated submodule pointers:

```bash
git add repos/wp-casino-theme repos/wp-casino-plugin
git commit -m "Update theme and plugin submodules"
git push origin main
```

---

## Remote deployment setup

Remote deployment assumes a VPS or server with:

- Docker installed
- Docker Compose installed
- Git installed
- SSH access to GitHub configured
- A domain pointing to the server

Example server directory:

```txt
/srv/wp-casino-orchestrator
```

Create the deployment directory:

```bash
sudo mkdir -p /srv/wp-casino-orchestrator
sudo chown -R $USER:$USER /srv/wp-casino-orchestrator
cd /srv/wp-casino-orchestrator
```

Clone the orchestrator with submodules:

```bash
git clone --recurse-submodules git@github.com:Mig-Garmor/wp-casino-orchestrator.git .
```

If already cloned without submodules:

```bash
git submodule update --init --recursive
```

Create the remote environment file:

```bash
cp .env.example .env
```

Edit `.env`:

```bash
nano .env
```

Set secure remote values:

```env
MYSQL_DATABASE=wordpress
MYSQL_USER=wordpress
MYSQL_PASSWORD=use-a-secure-password
MYSQL_ROOT_PASSWORD=use-a-secure-root-password

REMOTE_WORDPRESS_PORT=80
REMOTE_WORDPRESS_URL=https://your-domain.com
REMOTE_WORDPRESS_TITLE=Casino Affiliate
REMOTE_WORDPRESS_ADMIN_USER=admin
REMOTE_WORDPRESS_ADMIN_PASSWORD=use-a-secure-admin-password
REMOTE_WORDPRESS_ADMIN_EMAIL=your-email@example.com

WP_THEME_SLUG=wp-casino-theme
WP_PLUGIN_SLUG=wp-casino-plugin
```

Make scripts executable:

```bash
chmod +x scripts/*.sh
```

Run remote bootstrap:

```bash
./scripts/bootstrap-remote.sh
```

Open:

```txt
https://your-domain.com
```

Open admin:

```txt
https://your-domain.com/wp-admin
```

---

## Remote deployment workflow

After changes have been pushed to the theme/plugin repositories and the orchestrator submodule pointers have been updated, deploy on the server:

```bash
cd /srv/wp-casino-orchestrator
git pull origin main
git submodule update --init --recursive
docker compose -f docker-compose.remote.yml up -d
```

Flush rewrite rules if needed:

```bash
./scripts/wp-remote.sh rewrite flush
```

Check active theme:

```bash
./scripts/wp-remote.sh theme list
```

Check active plugins:

```bash
./scripts/wp-remote.sh plugin list
```

---

## Remote mount behavior

Local mounts are writable:

```txt
repos/wp-casino-theme
  -> writable inside WordPress container

repos/wp-casino-plugin
  -> writable inside WordPress container
```

Remote mounts should be read-only:

```txt
repos/wp-casino-theme
  -> read-only inside WordPress container

repos/wp-casino-plugin
  -> read-only inside WordPress container
```

The remote WordPress container should not edit Git-managed theme/plugin files.

Uploads remain writable:

```txt
wp-content/uploads
  -> writable Docker volume
```

Database data remains persistent:

```txt
MySQL data
  -> persistent Docker volume
```

---

## Expected Docker Compose behavior

Local compose file:

```txt
docker-compose.local.yml
```

Purpose:

- Run WordPress locally
- Run MySQL locally
- Mount theme and plugin repositories as writable folders
- Enable WordPress debug mode
- Expose WordPress on port `8080`

Remote compose file:

```txt
docker-compose.remote.yml
```

Purpose:

- Run WordPress remotely
- Run MySQL remotely
- Mount theme and plugin repositories as read-only folders
- Disable WordPress debug display
- Expose WordPress on port `80`

---

## Common problems

### Submodule folder is empty

Run:

```bash
git submodule update --init --recursive
```

### GitHub SSH permission denied

Check SSH access:

```bash
ssh -T git@github.com
```

If this fails, fix your SSH key setup before retrying.

### Theme does not appear in WordPress

Check that the theme has a valid `style.css` file with a WordPress theme header.

Minimum example:

```css
/*
Theme Name: WP Casino Theme
Version: 1.0.0
*/
```

Then run:

```bash
./scripts/wp-local.sh theme list
```

### Plugin does not appear in WordPress

Check that the plugin has a valid plugin header in its main PHP file.

Example:

```php
<?php
/**
 * Plugin Name: WP Casino Plugin
 * Description: Casino affiliate plugin.
 * Version: 1.0.0
 */
```

Then run:

```bash
./scripts/wp-local.sh plugin list
```

### Custom post type URLs return 404

Flush rewrite rules:

```bash
./scripts/wp-local.sh rewrite flush
```

Remote:

```bash
./scripts/wp-remote.sh rewrite flush
```

### Local WordPress needs full reset

Run:

```bash
docker compose -f docker-compose.local.yml down -v
./scripts/bootstrap-local.sh
```

This deletes the local database and uploads volume.

---

## What this repository should not contain

Do not commit:

- WordPress core files
- Database dumps by default
- Uploaded media files
- `.env`
- Theme source code directly outside the submodule
- Plugin source code directly outside the submodule

The orchestrator should stay focused on runtime orchestration.

---

## Mental model

```txt
wp-casino-orchestrator
  owns environment, Docker, scripts, and submodule references

wp-casino-theme
  owns presentation and template rendering

wp-casino-plugin
  owns WordPress functionality, custom post types, metadata, hooks, and business logic
```

Keep those boundaries clean.
