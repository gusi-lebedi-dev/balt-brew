# Baltika Brew

WordPress 6.9.4 with the static `balt-brew` theme. The theme contains the original layout, responsive styles, product animation, age confirmation, cookie banner, and the static news page.

## Local start

The untracked `.env.php.development` file is used by Docker Compose. The local WordPress container joins the existing external Docker network `victor-network` and connects to the database using the `victor-mysql` hostname. Adjust the configuration if needed, then run:

```bash
docker compose up --build -d
```

Open <http://localhost:8196> and complete the initial WordPress installation. The default theme is `balt-brew`.

## Development deployment

Pushes to `dev` run on the GitHub Actions runner with labels `self-hosted` and `dev`. The application is exposed on port `8196` as container `balt-brew-dev`.

Create one repository secret named `ENV_DEV`. Its value must be a PHP file matching `.env.php.example`. For the current development server, use `dev-mysql:3306` as `db-host` and `http://v.gldev.pro:8196` as `home-url` until a reverse-proxy domain is configured.

The workflow writes the secret to `/volumes/balt-brew-dev/.env.php` with read access for PHP-FPM, mounts it read-only, and persists writable uploads in `/volumes/balt-brew-dev/uploads`.
