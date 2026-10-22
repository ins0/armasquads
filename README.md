# ArmaSquads

Create and host squad.xml files for Arma games, free and without webspace of your own.

The site runs on [armasquads.com](https://armasquads.com) (English) and [armasquads.de](https://armasquads.de) (German).
It works with Arma, Arma 2, Arma 2: Combined Operations and Arma 3.

## Features

- **Squads and members:** any number of squads per account, each with its members (Player ID, nick, name, email,
  ICQ, remark).
- **Hosting:** every squad gets a private URL that serves its `squad.xml`, `squad.dtd` and logo. All files can also be
  downloaded as a ZIP file.
- **Logos:** upload a JPG, PNG or GIF of up to 4096x4096 pixels. It is scaled to the closest Arma size (16x16 to
  256x256 pixels), images that are not square get a transparent border, transparency is kept, and the result is
  converted to PAA (DXT5) with [armake](https://github.com/KoffeinFlummi/armake).
- **Tag check:** the squad form warns about tags that can keep the logo from showing in game (a period or space at the
  end, or one of `\ / : * ? " < > |`).
- **Editors:** the owner of a squad can let other accounts edit it by adding their username. Only the owner can delete
  the squad or change its editors.
- **REST API:** version 1 with an API key per account. The documentation is on
  [armasquads.com/RESTful/api](https://armasquads.com/RESTful/api), a PHP client is available in
  [ins0/armasquads-api-restclient](https://github.com/ins0/armasquads-api-restclient).
- **Languages:** English and German.
- **No emails:** the site does not send any emails.

## Running it locally

You need Docker with Compose.

```bash
git clone https://github.com/ins0/armasquads.git
cd armasquads
docker compose up -d --build
```

The site is then available on http://localhost:8080. On the first start MariaDB creates the tables from
`docker/mariadb/init.sql`. The database and the uploaded logos are stored in `data_mysql/` and
`data_armasquads/uploads/`, both are ignored by Git. `docker compose down` stops everything and keeps the data.

### Configuration

The web container reads these environment variables:

| Variable | Meaning | Default |
|---|---|---|
| `MYSQL_HOST` | database host | `localhost` |
| `MYSQL_USER`, `MYSQL_PWD`, `MYSQL_DB` | database user, password and name | |
| `APP_DISPLAY_ERRORS` | `1` shows error details | off |
| `ARMAKE_BINARY` | path of the armake binary | `armake` |

### Database changes

`docker/mariadb/init.sql` always contains the current schema for new installations. Changes for existing databases
are in `docker/mariadb/migrations/`, each with a `.rollback.sql` file, and are applied by hand, for example:

```bash
docker compose exec -T db sh -c 'mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' < docker/mariadb/migrations/2026-10-22-add-squad-editors.sql
```

### Logos without a PAA file

Logos uploaded years ago were stored without size checks, so some of them only have a PNG. This command scales them
like new uploads and creates the missing PAA files:

```bash
docker compose exec -u application web php bin/rebuild-logos.php
```

It prints one line per logo, `--limit=<n>` processes only the first `n` logos.

## Tests

```bash
cd src
composer install
vendor/bin/phpunit
```

Without the imagick extension, install with `composer install --ignore-platform-req=ext-imagick`. Tests that need
imagick or armake are then skipped. To run all of them, use the Docker image, which has both:

```bash
docker compose build web
docker run --rm -v "$PWD/src":/app -w /app --entrypoint vendor/bin/phpunit armasquads
```

## Project structure

```
src/app/         application code (Slim 4, namespace Armasquads)
src/templates/   page templates
src/lang/        translations (en, de)
src/public/      web root with index.php, CSS, JavaScript and images
src/bin/         command line scripts
src/tests/       PHPUnit tests
docker/          nginx configuration, database schema and migrations
```

## Contributing

Bugs and ideas go to the [issues](https://github.com/ins0/armasquads/issues). Pull requests are welcome, commit
messages follow [Conventional Commits](https://www.conventionalcommits.org/).

## License

GNU General Public License v2.0 only, see [LICENSE.txt](LICENSE.txt).

ArmaSquads is not affiliated with or authorized by Bohemia Interactive a.s. Arma and all associated logos and designs
are trademarks or registered trademarks of Bohemia Interactive a.s.
