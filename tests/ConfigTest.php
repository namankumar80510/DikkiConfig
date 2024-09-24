<?php

use Dikki\Config\Config;
use Dikki\Config\ConfigFetcher;
use Dikki\Config\DotEnvParser;
use Dikki\Config\IniParser;
use Dikki\Config\JsonParser;
use Dikki\Config\NeonParser;
use Dikki\Config\PhpArrayParser;
use Dikki\Config\YamlParser;
use Tester\Assert;

require_once __DIR__ . '/bootstrap.php';

$configDir = __DIR__ . '/config';

$configFetcher = new ConfigFetcher($configDir);
$dotenvConfig = new Config(new DotEnvParser($configDir . '/.env'));
$iniConfig = new Config(new IniParser($configDir . '/links.ini'));
$jsonConfig = new Config(new JsonParser($configDir . '/person.json'));
$neonConfig = new Config(new NeonParser($configDir . '/development.neon'));
$phpConfig = new Config(new PhpArrayParser($configDir . '/settings.php'));
$yamlConfig = new Config(new YamlParser($configDir . '/views.yaml'));

// test
Assert::equal($dotenvConfig->get('APP_NAME'), "Sample App");
Assert::equal($iniConfig->get('app.url'), "https://example.com");
Assert::equal($jsonConfig->get('app.author'), "Naman");
Assert::equal($neonConfig->get('app.debug'), false);
Assert::equal($phpConfig->get('app.timezone'), "Asia/Kolkata");
Assert::equal($yamlConfig->get('app.theme'), "Default");

// all
Assert::equal($configFetcher->get('APP_NAME'), "Sample App");
Assert::equal($configFetcher->get('person.app.author'), "Naman");
Assert::equal($configFetcher->get('links.app.url'), "https://example.com");
Assert::equal($configFetcher->get('development.app.debug'), false);
Assert::equal($configFetcher->get('settings.app.timezone'), "Asia/Kolkata");
Assert::equal($configFetcher->get('views.app.theme'), "Default");

var_dump($configFetcher->fetchAllConfigs());
