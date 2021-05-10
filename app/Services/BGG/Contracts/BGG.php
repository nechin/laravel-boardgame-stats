<?php

namespace App\Services\BGG\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface BGG
 * @package App\Services\BGG\Contracts
 */
interface BGG
{
    /**
     * @param string $userName
     * @return Collection
     */
    public function getUserCollection(string $userName): Collection;

    /**
     * @param string $userName
     * @return Collection
     */
    public function getUserPlays(string $userName): Collection;
}
