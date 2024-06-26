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
- Import the data dump:
  - `gunzip -k -c src/scripts/workbc-cc.sql.gz | docker-compose exec -T postgres psql -U drupal workbc-cc`
- Edit your `hosts` file to add the following line:
```
127.0.0.1       workbc-cc.docker.localhost
```
- Run the sync script: `docker-compose exec php scripts/sync.sh`
- Open http://workbc-cc.docker.localhost:8000/ to view the site and login as `aest-local` (obtain the password from your admin or change the password using `drush upwd aest-local 'password'`)

# Compile CSS
- Ensure you are running `node v11.15` (e.g. using [nvm](https://github.com/nvm-sh/nvm))
- Change folder to `./src/web/themes/custom/bcgov_career`
- Run `npm i && npm run sass`

# Architecture
The solution architecture is as below.
![Architecture](https://user-images.githubusercontent.com/79226696/177882962-f257ef30-6751-4873-a6b3-e0cfffbd0df8.png)

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
- Click Run > Start Debugging on VS Code
- Set some breakpoints in your Drupal code
- Navigate to the app in your browser to trigger the breakpoints
