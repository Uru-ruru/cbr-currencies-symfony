# Symfony Docker Currency Demo

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework,
with [FrankenPHP](https://frankenphp.dev) and [Caddy](https://caddyserver.com/) inside!

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --pull --no-cache` to build fresh images or `make build`
3. Run `docker compose up --wait` to set up and start a fresh Symfony project or `make up`
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers or `make down`.
6. Or you can run `make start` to run all the commands above in one go!

## Commands

1. For adding messages run `make add`
2. For running consumer run `make consume`

## Endpoints

1. For getting current currency rates run `GET /{charCode}` example: `GET /USD`
2. For getting currency rates for date run `GET /{charCode}/{date}` example: `GET /USD/2025-12-10`
3. For getting currency rates for base currency run `GET /{charCode}/{baseCurrency}` example: `GET /USD/EUR`
4. For getting currency rates for date and base currency run `GET /{charCode}/{baseCurrency}/{date}` example: `GET /USD/EUR/2025-12-10`

## License

Symfony Docker is available under the MIT License.
