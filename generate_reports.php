<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

$filters = [
    'weight',
    'thickness',
    'height',
    'width',
    'length',
    'manufacturer'
];

$client = new Client([
//    'base_uri'    => 'https://varioexcel.test',
    'base_uri'    =>  'https://vario-export.lemonadeframework.cz',
    'timeout'     => 60,
    'http_errors' => false,
    'verify'      => false // Důležité pro .test domény se self-signed certifikátem
]);

$total = 1 << count($filters);

for ($i = 0; $i < $total; $i++) {

    $query = [];

    for ($bit = 0; $bit < count($filters); $bit++) {
        if ($i & (1 << $bit)) {
            $query[$filters[$bit]] = 1;
        }
    }

    $queryString = http_build_query($query);

    echo "[" . ($i + 1) . "/{$total}] Requesting: ?" . ($queryString ?: '(no filters)') . PHP_EOL;

    try {
        // Voláme root '/', aby nás nezastavil .htaccess pravidlem pro .php soubory
        $response = $client->request('GET', '/', [
            'query' => array_merge($query, ['debug_guzzle' => '1']),
            'headers' => [
                'User-Agent' => 'GuzzleWarmup/1.0',
                'Cache-Control' => 'no-cache',
            ]
        ]);

        echo "Status: " . $response->getStatusCode() . PHP_EOL;

    } catch (GuzzleException $e) {
        echo "ERROR: {$e->getMessage()}\n";
    }

    usleep(100000); // 0.1s pauza
}
