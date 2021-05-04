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
    /**
     * @var array
     */
    protected array $parameters;

    /**
     * Thing constructor.
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getApiUri(): string
    {
        return config('services.bgg.path') . $this->getUri();
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

        return $xml;
    }

    /**
     * @return string
     */
    abstract public function getUri(): string;
}
