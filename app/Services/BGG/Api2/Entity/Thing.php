<?php

namespace App\Services\BGG\Api2\Entity;

use SimpleXMLElement;

/**
 * Class Thing
 * @package App\Services\BGG\Api2\Entity
 */
class Thing
{
    const LANGUAGE_LEVEL_NO_NECESSARY_TEXT = 1;     // No necessary in-game text
    const LANGUAGE_LEVEL_SOME_NECESSARY_TEXT = 2;   // Some necessary text - easily memorized or small crib sheet
    const LANGUAGE_LEVEL_MODERATE_TEXT = 3;         // Moderate in-game text - needs crib sheet or paste ups
    const LANGUAGE_LEVEL_EXTENSIVE_USE = 4;         // Extensive use of text - massive conversion needed to be playable
    const LANGUAGE_LEVEL_UNPLAYABLE = 5;            // Unplayable in another language

    const TYPE_BOARDGAME = 'boardgame';
    const TYPE_BOARDGAMEEXPANSION = 'boardgameexpansion';

    private SimpleXMLElement $root;

    public function __construct(SimpleXMLElement $xml)
    {
        $this->root = $xml;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return (int)$this->root['id'];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return (string)$this->root['type'];
    }

    /**
     * @return bool
     */
    public function isBoardgameExpansion(): bool
    {
        return $this->getType() == self::TYPE_BOARDGAMEEXPANSION;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->root->name['value'];
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return (string)$this->root->description;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return (string)$this->root->image;
    }

    /**
     * @return string
     */
    public function getThumbnail(): string
    {
        return (string)$this->root->thumbnail;
    }

    /**
     * @return int
     */
    public function getYearPublished(): int
    {
        return (int)$this->root->yearpublished['value'];
    }

    /**
     * @return int
     */
    public function getMinPlayers(): int
    {
        return (int)$this->root->minplayers['value'];
    }

    /**
     * @return int
     */
    public function getMaxPlayers(): int
    {
        return (int)$this->root->maxplayers['value'];
    }

    /**
     * @return int
     */
    public function getPlayingTime(): int
    {
        return (int)$this->root->playingtime['value'];
    }

    /**
     * @return int
     */
    public function getMinPlayTime(): int
    {
        return (int)$this->root->minplaytime['value'];
    }

    /**
     * @return int
     */
    public function getMaxPlayTime(): int
    {
        return (int)$this->root->maxplaytime['value'];
    }

    /**
     * @return int
     */
    public function getMinAge(): int
    {
        return (int)$this->root->minage['value'];
    }

    /**
     * @return array
     */
    public function getBoardgameCategories(): array
    {
        $values = [];
        $xml = $this->root->xpath("link[@type='boardgamecategory']");
        foreach ($xml as $element) {
            $values[] = (string)$element['value'];
        }

        return $values;
    }

    /**
     * @return array
     */
    public function getBoardgameMechanics(): array
    {
        $values = [];
        $xml = $this->root->xpath("link[@type='boardgamemechanic']");
        foreach ($xml as $element) {
            $values[] = (string)$element['value'];
        }

        return $values;
    }

    /**
     * @return array
     */
    public function getBoardgameDesigners(): array
    {
        $values = [];
        $xml = $this->root->xpath("link[@type='boardgamedesigner']");
        foreach ($xml as $element) {
            $values[] = (string)$element['value'];
        }

        return $values;
    }

    /**
     * @return array
     */
    public function getBoardgameArtists(): array
    {
        $values = [];
        $xml = $this->root->xpath("link[@type='boardgameartist']");
        foreach ($xml as $element) {
            $values[] = (string)$element['value'];
        }

        return $values;
    }

    /**
     * @return array
     */
    public function getBoardgamePublishers(): array
    {
        $values = [];
        $xml = $this->root->xpath("link[@type='boardgamepublisher']");
        foreach ($xml as $element) {
            $values[] = (string)$element['value'];
        }

        return $values;
    }

    /**
     * @return array
     */
    public function getBoardgameExpansions(): array
    {
        $values = [];
        $xml = $this->root->xpath("link[@type='boardgameexpansion']");
        foreach ($xml as $element) {
            if ($element['inbound'] != 'true') {
                $values[] = (int)$element['id'];
            }
        }

        return $values;
    }

    /**
     * @return float
     */
    public function getRatingAverage(): float
    {
        return round((float)$this->root->statistics->ratings->average['value'], 1);
    }

    /**
     * @return float
     */
    public function getWeightAverage(): float
    {
        return round((float)$this->root->statistics->ratings->averageweight['value'], 1);
    }

    /**
     * @return array
     */
    public function getAlternateNames(): array
    {
        $names = [];
        $xml = $this->root->xpath("name[@type='alternate']");
        foreach ($xml as $element) {
            $names[] = (string)$element['value'];
        }

        return $names;
    }

    /**
     * @return int
     */
    public function getLanguageDependenceLevel(): ?int
    {
        $level = null;
        if ($xml = $this->root->xpath("poll[@name='language_dependence']/results/result")) {

            $maxVotes = 0;
            foreach ($xml as $element) {
                if ((int)$element['numvotes'] > $maxVotes) {
                    $level = (int)$element['level'];
                }
            }

        }

        return $level;
    }

    /**
     * @return int
     */
    /*public function getBoardgameBasegame(): ?int
    {
        $xml = $this->root->xpath("link[@type='boardgameexpansion'][@inbound='true']");
        while(list( , $node) = each($xml)) {
            return (int)$node['id'];
        }

        return null;
    }*/
}
