⚡⚡ Weeding Helper - Dockerized ⚡⚡
=================================

**First**: read the main [Weeding Helper README file](../README.md). You'll be able to use this docker setup to view the `documentation.php` file.

**Second**: don't use this setup as-is in production. The working example in `docker/app/config.php` is for illustrative purposes only.

**Third**: File uploaded are limited to 2.5 MB, with a UI note added to the `upload.php` file: *"Must be less than 2.5 MB (~2000 records)"*

## Configuration
This setup should work out-of-the-box for local deployment.

**Default File Paths**: If you change the value of `$path_main` or `$secure_outside_path` in `config.php`, you'll need to modify the hard-coded directory paths in `app/Dockerfile` accordingly.

**Default Database Settings**: The database username and password values that are defined in `config.php` must match the environment variables set in `docker-compose.yml` under the `weeding-helper-db` service definition.

```php
# Defaults set in config.php
$MYSQL_LOGIN = "user";
$MYSQL_PASS = "password";
$MYSQL_DB = "weeding";

# Defaults set in docker-compose.yml
MYSQL_ROOT_PASSWORD: password
MYSQL_DATABASE: weeding
MYSQL_USER: user
MYSQL_PASSWORD: password
```

**Default Cron Jobs**: Cron Jobs need to be configured in `ofelia.ini`. This is specific to the docker implementation and differs from the instruction in `documentation.php` 

## Startup
There's a startup script is included in the project root. Open up a terminal and then run the script: `./start.sh`. If for some reason the script isn't executable, run `chmod +x start.sh`.

Weeding Helper will be available at [http://weeding.docker.localhost](http://weeding.docker.localhost)

**NOTE**: If you see a join() warning from PHP, ignore it. This will go away once you upload the first file.
