<?php

namespace App\Services\BGG\Api2\Items;

use App\Services\BGG\Api2\Contracts\Item;

/**
 * Class Plays
 * @package App\Services\BGG\Api2\Items
 */
class Plays extends Item
{
    /**
     * @return string
     */
    public function getUri(): string
    {
        return 'plays' . $this->getParameters();
    }
}
