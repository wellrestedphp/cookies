# WellRESTed Cookies

This package provides `CookieFactory`, a class that facilitates creating `Set-cookie` headers.

To install, add `wellrested/cookies` to your project's `composer.json`.

```json
{
   "require": {
      "wellrested/cookies": "~1.0"
   }
}
```

## Example

Use a `CookieFactory` to create the values for `Set-cookie` headers.

```php
<?php

use WellRESTed\Cookies\CookieFactory;

$cookieFactory = new CookieFactory();

$cookie = $cookieFactory->create('name', 'value');
// name=value
```

Use these with the PSR-7 `ResponseInterface->withAddedHeader` to add cookies to a response. 

```php
$response = $response
  ->withAddedHeader('Set-cookie', $cookieFactory->create('name', 'value'))
  ->withAddedHeader('Set-cookie', $cookieFactory->create('another', 'something-else')));
```

To configure a `CookieFactory` to provide `Domain`, `Path`, `Secure`, and `HttpOnly`, pass these to the contructor.

```php
$cookieFactory = new CookieFactory(
  'www.example.com', // Domain
  '/',               // Path
  true,              // Secure
  true               // HttpOnly
);

$cookie = $cookieFactory->create('name', 'value');
// name=value; Domain=www.example.com; Path=/; Secure; HttpOnly
```

### Expires and Max-Age

By default, `create()` will make session cookies that expire when the user closes the browser. To create a persistant cookie, provide the expiration as the third argument. This argument can several forms:

Numeric values are read as seconds for `Max-Age`.

```php
$cookie = $cookieFactory->create('name', 'value', 3600);
// name=value; Max-Age=3600
```

Use a `DateTime` instance to set a specific expiration time. Note that `CookieFactory` will convert this `DateTime` to an `HTTP-date` in the format `<day-name>, <day> <month> <year> <hour>:<minute>:<second> GMT` in accordance with [RFC7321](https://tools.ietf.org/html/rfc7231#section-7.1.1.2).

```php
$cookie = $cookieFactory->create('name', 'value', new DateTime('31-Dec-2018 23:00:00 EST'));
// name=value; Expires=Tue, 01 Jan 2019 04:00:00 GMT
```

You may also provide a string which will be used as-is for the expiration. When you provide a string, you are responsible for formatting the date.

```php
$cookie = $cookieFactory->create('name', 'value', new DateTime('Tue, 01 Jan 2019 04:00:00 GMT'));
// name=value; Expires=Tue, 01 Jan 2019 04:00:00 GMT
```

### Removing Cookies

To remove a cookie, use the `remove` method to create a `Set-cookie` header that sets the value for the cookie to an empty string and sets the expiration to a date in the past.

```php
$cookie = $cookieFactory->remove('name');
// name=; Expires=Thu, 01 Jan 1970 00:00:00 GMT
```

## Development

To setup a local development enviroment, close this repositoy and run:

```bash
docker-compose build
docker-compose run --rm php composer install
```

To run unit tests, run:

```bash
docker-compose run --rm php phpunit
```
