<?php

namespace App\Services\BGG\Contracts;

use Illuminate\Support\Collection;

/**
 * Class BaseBGG
 * @package App\Services\BGG\Contracts
 */
abstract class BaseBGG implements BGG
{
    /**
     * @param string $userName
     * @return Collection
     */
    abstract public function getUserPlays(string $userName): Collection;

    /**
     * @param string $userName
     * @return Collection
     */
    abstract public function getUserCollections(string $userName): Collection;

    /**
     * @param string $userName
     * @return Collection
     */
    abstract public function getUserPlaysStat(string $userName): Collection;
}
