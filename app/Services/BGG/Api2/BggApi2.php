<?php

namespace App\Services\BGG\Api2;

use App\Services\BGG\Api2\Contracts\Item;
use App\Services\BGG\Api2\Entity\Collection as Game;
use App\Services\BGG\Api2\Entity\Play;
use App\Services\BGG\Api2\Items\Collections;
use App\Services\BGG\Api2\Items\Plays;
use App\Services\BGG\Contracts\BaseBGG;
use Carbon\Carbon;
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
    private string $dateFormat = 'Y-m-d';
    private Collection $plays;
    private Collection $games;
    private array $playsByMonth;
    private int $cacheSeconds = 3600;

    /**
     * @param string $userName
     * @return Collection
     * @throws Exception
     */
    public function getUserPlays(string $userName): Collection
    {
        $cacheKey = 'bgg:plays:' . $userName;
        if (Cache::has($cacheKey)) {
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

                foreach ($elements as $item) {
                    /** @var SimpleXMLElement $item */
                    $play = new Play($item);
                    $plays->push([
                        'id' => $play->getId(),
                        'gameId' => $play->getGameId(),
                        'date' => $play->getDate(),
                    ]);
                }

                $page++;
            } while ($elements && $page < $totalPages);

            if ($plays->count()) {
                Cache::put($cacheKey, $plays, $this->cacheSeconds);
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
    public function getUserCollections(string $userName): Collection
    {
        $cacheKey = 'bgg:collection:' . $userName;
        if (Cache::has($cacheKey)) {
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
                ]);
            }

            if ($games->count()) {
                Cache::put($cacheKey, $games, $this->cacheSeconds);
            }

            return $games;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param string $userName
     * @return Collection
     * @throws Exception
     */
    public function getUserPlaysStat(string $userName): Collection
    {
        try {
            $stats = [
                'headers' => [],
                'items' => [],
            ];

            $this->plays = $this->getUserPlays($userName);
            $this->games = $this->getUserCollections($userName);

            if (empty($this->plays) || empty($this->games)) {
                return collect($stats);
            }

            $this->playsByMonth = $this->fillPlaysByMonth();
            $stats['headers'] = $this->fillHeaders();
            $stats['items'] = $this->fillItems($stats['headers']);

            return collect($stats);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @return array
     */
    private function fillPlaysByMonth(): array
    {
        $data = [];

        foreach ($this->plays as $play) {
            $date = Carbon::createFromFormat($this->dateFormat, $play['date']);

            if (isset($data[$date->year][$date->format('m')][$play['gameId']])) {
                $data[$date->year][$date->format('m')][$play['gameId']]++;
            } else {
                $data[$date->year][$date->format('m')][$play['gameId']] = 1;
            }
        }

        return $data;
    }

    /**
     * @return Collection
     */
    private function fillHeaders(): Collection
    {
        if (empty($this->playsByMonth)) {
            return collect([]);
        }

        $data = collect(['']);

        ksort($this->playsByMonth);

        foreach ($this->playsByMonth as $year => $monthPlays) {
            ksort($monthPlays);
            foreach ($monthPlays as $month => $plays) {
                $data->push([$year, $month]);
            }
        }

        return $data;
    }

    /**
     * @param Collection $headers
     * @return Collection
     */
    private function fillItems(Collection $headers): Collection
    {
        $data = collect([]);

        if (empty($this->playsByMonth)) {
            return $data;
        }

        foreach ($this->games as $game) {
            $row = [$game['name']];

            foreach ($headers as $key => $header) {
                if ($key) {
                    $row[] = $this->playsByMonth[$header[0]][$header[1]][$game['id']] ?? '';
                }
            }

            $data->push($row);
        }

        return $data;
    }
}
