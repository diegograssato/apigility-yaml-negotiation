<?php

namespace ZF\ContentNegotiation\YAML\View;

use ZF\Hal\Collection;
use ZF\Hal\Entity;
use ZF\ContentNegotiation\ViewModel;

/**
 * Simple extension to facilitate the specialized YamlStrategy and YamlRenderer
 * in this Module.
 */
class YamlModel extends ViewModel
{
    /**
     * @var bool
     */
    protected $terminate = true;

    /**
     * Does the payload represent a HAL collection?
     *
     * @return bool
     */
    public function isCollection()
    {
        $payload = $this->getPayload();

        return $payload instanceof Collection;
    }

    /**
     * Does the payload represent a HAL entity?
     *
     * @return bool
     */
    public function isEntity()
    {
        $payload = $this->getPayload();

        return $payload instanceof Entity;
    }

    /**
     * Set the payload for the response.
     *
     * This is the value to represent in the response.
     *
     * @param mixed $payload
     *
     * @return self
     */
    public function setPayload($payload)
    {
        $this->setVariable('payload', $payload);

        return $this;
    }

    /**
     * Retrieve the payload for the response.
     *
     * @return mixed
     */
    public function getPayload()
    {
        return $this->getVariable('payload');
    }

    /**
     * Override setTerminal().
     *
     * Does nothing; does not allow re-setting "terminate" flag.
     *
     * @param bool $flag
     *
     * @return self
     */
    public function setTerminal($flag = true)
    {
        return $this;
    }
}
