<?php

namespace Anilken\Scraper;

use Anilken\Scraper\Base\Controller;
use Anilken\Scraper\Exception\NotFoundException;
use Symfony\Component\DomCrawler\Crawler;

class Sahibinden extends Controller
{
    protected $base_url = "https://www.sahibinden.com";

    /**
     * @param $id
     * @return array
     */
    public function getListingId($id): array
    {
        return $this->listingParser($this->searchById($id));
    }

    /**
     * @param $url
     * @return array
     */
    public function getListingUrl($url): array
    {
        $response = $this->request('GET', $url);

        if ($response->filterXPath('//*[@id="favoriteClassifiedPrice"]')->first()->count() == 0) {
            throw new NotFoundException('Requested listing not found');
        }

        return $this->listingParser($response);
    }

    /**
     * @param $ids
     * @return array
     */
    public function getListingsId($ids): array
    {
        $ids = (array) $ids;
        $list = [];

        foreach ($ids as $id) {
            $list[$id] = $this->getListingId($id);
        }

        return $list;
    }

    /**
     * @param $crawler
     * @return array
     */
    protected function listingParser($crawler): array
    {
        $info = [
            'id' => null,
            'url' => null,
            'title' => null,
            'price' => null,
            'description' => null,
            'description_html' => null,
            'user' => [],
            'info' => [],
            'images' => [],
            'breadcrumb' => [],
            'loc_breadcrumb' => [],
            'attributes' => [],
        ];

        $info['id'] = $crawler->filter('#classifiedIdValue')->first()->attr('value');
        $info['url'] = $crawler->filter('link[rel="alternate"]')->first()->attr('href');
        $info['title'] = $crawler->filter('div.classifiedDetailTitle > h1')->text();
        $info['price'] = str_replace(['TL', ' '], '', $crawler->filter('#favoriteClassifiedPrice')->first()->attr('value'));
        $desc = $this->cleanDescription($crawler->filter('#classifiedDescription'));

        $info['description'] = $desc['text'];
        $info['description_html'] = $desc['html'];


        if ($crawler->filter('.username-info-area h5')->count() > 0) {
            $info['user']['name'] = $crawler->filter('.username-info-area h5')->text();
        }

        if ($crawler->filter('.user-info-store-name')->count() > 0) {
            $info['user']['store_name'] = $crawler->filter('.user-info-store-name')->text();
        }

        if ($crawler->filter('.user-info-agent h3')->count() > 0) {
            $info['user']['name'] = $crawler->filter('.user-info-agent h3')->text();
        }

        if ($crawler->filter('.user-info-phones')->count() > 0) {
            $info['user']['phone'] = $crawler->filter('.user-info-phones > dl > div')->each(function ($node) use ($crawler) {
                return $info['user']['phone'][] = $node->filter('dd')->text();
            });
        }


        if ($crawler->filter('#phoneInfoPart > li')->count() > 0) {
            $info['user']['phone'] = $crawler->filter('#phoneInfoPart > li')->each(function ($node) use ($crawler) {
                return $info['user']['phone'][] = $node->filter('.pretty-phone-part')->text();
            });
        }


        $info['breadcrumb'] = $crawler->filter('#uiBreadCrumb > li > a')->each(function ($node) {
            return [
                'url' => $node->attr('href'),
                'text' => $node->text(),
            ];
        });

        $info['loc_breadcrumb'] = $crawler->filter('div.classifiedInfo > h2 > a')->each(function ($node) {
            return [
                'url' => $node->attr('href'),
                'text' => $node->text(),
            ];
        });


        $crawler->filter('div.classifiedInfo > ul > li')->each(function ($node) use (&$info) {
            $name = $node->filter('strong')->text();
            $val = $node->filter('span')->text();

            return $info['info'][$name] = $val;
        });


        $info['images'] = $crawler->filter('div.classifiedDetailMainPhoto label > img')->each(function ($node) {
            $image = $node->attr('data-src') == null ? $node->attr('src') : $node->attr('data-src');

            return [
                'thumbnail' => str_replace('x5_', 'thmb_', $image),
                'medium' => $image,
                'full' => str_replace('x5_', 'x16_', $image),
            ];
        });

        $attribute_title = $crawler->filter('#classifiedProperties > h3:not(:contains("Boyalı veya Değişen Parça"))')->each(function ($node) {
            return $node->text();
        });

        $crawler->filter('#classifiedProperties > ul')->each(function ($node, $i) use (&$info, &$attribute_title) {
            $title = $attribute_title[$i];

            return $info['attributes'][$title] = $node->filter('.selected')->each(function ($node) {
                return $node->text();
            });
        });

        return $info;
    }

    /**
     * @param $id
     * @return Crawler
     */
    protected function searchById($id): Crawler
    {
        $params = ['query_text' => $id];

        $response = $this->request('GET', $this->base_url . '/kelime-ile-arama', $params);

        if ($response->filterXPath('//*[@id="favoriteClassifiedPrice"]')->first()->count() == 0) {
            throw new NotFoundException('Requested listing not found or banned');
        }

        return $response;
    }

    /**
     * @param Crawler $descriptionNode
     * @return array
     */
    protected function cleanDescription(Crawler $descriptionNode): array
    {
        $descriptionNode->filter('a')->each(function ($node) {
            $domElement = $node->getNode(0);
            $href = $domElement->getAttribute('href');
            $domElement->setAttribute('href', 'https://www.google.com/search?q='.urlencode($href).'&sourceid=chrome&ie=UTF-8');
        });

        $html = $descriptionNode->html();
        $text = trim($this->convertHtmlToText($descriptionNode->getNode(0)));

        return [
            'html' => $html,
            'text' => $text,
        ];
    }
}
