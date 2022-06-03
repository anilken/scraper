<?php

namespace Anilken\Scraper\Exception;

class NotFoundException extends RequestException
{
    public function __construct($message = '', $code = 404, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
