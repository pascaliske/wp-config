# `pi/wp-config`

[![build status](https://git.pascal-iske.de/pascaliske/wp-config/badges/master/build.svg)](https://git.pascal-iske.de/pascaliske/wp-config/commits/master)
[![coverage report](https://git.pascal-iske.de/pascaliske/wp-config/badges/master/coverage.svg)](https://git.pascal-iske.de/pascaliske/wp-config/commits/master)

> Package for easy WordPress configurations

A composer package which abstracts the standard wordpress configuration and enables the usage of environment based `.yml` files.

## Install

You have to add the following to your `composer.json`s repositories:

```json
{
  "type": "composer",
  "url" : "https://dev.pascal-iske.de/packages/"
}
```

And then you can just require the package:

```bash
$ composer require pi/wp-config
```

## Usage

```php
$root = dirname(__DIR__);

$env = new PI\Configuration\Environment($root);

// set env urls
$urls = new PI\Configuration\UrlSet();
$urls->set('production', 'adrian-vidak.de');
$urls->set('staging', 'preview-staging-adrian-vidak-de.pascal-iske.de');
$urls->set('development', array(
    'local.dev',
    'client-project.dev'
));

// set configuration
$config = new PI\Configuration\Configuration($root, $urls);

$config->get('')
```

## License

MIT © [Pascal Iske](https://pascal-iske.de)
