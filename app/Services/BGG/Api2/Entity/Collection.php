<?php

namespace App\Services\BGG\Api2\Entity;

/**
 * Class Collection
 * @package App\Services\BGG\Api2\Entity
 */
class Collection extends Element
{
    /**
     * @return int
     */
    public function getId(): int
    {
        return (int)$this->root['collid'];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->root->name;
    }

    /**
     * @return string
     */
    public function getNumPlays(): string
    {
        return (string)$this->root->numplays;
    }

    /**
     * @return string
     */
    public function getGameId(): string
    {
        return strval($this->root['objectid']);
    }
}
