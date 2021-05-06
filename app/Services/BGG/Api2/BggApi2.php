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
use SimpleXMLElement;

/**
 * Class BggApi2
 * @package App\Services\BGG\Api2
 */
class BggApi2 extends BaseBGG
{
    private string $dateFormat = 'Y-m-d';

    /**
     * @param string $userName
     * @return Collection
     * @throws Exception
     */
    public function getUserPlays(string $userName): Collection
    {
        $parameters = [
            'username' => $userName,
            'type' => Item::TYPE_THING
        ];

        $element = new Plays();
        try {
            $page = 1;
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
            } while ($elements && $page < 15);

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

            $plays = $this->getUserPlays($userName);
            $games = $this->getUserCollections($userName);

            if (empty($plays) || empty($games)) {
                return collect($stats);
            }

            $playsByMonth = $this->fillPlaysByMonth($plays);
            $stats['headers'] = $this->fillHeaders($playsByMonth);
            $stats['items'] = $this->fillItems($playsByMonth, $stats['headers'], $games);

            return collect($stats);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param Collection $plays
     * @return array
     */
    private function fillPlaysByMonth(Collection $plays): array
    {
        $data = [];

        foreach ($plays as $play) {
            $date = Carbon::createFromFormat($this->dateFormat, $play['date']);

            if (isset($data[$date->year][$date->month][$play['gameId']])) {
                $data[$date->year][$date->month][$play['gameId']]++;
            } else {
                $data[$date->year][$date->month][$play['gameId']] = 1;
            }
        }

        return $data;
    }

    /**
     * @param array $playsByMonth
     * @return Collection
     */
    private function fillHeaders(array $playsByMonth): Collection
    {
        if (empty($playsByMonth)) {
            return collect([]);
        }

        $data = collect(['Название']);

        ksort($playsByMonth);

        foreach ($playsByMonth as $year => $monthPlays) {
            ksort($monthPlays);
            foreach ($monthPlays as $month => $plays) {
                $data->push($year . '-' . $month);
            }
        }

        return $data;
    }

    /**
     * @param array $playsByMonth
     * @param Collection $headers
     * @param Collection $games
     * @return Collection
     */
    private function fillItems(array $playsByMonth, Collection $headers, Collection $games): Collection
    {
        $data = collect([]);

        if (empty($playsByMonth)) {
            return $data;
        }

        foreach ($games as $game) {
            $row = [$game['name']];

            foreach ($headers as $key => $header) {
                if ($key) {
                    $dates = explode('-', $header);
                    $row[] = $playsByMonth[$dates[0]][$dates[1]][$game['id']] ?? '';
                }
            }

            $data->push($row);
        }

        return $data;
    }
}
