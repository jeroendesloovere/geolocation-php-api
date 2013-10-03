# Geolocation PHP class connects to Google MAPS API

This Geolocation PHP class connects to Google Maps API to find latitude/longitude or address.

## Public functions

```
// get latitude/longitude coordinates from address
Geolocation::getCoordinates('Koningin Maria Hendrikaplein', '1', 'Gent', '1', 'belgium');

// get address from latitude/longitude coordinates
Geolocation::getAddress(51.0363935, 3.7121008);
```
[View tests](./tests/index.php) or [check class](./src/Geolocation/Geolocation.php).

## Contributing

It would be great if you could help us improve this class. GitHub does a great job in managing collaboration by providing different tools, the only thing you need is a [GitHub](http://github.com) login.

* Use **Pull requests** to add or update code
* **Issues** for bug reporting or code discussions
* Or regarding documentation and how-to's, check out **Wiki**
More info on how to work with GitHub on help.github.com.


## License

The module is licensed under [MIT](./LICENSE.md). In short, this license allows you to do everything as long as the copyright statement stays present.