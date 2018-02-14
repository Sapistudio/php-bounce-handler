# php-bounce-handler

[![Build Status](https://travis-ci.org/malas/php-bounce-handler.svg?branch=master)](https://travis-ci.org/malas/php-bounce-handler)

## Running tests on Docker container

In order to develop this library/run the tests, the easiest way is to run docker container.
Don't have Docker installed? Please visit the instruction on https://docs.docker.com/install/

For the first time you run this command, docker will build the container image, so it may take a few minutes to finish.

```
docker-compose up -d
```

When running the container for the first time or if you pulled new updates, be sure to install the vendors:
```
docker exec <container-id> composer install
```

To run the tests run the following command:
```
docker exec f3e bin/phpspec run
```
