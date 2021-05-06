<?php

namespace App\Services\BGG\Api2\Items;

use App\Services\BGG\Api2\Contracts\Item;

/**
 * Class Collections
 * @package App\Services\BGG\Api2\Items
 */
class Collections extends Item
{
    /**
     * @return string
     */
    public function getUri(): string
    {
        return 'collection' . $this->getParameters();
    }
}
