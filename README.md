# Geolocation PHP class connects to Google MAPS API
[![Latest Stable Version](http://img.shields.io/packagist/v/jeroendesloovere/geolocation-php-api.svg)](https://packagist.org/packages/jeroendesloovere/geolocation-php-api)
[![License](http://img.shields.io/badge/license-MIT-lightgrey.svg)](https://github.com/jeroendesloovere/geolocation-php-api/blob/master/LICENSE)
[![Build Status](http://img.shields.io/travis/jeroendesloovere/geolocation-php-api.svg)](https://travis-ci.org/jeroendesloovere/geolocation-php-api)

> This Geolocation PHP class connects to Google Maps API to find latitude/longitude or address.

## Installing

### Using Composer

When using [Composer](https://getcomposer.org) you can always load in the latest version.

``` json
{
    "require": {
        "jeroendesloovere/geolocation-php-api": "1.3.*"
    }
}
```
Check [in Packagist](https://packagist.org/packages/jeroendesloovere/geolocation-php-api).

### Usage example

**getCoordinates**

> Get latitude/longitude coordinates from address.

``` php
$street = 'Koningin Maria Hendrikaplein';
$streetNumber = '1';
$city = 'Gent';
$zip = '1';
$country = 'belgium';

$result = Geolocation::getCoordinates(
    $street,
    $streetNumber,
    $city,
    $zip,
    $country
);
```

**getAddress**

> Get address from latitude/longitude coordinates.

``` php
$latitude = 51.0363935;
$longitude = 3.7121008;

$result = Geolocation::getAddress(
    $latitude,
    $longitude
);
```

Check [the Geolocation class source](./src/Geolocation.php) or [view examples](./examples/example.php).

## Documentation

The class is well documented inline. If you use a decent IDE you'll see that each method is documented with PHPDoc.

## Contributing

It would be great if you could help us improve this class. GitHub does a great job in managing collaboration by providing different tools, the only thing you need is a [GitHub](http://github.com) login.

* Use **Pull requests** to add or update code
* **Issues** for bug reporting or code discussions
* Or regarding documentation and how-to's, check out **Wiki**
More info on how to work with GitHub on help.github.com.

## License

The module is licensed under [MIT](./LICENSE). In short, this license allows you to do everything as long as the copyright statement stays present.