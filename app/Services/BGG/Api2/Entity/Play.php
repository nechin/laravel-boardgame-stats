<?php

namespace App\Services\BGG\Api2\Entity;

use SimpleXMLElement;

/**
 * Class Play
 * @package App\Services\BGG\Api2\Entity
 */
class Play
{
    private SimpleXMLElement $root;

    public function __construct(SimpleXMLElement $xml)
    {
        $this->root = $xml;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int)$this->root['id'];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->root->item['name'];
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return (string)$this->root['date'];
    }
}
