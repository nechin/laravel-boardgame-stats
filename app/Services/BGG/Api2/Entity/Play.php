<?php

namespace App\Services\BGG\Api2\Entity;

/**
 * Class Play
 * @package App\Services\BGG\Api2\Entity
 */
class Play extends Element
{
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
    public function getGameId(): string
    {
        return (string)$this->root->item['objectid'];
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return strval($this->root['date']);
    }

    /**
     * @return string
     */
    public function getQuantity(): string
    {
        return strval($this->root['quantity']);
    }
}
