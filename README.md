PHP API for getting the alexa ranks of web sites

```php
$api = new \RM\SimpleAlexaRank\SimpleAlexaRank();
var_dump($api->getGlobalRank('amazon.com'));
