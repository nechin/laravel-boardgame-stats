<?php

namespace App\Services\BGG;

use App\Services\BGG\Contracts\BGG;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

class BggService
{
    private string $dateFormat = 'Y-m-d';
    private Collection $plays;
    private Collection $games;
    private array $playsByMonth;
    private BGG $bggApi;

    /**
     * Bgg constructor.
     */
    public function __construct()
    {
        $this->bggApi = app(BGG::class);
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

            $this->plays = $this->bggApi->getUserPlays($userName);
            $this->games = $this->bggApi->getUserCollection($userName);

            if (empty($this->plays) || empty($this->games)) {
                return collect($stats);
            }

            $this->playsByMonth = $this->fillPlaysByMonth();
            $stats['headers'] = $this->fillHeaders();
            $stats['items'] = $this->fillItems();

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
                $data[$date->year][$date->format('m')][$play['gameId']] += $play['count'];
            } else {
                $data[$date->year][$date->format('m')][$play['gameId']] = $play['count'];
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

        $data = collect();
        $data->push(['', 'Название']);
        $data->push(['Всего', '']);

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
     * @return Collection
     */
    private function fillItems(): Collection
    {
        $data = [];

        if (empty($this->playsByMonth)) {
            return collect($data);
        }

        foreach ($this->games as $game) {
            $row = [$game['name'], $game['numPlays']];

            foreach ($this->playsByMonth as $year => $monthPlays) {
                ksort($monthPlays);
                foreach ($monthPlays as $month => $plays) {
                    $row[] = $this->playsByMonth[$year][$month][$game['id']] ?? '';
                }
            }

            $data[] = $row;
        }

        return collect($data);
    }
}
