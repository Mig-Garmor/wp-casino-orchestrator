# wp-casino-orchestrator

Local and remote WordPress orchestration repo for a casino affiliate demo project.

This repository does **not** own the WordPress theme or plugin code directly. It mounts separate Git repositories into a running WordPress container.

The orchestrator owns:

- Docker configuration
- WordPress runtime
- MySQL runtime
- WP-CLI helper commands
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

## Current submodule setup

This repository already has the theme and plugin configured as Git submodules.

The `.gitmodules` file contains:

```txt
[submodule "repos/wp-casino-theme"]
	path = repos/wp-casino-theme
	url = git@github.com:Mig-Garmor/wp-casino-theme.git

[submodule "repos/wp-casino-plugin"]
	path = repos/wp-casino-plugin
	url = git@github.com:Mig-Garmor/wp-casino-plugin.git
```

That means you do **not** need to run `git submodule add` again.

You only need to initialize and update the existing submodules after cloning the orchestrator.

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

Check Git:

```bash
git --version
```

Check GitHub SSH access:

```bash
ssh -T git@github.com
```

If GitHub SSH access fails, fix that before continuing. Submodule cloning uses SSH and will fail without access.

## Project structure

Expected structure after submodules are initialized:

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

## Local development setup

### 1. Clone the orchestrator with submodules

Use this when cloning the project for the first time:

```bash
git clone --recurse-submodules git@github.com:Mig-Garmor/wp-casino-orchestrator.git
cd wp-casino-orchestrator
```

This clones:

```txt
wp-casino-orchestrator
repos/wp-casino-theme
repos/wp-casino-plugin
```

### 2. If you already cloned without submodules

Run this from the root of `wp-casino-orchestrator`:

```bash
git submodule update --init --recursive
```

Or use the helper command:

```bash
./scripts/init-submodules.sh
```

### 3. Create the local environment file

```bash
cp .env.example .env
```

### 4. Make scripts executable

```bash
chmod +x scripts/*.sh
```

### 5. Start the local WordPress environment

```bash
./scripts/bootstrap-local.sh
```

This command should:

```txt
1. Initialize submodules if needed
2. Start the MySQL container
3. Start the WordPress container
4. Install WordPress if it is not already installed
5. Activate the theme
6. Activate the plugin
7. Flush rewrite rules
```

### 6. Open WordPress locally

Frontend:

```txt
http://localhost:8080
```

Admin:

```txt
http://localhost:8080/wp-admin
```

Default local credentials:

```txt
Username: admin
Password: admin
```

These credentials are for local development only.

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

For checking active themes:

```bash
./scripts/wp-local.sh theme list
```

For checking active plugins:

```bash
./scripts/wp-local.sh plugin list
```

For activating the theme manually:

```bash
./scripts/wp-local.sh theme activate wp-casino-theme
```

For activating the plugin manually:

```bash
./scripts/wp-local.sh plugin activate wp-casino-plugin
```

For deactivating and reactivating the plugin:

```bash
./scripts/wp-local.sh plugin deactivate wp-casino-plugin
./scripts/wp-local.sh plugin activate wp-casino-plugin
```

## Useful local commands

Start local containers:

```bash
docker compose -f docker-compose.local.yml up -d
```

Stop local containers:

```bash
docker compose -f docker-compose.local.yml down
```

Stop local containers and delete local volumes:

```bash
docker compose -f docker-compose.local.yml down -v
```

Use this when you want to fully reset the local WordPress database and uploads.

Rebuild the local environment after a reset:

```bash
./scripts/bootstrap-local.sh
```

View all local container logs:

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

## Working with submodules locally

The theme and plugin are real Git repositories inside `repos/`.

### Updating the theme

Go into the theme repo:

```bash
cd repos/wp-casino-theme
```

Make sure you are on the right branch:

```bash
git checkout main
```

Make your changes, then commit and push them:

```bash
git add .
git commit -m "Update casino theme"
git push origin main
```

Return to the orchestrator:

```bash
cd ../..
```

The orchestrator now sees that the submodule points to a different commit.

Check status:

```bash
git status
```

Commit the updated submodule pointer:

```bash
git add repos/wp-casino-theme
git commit -m "Update theme submodule pointer"
git push origin main
```

### Updating the plugin

Go into the plugin repo:

```bash
cd repos/wp-casino-plugin
```

Make sure you are on the right branch:

```bash
git checkout main
```

Make your changes, then commit and push them:

```bash
git add .
git commit -m "Update casino plugin"
git push origin main
```

Return to the orchestrator:

```bash
cd ../..
```

Check status:

```bash
git status
```

Commit the updated submodule pointer:

```bash
git add repos/wp-casino-plugin
git commit -m "Update plugin submodule pointer"
git push origin main
```

The important rule is:

```txt
Theme/plugin code is committed inside the theme/plugin repositories.
The orchestrator only commits the updated submodule pointer.
```

## Updating submodules to latest remote commits

Run:

```bash
./scripts/update-submodules.sh
```

Then review the result:

```bash
git status
```

If the submodule pointers changed, commit them:

```bash
git add repos/wp-casino-theme repos/wp-casino-plugin
git commit -m "Update theme and plugin submodules"
git push origin main
```

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

### 1. Create the deployment directory

```bash
sudo mkdir -p /srv/wp-casino-orchestrator
sudo chown -R $USER:$USER /srv/wp-casino-orchestrator
cd /srv/wp-casino-orchestrator
```

### 2. Clone the orchestrator with submodules

```bash
git clone --recurse-submodules git@github.com:Mig-Garmor/wp-casino-orchestrator.git .
```

If the repo was already cloned without submodules, run:

```bash
git submodule update --init --recursive
```

Or use the helper command:

```bash
./scripts/init-submodules.sh
```

### 3. Create the remote environment file

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

### 4. Make scripts executable

```bash
chmod +x scripts/*.sh
```

### 5. Start the remote WordPress environment

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

## Remote deployment workflow

After changes have been pushed to the theme/plugin repositories and the orchestrator submodule pointers have been updated, deploy on the server.

Go to the remote project directory:

```bash
cd /srv/wp-casino-orchestrator
```

Pull the latest orchestrator changes:

```bash
git pull origin main
```

Update the submodules to the commits stored in the orchestrator:

```bash
git submodule update --init --recursive
```

Restart the remote containers:

```bash
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

## Common problems

### Submodule folder is empty

Run:

```bash
git submodule update --init --recursive
```

Or run:

```bash
./scripts/init-submodules.sh
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

Flush rewrite rules locally:

```bash
./scripts/wp-local.sh rewrite flush
```

Flush rewrite rules remotely:

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

## What this repository should not contain

Do not commit:

- WordPress core files
- Database dumps by default
- Uploaded media files
- `.env`
- Theme source code directly outside the submodule
- Plugin source code directly outside the submodule

The orchestrator should stay focused on runtime orchestration.

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
