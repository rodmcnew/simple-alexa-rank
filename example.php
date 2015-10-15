<?php
//Must run "composer install" before this will work
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/SimpleAlexaRank.php';

$api = new \RodMcnew\SimpleAlexaRank\SimpleAlexaRank();
var_dump($api->getGlobalRank('amazon.com'));
