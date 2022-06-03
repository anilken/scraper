<?php

namespace Anilken\Scraper\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Mockery as m;

class SahibindenTest extends TestCase
{
    private $scraper;

    /** @test */
    public function it_can_test()
    {
        $this->assertTrue(true);
    }

    public function getScraper(HandlerStack $handler = null)
    {
        $guzzleOptions = [
            'defaults' => ['allow_redirects' => false, 'cookies' => true],
        ];
        if ($handler) {
            $guzzleOptions['handler'] = $handler;
        }
        $guzzleClient = new Client($guzzleOptions);

        return m::mock('Anilken\Scraper\Sahibinden', [$guzzleClient])->makePartial();
    }

    /** @test */
    public function it_set_delay_test()
    {
        $scraper = $this->getScraper();
        $scraper->setDelay(5000);
        $this->assertEquals(5000, $scraper->getDelay());
    }

    /** @test */
    public function it_get_listing_test()
    {
        $transactions = [];
        $history = Middleware::history($transactions);
        $mock = new MockHandler([
            new Response(200, ['content-type' => 'text/html; charset=utf-8'], file_get_contents(__DIR__.'/resources/listing.html')),
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push($history);
        $scraper = $this->getScraper($handler);
        $app = $scraper->getListingId('144190656');
        $expected = json_decode(file_get_contents(__DIR__.'/resources/listing.json'), true);

        $this->assertEquals($expected, $app);
        $this->assertEquals('https://www.sahibinden.com/kelime-ile-arama?query_text=144190656', $transactions[0]['request']->getUri());
    }
}
