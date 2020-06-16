<?php
declare(strict_types=1);
ini_set('display_errors', 'stderr');

require __DIR__ . '/../vendor/autoload.php';

if ($argc !== 4) {
    $message = <<<EOS
Invalid argument.

Usage:
  {$argv[0]} <connector> <host> <app-name>

  <connector> ::= fopen | curl | guzzle

EOS;

    fprintf(STDERR, $message);
    exit(1);
}

switch ($argv[1]) {
    case 'fopen':
        $connector = new \Minitoot\Connector\FopenConnector();
        break;
    case 'curl':
        $connector = new \Minitoot\Connector\CurlConnector();
        break;
    case 'guzzle':
        $connector = new \Minitoot\Connector\GuzzleConnector();
        break;
    default:
        fprintf(STDERR, "Unknown connector: {$argv[1]}\n");
        exit(1);
}

$client = new \Minitoot\Client($argv[2], $connector);
$app = $client->registerApplication($argv[3], \Minitoot\Client::OOB_CALLBACK_URI, 'read write');
var_dump($app);
