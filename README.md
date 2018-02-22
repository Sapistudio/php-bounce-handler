# php-bounce-handler

[![Build Status](https://travis-ci.org/malas/php-bounce-handler.svg?branch=master)](https://travis-ci.org/malas/php-bounce-handler)

## Overview

This library parses bounced email reports and parses and extracts recipient and [RFC 3463](https://tools.ietf.org/html/rfc3463) status code. From these fields it can assign an email one of the Message statuses - hard bounce, soft bounce, unknown or "ok" status.

"ok" status in most cases occurs when receiving vacation/auto-responder emails.

## Example usage

### Import and parse emails imported via IMAP
```
use Malas\BounceHandler\BounceHandler;
use Malas\BounceHandler\BounceHandler;

$import = new IMAPMailImport([
  // imap_open mailbox string
  'mailbox' => '{imap.example.com:143/imap/notls}INBOX',
  'username' => 'user@example.com',
  'password' => 'secret-password',
  // do you want to delete the emails after processing true|false
  'delete_mail' => true,
  'options' => CL_EXPUNGE,
  ]);
// array of Malas\BounceHandler\Model\Message objects ready for parsing
$mails = $import->import(100);

$handler = new BounceHandler();
// final object with the parsed results
// please check Malas\BounceHandler\Model\Result for more details
$result = $handler->parse($mails);
```

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
