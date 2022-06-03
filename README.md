# Scrape turkish popular sites - Education purpose only

[![Latest Version on Packagist](https://img.shields.io/packagist/v/anilken/scraper.svg?style=flat-square)](https://packagist.org/packages/anilken/scraper)
[![Tests](https://github.com/anilken/scraper/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/anilken/scraper/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/anilken/scraper.svg?style=flat-square)](https://packagist.org/packages/anilken/scraper)

## Installation

Add `anilken/scraper` as a require dependency in your `composer.json` file:

```sh
composer require anilken/scraper
```

## Currently supported sites

- Sahibinden - [Examples](examples/Sahibinden.md)
- More coming soon...

## Usage example of sahibinden

First create a `Sahibinden` or `other` instance.

```php
use Anilken\Scraper\Sahibinden;

$scraper = new Sahibinden();
```

Use with proxy:

```php
$scraper = new Sahibinden(new \GuzzleHttp\Client(['proxy' => 'https://127.0.0.1:80', 'verify' => false]));
```

There are several methods to configure the default behavior:
* `setDelay($delay)`: Sets the delay in milliseconds between requests to site.

### getListingId

#### Parameters

* `$id`: Sahibinden app identifier.

#### Example

```php
$app = $scraper->getListingId('1029942112'); 

//or get with url 

$app = $scraper->getListingUrl('https://www.sahibinden.com/listing/vasita-arazi-suv-pickup-land-rover-24-saat-gecerli-rakam-sandik-motorlu-faturali-takas-olur-1029942112/detail'); 
```

Gets listing information given its ID.

Returns:

```css
array:12 [▼
  "id" => "1029942112"
  "url" => "https://www.sahibinden.com/listing/vasita-arazi-suv-pickup-land-rover-24-saat-gecerli-rakam-sandik-motorlu-faturali-takas-olur-1029942112/detail"
  "title" => "24 SAAT GEÇERLİ RAKAM SANDIK MOTORLU FATURALI TAKAS OLUR"
  "price" => "400.000"
  "description" => ""
  "description_html" => ""
  "user" => array:2 [▼
    "name" => "Levent XXX"
    "phone" => array:1 [▼
      0 => "0 (501) XXX XX XX"
    ]
  ]
  "info" => array:21 [▼
    "İlan No" => "1029942112"
    "İlan Tarihi" => "01 Haziran 2022"
    "Marka" => "Land Rover"
    "Seri" => "Range Rover Sport"
    "Model" => "2.7 TDV6 HSE"
    "Yıl" => "2008"
    "Yakıt" => "Dizel"
    "Vites" => "Otomatik"
    "KM" => "358.125"
    "Kasa Tipi" => "SUV"
    "Motor Gücü" => "190 hp"
    "Motor Hacmi" => "2720 cc"
    "Çekiş" => "4x4"
    "Kapı" => "5"
    "Renk" => "Siyah"
    "Garanti" => "Hayır"
    "Plaka / Uyruk" => "Türkiye (TR) Plakalı"
    "Kimden" => "Galeriden"
    "Görüntülü Arama İle Görülebilir" => "Hayır"
    "Takas" => "Evet"
    "Durumu" => "İkinci El"
  ]
  "images" => array:16 [▼
    0 => array:3 [▼
      "thumbnail" => "https://i0.shbdn.com/photos/94/21/12/thmb_1029942112otl.jpg"
      "medium" => "https://i0.shbdn.com/photos/94/21/12/x5_1029942112otl.jpg"
      "full" => "https://i0.shbdn.com/photos/94/21/12/x16_1029942112otl.jpg"
    ]
    1 => array:3 [▶]

  ]
  "breadcrumb" => array:6 [▼
    0 => array:2 [▼
      "url" => "/kategori/vasita"
      "text" => "Vasıta"
    ]
    1 => array:2 [▶]
  ]
  "loc_breadcrumb" => array:3 [▼
    0 => array:2 [▼
      "url" => "/arazi-suv-pickup-land-rover-range-rover-sport-2.7-tdv6-hse/aydin"
      "text" => "Aydın"
    ]
    1 => array:2 [▶]
  ]
  "attributes" => array:4 [▼
    "Güvenlik" => array:14 [▼
      0 => "ABS"
      1 => "Airmatic"
      2 => "Alarm"
      3 => "ASR"
      4 => "BAS"
      5 => "EBD"
      6 => "EDL"
      7 => "ESP / VSA"
      8 => "Hava Yastığı (Sürücü)"
      9 => "Hava Yastığı (Yolcu)"
      10 => "Immobiliser"
      11 => "Merkezi Kilit"
      12 => "TCS"
      13 => "Yokuş Kalkış Desteği"
    ]
    "İç Donanım" => array:16 [▶]
    "Dış Donanım" => array:9 [▶]
    "Multimedya" => array:3 [▶]
  ]
]
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [ANILKEN](https://github.com/anilken)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
