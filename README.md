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
// detect the environment
$env = new PI\Configuration\Environment();

// map urls to specific environments
$urls = new PI\Configuration\UrlSet();
$urls->set('production', 'production.url');
$urls->set('staging', 'staging.url');
$urls->set('development', array(
    'local.url',
    'project.url'
));

// access your configuration based on the current environment
$config = new PI\Configuration\Configuration($urls);
$config->get('file:key:subkey'); //
```

## License

MIT © [Pascal Iske](https://pascal-iske.de)
