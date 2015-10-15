<?php

namespace RodMcnew\SimpleAlexaRank;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

/**
 * Class AlexaRankApi
 * @package simple_alexa_rank
 */
class SimpleAlexaRank
{
    protected $alexaApiUrl = 'http://data.alexa.com/data?cli=10&url=';

    /**
     * Looks up a domain name via the Alexa REST API and
     * returns the web site's "GlobalRank" as an integer
     *
     * @param string $domainName the domain name to lookup on Alexa
     * @return int
     */
    public function getGlobalRank($domainName)
    {
        return (int)$this->getAlexaXmlResponse($domainName)
            ->SD->POPULARITY['TEXT'];
    }

    /**
     * Looks up a domain name via the Alexa REST API and
     * returns the Alexa XML response
     *
     * @param string $domainName the domain name to lookup on Alexa
     * @return \SimpleXMLElement
     */
    public function getAlexaXmlResponse($domainName)
    {
        $client = new Client();
        $res = $client->get($this->alexaApiUrl . urlencode($domainName));
        if ($res->getStatusCode() !== 200) {
            throw new BadAlexaResponseException('Alexa API did not return 200/OK');
        }

        return $this->guzzleResponseToXml($res);
    }

    /**
     * Guzzle 5 provided XML response processing support using
     * the following function but this disappeared in guzzle 6
     *
     * It would be good to find a better way to do this than
     * pasting an old guzzle function here
     *
     * @param Response $res
     * @param array $config
     * @return \SimpleXMLElement
     * @throws BadAlexaResponseException
     */
    protected function guzzleResponseToXml(Response $res, array $config = [])
    {
        $disableEntities = libxml_disable_entity_loader(true);
        $internalErrors = libxml_use_internal_errors(true);
        try {
            // Allow XML to be retrieved even if there is no response body
            $xml = new \SimpleXMLElement(
                (string)$res->getBody() ?: '<root />',
                isset($config['libxml_options']) ? $config['libxml_options'] : LIBXML_NONET,
                false,
                isset($config['ns']) ? $config['ns'] : '',
                isset($config['ns_is_prefix']) ? $config['ns_is_prefix'] : false
            );
            libxml_disable_entity_loader($disableEntities);
            libxml_use_internal_errors($internalErrors);
        } catch (\Exception $e) {
            libxml_disable_entity_loader($disableEntities);
            libxml_use_internal_errors($internalErrors);
            throw new BadAlexaResponseException(
                'Unable to parse response body into XML: ' . $e->getMessage(),
                $this,
                $e,
                (libxml_get_last_error()) ?: null
            );
        }

        return $xml;
    }
}
