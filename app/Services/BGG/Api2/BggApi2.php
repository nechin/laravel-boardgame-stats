<?php

namespace App\Services\BGG\Api2;

use App\Services\BGG\Api2\Entity\Play;
use App\Services\BGG\Api2\Items\Plays;
use App\Services\BGG\Contracts\BaseBGG;
use Exception;
use Illuminate\Support\Collection;
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
        $parameters = [
            'username' => $userName
        ];
        $item = new Plays($parameters);
        try {
            $plays = collect([]);
            $items = $item->getResult();

            foreach ($items as $item) {
                /** @var SimpleXMLElement $item */
                $thing = new Play($item);
                $plays->push([
                    'id' => $thing->getId(),
                    'name' => $thing->getName(),
                    'date' => $thing->getDate(),
                ]);
            }

            return $plays;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
