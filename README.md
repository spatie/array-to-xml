# Convert an arry to xml

[![Latest Version](https://img.shields.io/github/release/spatie/array-to-xml.svg?style=flat-square)](https://github.com/spatie/array-to-xml/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/array-to-xml/master.svg?style=flat-square)](https://travis-ci.org/spatie/array-to-xml)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/spatie/array-to-xml.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/array-to-xml/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/array-to-xml.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/array-to-xml)
[![Total Downloads](https://img.shields.io/packagist/dt/league/array-to-xml.svg?style=flat-square)](https://packagist.org/packages/league/array-to-xml)

This package provides a very simple class to convert an array to an xml string.

## Install

Via Composer

``` bash
composer require spatie/array-to-xml
```

## Usage

```php
use Spatie\ArrayToXml\ArrayToXml;
...
$array = [
    'Good guy' => [
        'name' => 'Luke Skywalker',
        'weapon' => 'Lightsaber'
    ],
    'Bad guy' => [
        'name' => 'Sauron',
        'weapon' => 'Evil Eye'
    ]
];

$result = ArrayToXml::convert($array);
```
After running this piece of code `$result` will contain:

```xml
<?xml version="1.0"?>
<root>
    <Good_guy>
        <name>Luke Skywalker</name>
        <weapon>Lightsaber</weapon>
    </Good_guy>
    <Bad_guy>
        <name>Sauron</name>
        <weapon>Evil Eye</weapon>
    </Bad_guy>
</root>
```

## Testing

```bash
vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
