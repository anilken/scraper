<?php

namespace Anilken\Scraper;

use Anilken\Scraper\Base\Controller;

class Afad extends Controller
{
    protected $base_url = "https://deprem.afad.gov.tr";

    /**
     * Fetch data from Afad earthquake
     *
     * @param int $days
     * @return mixed
     */
    public function getLastEarthquakes(int $days = 1)
    {
        $this->setHeaders([
            'Referer' => 'https://deprem.afad.gov.tr/sondepremler',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $data = [
            'm' => 0,
            'utc' => 0,
            'lastDay' => $days,
            'page' => 0,
        ];

        $this->request('POST', $this->base_url . '/latestCatalogsList', $data);

        //cannot get html only json
        return $this->client->getResponse()->getContent();
    }
}
