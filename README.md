PHP API for getting the alexa ranks of web sites

```php
$api = new \SimpleAlexaRank\SimpleAlexaRank\SimpleAlexaRank();
var_dump($api->getGlobalRank('amazon.com'));
// returns int(6)
