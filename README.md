# laravel-template-project project
[![Test integration on push](https://github.com/dannypas00/laravel-template/actions/workflows/test_integration_on_push.yaml/badge.svg)](https://github.com/dannypas00/laravel-template/actions/workflows/test_integration_on_push.yaml)  

## See this template in action at [laravel-template.dannypas00.com](https://laravel-template.dannypas00.com)
Default credentials are `test@example.com` and `test1234`.

## Installation
Run the command `make PROJECT_NAME=<your project name here>`, substituting for you project name.  
This make target will install all required dependencies and replace all templating with your chosen project name.  

## Running locally
To run the project locally, after installation simply run `docker compose up -d` to start the docker environment.

To change ports of services, change the [.env](.env) file, ports are imported from there.

## Local environment default urls
- Application: http://localhost:80
- Mailpit: http://localhost:8025

## Default settings
- User credentials:
  - email: test@example.com
  - password: test1234

## Deploying
- Change template values in [Makefile](Makefile)
- Ensure the `ENV` environment variable is set to `production`
- Run `make`
- Edit the [.env](.env) file to match the production environment
- Run `make deploy`
- This will install and expose the app according to the .env file

### Reverse proxy
To set up a reverse proxy on your host (e.g. for hosting multiple projects on one host):
- Copy the [reverse-proxy.docker-compose.yaml](.docker/production/reverse-proxy.docker-compose.yaml) and [reverse-proxy.Caddyfile](.docker/production/reverse-proxy.Caddyfile) to a location on your host.
- Configure the [reverse-proxy.Caddyfile](.docker/production/reverse-proxy.Caddyfile) to accept the correct host
- Run the service with `docker compose up -d`
- Adding more hosts is as simple as copy/pasting the contents of the Caddyfile and changing the external host and internal port
