<?php

namespace Anilken\Scraper\Base;

use Anilken\Scraper\Exception\NotFoundException;
use Anilken\Scraper\Exception\RequestException;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class Controller
{
    protected $client;

    protected $delay = 1000;

    protected $lastRequestTime;

    protected $response;

    protected $status_code;

    /**
     * @param GuzzleClientInterface|null $guzzleClient
     */
    public function __construct(GuzzleClientInterface $guzzleClient = null)
    {
        $this->client = new Client();

        if ($guzzleClient) {
            $this->client->setClient($guzzleClient);
        }
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @return Crawler
     */
    protected function request(string $method = 'GET', string $url = '', array $params = []): Crawler
    {
        // handle delay
        if (! empty($this->delay) && ! empty($this->lastRequestTime)) {
            $currentTime = microtime(true);
            $delaySecs = $this->delay / 1000;
            $delay = max(0, $delaySecs - $currentTime + $this->lastRequestTime);
            usleep($delay * 1000000);
        }

        $this->lastRequestTime = microtime(true);

        $query = http_build_query($params);

        if ($query) {
            $url .= '?'.$query;
        }

        $this->client->setServerParameter('HTTP_USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.67 Safari/537.36');


        $this->client->setHeader('Accept', "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9");
        $this->client->setHeader('User-Agent', "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.67 Safari/537.36");
        $this->client->setHeader('Accept-Encoding', "gzip, deflate, br");
        $this->client->setHeader('Accept-Language', "tr,en;q=0.9");

        $crawler = $this->client->request($method, $url);
        $status_code = $this->client->getResponse()->getStatusCode();

        if ($status_code == 404) {
            throw new NotFoundException('Requested resource not found');
        } elseif ($status_code != 200) {
            throw new RequestException(sprintf('Request failed with "%d" status code', $status_code), $status_code);
        }

        $this->setResponse($crawler);
        $this->setStatusCode($status_code);

        return $crawler;
    }

    /**
     * @param $delay
     * @return void
     */
    public function setDelay($delay)
    {
        $this->delay = intval($delay);
    }

    /**
     * @return int
     */
    public function getDelay(): int
    {
        return $this->delay;
    }

    /**
     * @return mixed
     */
    protected function getResponse()
    {
        return $this->response;
    }

    /**
     * @param $response
     * @return void
     */
    protected function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    protected function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * @param $status_code
     * @return void
     */
    protected function setStatusCode($status_code)
    {
        $this->status_code = $status_code;
    }

    /**
     * @param \DOMNode $node
     * @return array|string|string[]|null
     */
    protected function convertHtmlToText(\DOMNode $node)
    {
        if ($node instanceof \DOMText) {
            $text = preg_replace('/\s+/', ' ', $node->wholeText);
        } else {
            $text = '';

            foreach ($node->childNodes as $childNode) {
                $text .= $this->convertHtmlToText($childNode);
            }

            switch ($node->nodeName) {
                case 'h1':
                case 'h2':
                case 'h3':
                case 'h4':
                case 'h5':
                case 'h6':
                case 'p':
                case 'ul':
                case 'div':
                    $text = "\n\n".$text."\n\n";

                    break;
                case 'li':
                    $text = '- '.$text."\n";

                    break;
                case 'br':
                    $text = $text."\n";

                    break;
            }

            $text = preg_replace('/\n{3,}/', "\n\n", $text);
        }

        return $text;
    }
}
