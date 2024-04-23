# Dikki Config

Create and store PHP app configuration files (like site url, timezone, db credentials, etc.) in any of the bellow
formats:

- PHP arrays
- JSON
- YAML
- INI

## Installation

```bash
composer require dikki/config
```

## Usage

### Create a config file or folder

First, ensure that you have a config folder in your project. Add all your config files in this folder.

```php
use Dikki\Config\Config;

// 1. create instance of Config class 
// 2. pass the parser you want to use to the constructor [YamlParser, JsonParser, IniParser, PhpArrayParser]
// 3. pass the path to the config folder to the constructor or the file path to the config file
$config = new Config(new \Dikki\Config\YamlParser(__DIR__ . '/config'));

// get a config value (dot notation is supported)
echo $config->get('app.timezone');
```

**OUTPUT:** `UTC`

## Supported Parsers

- PHP Array (Dikki\Config\PhpArrayParser)
- JSON (Dikki\Config\JsonParser)
- YAML (Dikki\Config\YamlParser)
- INI (Dikki\Config\IniParser)
