WorkBC Career Discovery Quizzes
===============================

[![Lifecycle:Maturing](https://img.shields.io/badge/Lifecycle-Maturing-007EC6)](https://github.com/bcgov/workbc-cc)

Career Discovery Quizzes, a subsite of [WorkBC.ca](https://www.workbc.ca).
# Initial setup
- Copy `.env.example` to `.env`
- Create private directory: `mkdir src/private`
- Start the environment: `docker-compose up`
- Adjust folder permissions:
  - `docker-compose exec php sudo chown www-data /var/www/html/private`
  - `docker-compose exec php sudo chown www-data /var/www/html/config/sync`
  - `docker-compose exec php sudo mkdir /var/www/html/web/sites/default/files`
  - `docker-compose exec php sudo chown www-data /var/www/html/web/sites/default/files`
- Import the data dumps:
  - `gunzip -k -c src/scripts/workbc-cc.sql.gz | docker-compose exec -T postgres psql -U workbc workbc-cc-refactor`
  - Restore the SSOT data dump as per the [`workbc-ssot` README](https://github.com/bcgov/workbc-ssot?tab=readme-ov-file#development)
- Edit your `hosts` file to add the following line:
```
127.0.0.1       workbc-cc.docker.localhost
```
- Run the sync script: `docker-compose exec php scripts/sync.sh`
- Open http://workbc-cc.docker.localhost:8000/ to view the site and login as `aest-local` (obtain the password from your admin or change the password using `drush upwd aest-local 'password'`)
- Open http://localhost:8080/ to view the SSoT API

# Updating local dev environment from a deployment stage
You may want to get the latest data from a deployment stage (DEV, TEST or PROD). In that case, follow these steps:
- Take a full database dump: `docker-compose exec -T postgres pg_dump --clean --username workbc workbc-cc-refactor | gzip > src/scripts/workbc-cc.sql.gz`
- Reset your database `docker-compose exec -T postgres psql -U workbc workbc-cc-refactor < src/scripts/workbc-cc.reset.sql`
- Download a fresh dump from your deployment stage via Backup/Migrate module at `/admin/config/development/backup_migrate` and select Backup Source **Default Drupal Database**
- Restore the fresh dump on your local at http://workbc.docker.localhost:8000/admin/config/development/backup_migrate/restore
- Repeat the above two steps for Backup Source **Public Files Directory** in case you also need the latest files
- Run the sync script: `docker-compose exec php scripts/sync.sh`

# Compiling Theme
In order to compile the theme (workbc_cdq) you must:
- Install SASS (https://sass-lang.com/install)
- run `sass scss/style.scss css/style.css && sass scss/ck5style.scss css/ck5style.css` (or `sass scss/style.scss css/style.css; sass scss/ck5style.scss css/ck5style.css` in Windows Powershell) in the theme folder `/src/web/themes/custom/workbc_cdq`

# Debugging and troubleshooting

## Xdebug on VS Code
- The Docker Compose file is ready for Xdebug on Windows/Mac/Linux
- Add a `.vscode/launch.json` file with the following:
```json
{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "Listen for Xdebug on workbc-cc",
      "type": "php",
      "request": "launch",
      "port": 9003,
      "pathMappings": {
        "/var/www/html/": "/path/to/your/workbc-cc/src"
      }
    }
  ]
}
```
- Click **Run** > **Start Debugging** on VS Code
- Set some breakpoints in your Drupal code
- Navigate to the app in your browser to trigger the breakpoints
