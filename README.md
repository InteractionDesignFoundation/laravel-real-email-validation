# Real email validation for Laravel

[![Build Status](https://img.shields.io/travis/InteractionDesignFoundation/laravel-real-email-validation/master.svg?style=flat-square)](https://travis-ci.org/InteractionDesignFoundation/laravel-real-email-validation)
[![Code coverage](https://scrutinizer-ci.com/g/InteractionDesignFoundation/laravel-real-email-validation/badges/coverage.png)](https://scrutinizer-ci.com/g/InteractionDesignFoundation/laravel-real-email-validation)
[![Quality Score](https://img.shields.io/scrutinizer/g/InteractionDesignFoundation/laravel-real-email-validation.svg?style=flat-square)](https://scrutinizer-ci.com/g/InteractionDesignFoundation/laravel-real-email-validation)
[![StyleCI](https://github.styleci.io/repos/200292916/shield?branch=master)](https://github.styleci.io/repos/200292916)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/idf/laravel-real-email-validation.svg?style=flat-square)](https://packagist.org/packages/idf/laravel-real-email-validation)
[![Total Downloads](https://img.shields.io/packagist/dt/idf/laravel-real-email-validation.svg?style=flat-square)](https://packagist.org/packages/idf/laravel-real-email-validation)

Laravel has a good `email` validation rule, but it can miss some invalid email addresses.
This packages aims to cover more cases.

## Installation

You can install the package via composer:

```bash
composer require idf/laravel-real-email-validation
```

The package will automatically register itself.

### Translations

If you wish to edit the package translations, you can run the following command to publish them into your `resources/lang` folder

```bash
php artisan vendor:publish --provider="IDF\RealEmailValidation\ServiceProvider"
```

## Usage

```php
$this->validate($request, [
    'email' => ['required', 'email', new RealEmail()],
]);
```

By default it uses the following checks: `html5`, `rfc`, `host`, but you can define your set:
```php
// checks without network requests
new RealEmail(['html5', 'rfc'])
```

1. `html5`: Uses regex pattern for rules defined by [WHATWG](https://html.spec.whatwg.org/multipage/input.html#valid-e-mail-address). Browsers use it for `input[type="email"]`.
1. `rfc`: Strict RFC validation. Check against RFC 5321, 5322, 6530, 6531, 6532, treats warnings as errors.
1. `host`: Checks DNS Records for the host extracted from email address. Uses network.
1. `mx`: Check DNS Records for MX type only. ⚠️ This option is not reliable because it depends on the network conditions and some valid servers refuse to respond to those requests.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email hello@team.interaction-design.org instead of using the issue tracker.

## Credits

- [All Contributors](../../contributors)
- Symfony validation (for inspiration)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
