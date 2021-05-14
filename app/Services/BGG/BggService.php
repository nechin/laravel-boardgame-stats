<?php

namespace App\Services\BGG;

use App\Services\BGG\Contracts\BGG;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class BggService
 * @package App\Services\BGG
 */
class BggService
{
    private string $dateFormat = 'Y-m-d';
    private Collection $plays;
    private Collection $games;
    private array $playsByMonth;
    private array $statistics;
    private BGG $bggApi;

    /**
     * Bgg constructor.
     */
    public function __construct()
    {
        $this->bggApi = app(BGG::class);
        $this->statistics = [
            'headers' => [],
            'items' => [],
            'props' => [],
        ];
    }

    /**
     * @param string $userName
     * @return Collection
     * @throws Exception
     */
    public function getUserPlaysStat(string $userName): Collection
    {
        try {
            $this->plays = $this->bggApi->getUserPlays($userName);
            $this->games = $this->bggApi->getUserCollection($userName);

            if (empty($this->plays) || empty($this->games)) {
                return collect($this->statistics);
            }

            $this->fillPlaysByMonth();
            $this->fillStats();

            return collect($this->statistics);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    private function fillPlaysByMonth(): void
    {
        $this->playsByMonth = [];

        foreach ($this->plays as $play) {
            $date = Carbon::createFromFormat($this->dateFormat, $play['date']);

            // Если игра есть в коллекции
            if ($this->games->firstWhere('id', $play['gameId'])) {
                if (isset($this->playsByMonth[$date->year][$date->format('m')][$play['gameId']])) {
                    $this->playsByMonth[$date->year][$date->format('m')][$play['gameId']] += $play['count'];
                } else {
                    $this->playsByMonth[$date->year][$date->format('m')][$play['gameId']] = $play['count'];
                }
            }
        }
    }

    private function fillStats(): void
    {
        if (empty($this->playsByMonth)) {
            return;
        }

        $headers = collect();
        $headers->push(['', 'Название']);
        $headers->push(['Всего', '']);

        $items = [];
        $props = [];

        ksort($this->playsByMonth);

        $headersNotFilled = true;

        foreach ($this->games as $game) {
            $itemRow = [$game['name'], $game['numPlays']];
            $notPlayedPeriod = [
                'year' => false,
                'half' => false,
            ];
            foreach ($this->playsByMonth as $year => $monthPlays) {
                ksort($monthPlays);
                foreach ($monthPlays as $month => $plays) {
                    $playCount = $plays[$game['id']] ?? '';
                    $itemRow[] = $playCount;

                    if ($playCount) {
                        $date = Carbon::create($year, $month);
                        $now = Carbon::now();
                        $diffInYears = $date->diffInYears($now);
                        $diffInMonths = $date->diffInMonths($now);
                        $notPlayedPeriod['year'] = $diffInYears > 0;
                        $notPlayedPeriod['half'] = $diffInMonths > 6;
                    }

                    if ($headersNotFilled) {
                        $headers->push([$year, $month]);
                    }
                }
            }

            $items[] = $itemRow;
            $props[] = $notPlayedPeriod;
            $headersNotFilled = false;
        }

        $this->statistics['headers'] = $headers;
        $this->statistics['items'] = collect($items);
        $this->statistics['props'] = collect($props);
    }
}
