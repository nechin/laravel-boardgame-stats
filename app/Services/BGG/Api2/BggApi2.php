<?php

namespace App\Services\BGG\Api2;

use App\Services\BGG\Api2\Contracts\Item;
use App\Services\BGG\Api2\Entity\Collection as Game;
use App\Services\BGG\Api2\Entity\Play;
use App\Services\BGG\Api2\Items\Collections;
use App\Services\BGG\Api2\Items\Plays;
use App\Services\BGG\Contracts\BaseBGG;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use SimpleXMLElement;

/**
 * Class BggApi2
 * @package App\Services\BGG\Api2
 */
class BggApi2 extends BaseBGG
{
    /**
     * @param string $userName
     * @return Collection
     * @throws Exception
     */
    public function getUserPlays(string $userName): Collection
    {
        $cacheKey = 'bgg:plays:' . $userName;
        if (config('services.bgg.cache_enabled') && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $parameters = [
            'username' => $userName,
            'type' => Item::TYPE_THING
        ];

        $element = new Plays();
        try {
            $page = 1;
            $totalPages = round(config('services.bgg.plays_count') / 100);
            $plays = collect([]);
            do {
                $parameters['page'] = $page;
                $element->setParameters($parameters);
                $elements = $element->getResult();

                if ($elements->attributes()->total == 0) {
                    break;
                }

                foreach ($elements as $item) {
                    /** @var SimpleXMLElement $item */
                    $play = new Play($item);
                    $plays->push([
                        'id' => $play->getId(),
                        'gameId' => $play->getGameId(),
                        'date' => $play->getDate(),
                        'count' => $play->getQuantity(),
                    ]);
                }

                $page++;
            } while ($page <= $totalPages);

            if ($plays->count()) {
                Cache::put($cacheKey, $plays, config('services.bgg.cache_seconds'));
            }

            return $plays;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param string $userName
     * @return Collection
     * @throws Exception
     */
    public function getUserCollection(string $userName): Collection
    {
        $cacheKey = 'bgg:collection:' . $userName;
        if (config('services.bgg.cache_enabled') && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $parameters = [
            'username' => $userName,
            'own' => 1,
            'excludesubtype' => Item::THING_TYPE_BG_EXPANSION,
        ];

        $element = new Collections();
        $element->setParameters($parameters);
        try {
            $games = collect([]);
            $elements = $element->getResult();

            foreach ($elements as $item) {
                /** @var SimpleXMLElement $item */
                $game = new Game($item);
                $games->push([
                    'id' => $game->getGameId(),
                    'name' => $game->getName(),
                    'numPlays' => $game->getNumPlays(),
                ]);
            }

            if ($games->count()) {
                Cache::put($cacheKey, $games, config('services.bgg.cache_seconds'));
            }

            return $games;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

}
