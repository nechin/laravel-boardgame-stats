<?php

namespace App\Services\BGG\Api2\Entity;

use SimpleXMLElement;

class Element
{
    protected SimpleXMLElement $root;

    public function __construct(SimpleXMLElement $xml)
    {
        $this->root = $xml;
    }
}
