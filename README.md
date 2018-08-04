# `pi/wp-config`

[![Build Status](https://travis-ci.com/pascaliske/wp-config.svg?branch=master)](https://travis-ci.com/pascaliske/wp-config)

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

Create the following folder structure:

```bash
$ tree conf
conf
├── development
├── production
└── staging

3 directories, 0 files
```

In your WordPress config require the composer autoload file and then you can initialize the config setup:

```php
// map urls to specific environments
$urls = new PI\Configuration\UrlSet();
$urls->set('production', '<production-domain>');
$urls->set('staging', '<staging-domain>');
$urls->set('development', array(
    '<local-domain>',
    '<other-domain>',
));

// access your environment and configuration
$env = new PI\Configuration\Environment($urls);
$config = $env->config();

$env->version; // returns the version string from composer file
$config->get('file:key:subkey'); // returns the value for the given key path
```

## License

MIT © [Pascal Iske](https://pascal-iske.de)
