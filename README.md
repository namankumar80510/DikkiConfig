# Dikki Config

**Disclosure: The documentation on this page was written with Claude AI**.

Dikki Config is a PHP library that allows you to create and manage configuration files for your PHP applications. It supports multiple file formats including PHP arrays, `.env` files, JSON, YAML, INI, and Neon. This library provides a unified interface to access configuration values regardless of the underlying file format.

## Supported Parsers

- **PHP Array** (`Dikki\Config\PhpArrayParser`)
- **JSON** (`Dikki\Config\JsonParser`)
- **YAML** (`Dikki\Config\YamlParser`)
- **INI** (`Dikki\Config\IniParser`)
- **Neon** (`Dikki\Config\NeonParser`)
- **DotEnv** (`Dikki\Config\DotEnvParser`)

## Installation

To install Dikki Config, use Composer:

```bash
composer require dikki/config
```

## Usage

### Multiple Config File Types

If you are using more than one type of configuration file, you can use the `ConfigFetcher` class to fetch and merge configurations from all supported file types.

#### Example of using ConfigFetcher Class

```php
use Dikki\Config\ConfigFetcher;

$configFetcher = new ConfigFetcher(__DIR__ . '/config');

// Fetch all configurations
$config = $configFetcher->fetch();

// Get a specific configuration value using dot notation
echo $configFetcher->get('app.timezone');
```

### Individual Config File Types

If you are using only one type of configuration file, you can use the `Config` class with the appropriate parser.

#### Create a Config File or Folder

First, ensure that you have a config folder in your project. Add all your config files in this folder.

#### Example

```php
use Dikki\Config\Config;
use Dikki\Config\YamlParser;

// Create an instance of the Config class
$config = new Config(new YamlParser(__DIR__ . '/config')); # pass either single file path or whole directory

// Get a configuration value (dot notation is supported)
echo $config->get('app.timezone');
```

**OUTPUT:** `UTC`

## More Details

### ConfigFetcher Class

The `ConfigFetcher` class is designed to recursively search for and parse all supported configuration file formats within a given directory.

#### Constructor

```php
public function __construct(string $path)
```

- `path`: The path to the directory containing configuration files.

#### Methods

- `fetch()`: Fetch and parse all supported config files.
- `getFiles(string $dir)`: Recursively get all files from the given directory.
- `mergeConfigs(array $baseConfig, array $newConfig)`: Merge two configuration arrays, preserving nested keys.
- `get(string $key, mixed $default = null)`: Get a specific configuration value by key using dot notation.

### Config Class

The `Config` class takes an instance of a class implementing `ConfigInterface` and delegates the `get()` method to it.

#### Constructor

```php
public function __construct(ConfigInterface $parser)
```

- `parser`: An instance of a class implementing `ConfigInterface`.

#### Methods

- `get(string $key, mixed $default = null)`: Get a value from the config array using dot notation.
- `getAll()`: Get all configuration values.
- `parse()`: Parse the configuration file(s) and return an array.

### Parser Classes

Each parser class implements the `ConfigInterface` and provides methods to parse specific file formats.

#### Common Methods

- `parse()`: Parse the configuration file(s) and return an array.
- `get(string $key, mixed $default = null)`: Get a value from the config array using dot notation.

#### DotEnvParser

Parses `.env` files.

#### IniParser

Parses INI files.

#### JsonParser

Parses JSON files.

#### NeonParser

Parses Neon files.

#### PhpArrayParser

Parses PHP array files.

#### YamlParser

Parses YAML files.

## Testing

To test the functionality of the Dikki Config library, you can use the provided test cases coded using Nette Tester.

```php
use Dikki\Config\Config;
use Dikki\Config\ConfigFetcher;
use Dikki\Config\DotEnvParser;
use Dikki\Config\IniParser;
use Dikki\Config\JsonParser;
use Dikki\Config\NeonParser;
use Dikki\Config\PhpArrayParser;
use Dikki\Config\YamlParser;
use Tester\Assert;

require __DIR__ . '/vendor/autoload.php';

Tester\Environment::setup();

$configDir = __DIR__ . '/config';

$configFetcher = new ConfigFetcher($configDir);
$dotenvConfig = new Config(new DotEnvParser($configDir . '/.env'));
$iniConfig = new Config(new IniParser($configDir . '/iniconfig.ini'));
$jsonConfig = new Config(new JsonParser($configDir . '/jsonconfig.json'));
$neonConfig = new Config(new NeonParser($configDir . '/neonconfig.neon'));
$phpConfig = new Config(new PhpArrayParser($configDir . '/phpconfig.php'));
$yamlConfig = new Config(new YamlParser($configDir . '/yamlconfig.yaml'));

// Test individual parsers
Assert::equal($dotenvConfig->get('APP_NAME'), "Sample App");
Assert::equal($iniConfig->get('app.url'), "https://example.com");
Assert::equal($jsonConfig->get('app.author'), "Naman");
Assert::equal($neonConfig->get('app.debug'), false);
Assert::equal($phpConfig->get('app.timezone'), "Asia/Kolkata");
Assert::equal($yamlConfig->get('app.theme'), "Default");

// Test ConfigFetcher
Assert::equal($configFetcher->get('APP_NAME'), "Sample App");
Assert::equal($configFetcher->get('app.author'), "Naman");
Assert::equal($configFetcher->get('app.url'), "https://example.com");
Assert::equal($configFetcher->get('app.debug'), false);
Assert::equal($configFetcher->get('app.timezone'), "Asia/Kolkata");
Assert::equal($configFetcher->get('app.theme'), "Default");
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
