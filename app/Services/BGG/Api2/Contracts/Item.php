<?php

namespace App\Services\BGG\Api2\Contracts;

use Exception;
use SimpleXMLElement;

/**
 * Class Item
 * @package App\Services\BGG\Api2\Contracts
 */
abstract class Item
{
    const TYPE_THING = 'thing';
    const TYPE_FAMILY = 'family';

    const THING_TYPE_BG = 'boardgame';
    const THING_TYPE_BG_EXPANSION = 'boardgameexpansion';
    const THING_TYPE_BG_ACCESSORY = 'boardgameaccessory';
    const THING_TYPE_VIDEO_GAME = 'videogame';
    const THING_TYPE_VIDEO_RPG_ITEM = 'rpgitem';
    const THING_TYPE_VIDEO_RPG_ISSUE = 'rpgissue';

    /**
     * @var array
     */
    protected array $parameters;

    /**
     * @return string
     */
    public function getApiUri(): string
    {
        return config('services.bgg.path') . $this->getUri();
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getParameters(): string
    {
        return $this->parameters ? '?' . http_build_query($this->parameters) : '';
    }

    /**
     * @return SimpleXMLElement
     * @throws Exception
     */
    public function getResult(): SimpleXMLElement
    {
        $xml = simplexml_load_file($this->getApiUri());

        if (!$xml instanceof SimpleXMLElement) {
            throw new Exception('API call failed');
        }

        if ($xml->error) {
            throw new Exception($xml->error->message);
        }

        if (strpos($xml, 'Your request for this collection') !== false) {
            throw new Exception('Ваш запрос принят и скоро будет обработан. Нажмите кнопку "Показать" ещё раз.');
        }

        return $xml;
    }

    /**
     * @return string
     */
    abstract public function getUri(): string;
}
