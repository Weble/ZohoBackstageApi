<?php

namespace Weble\ZohoBackstageApi\Mixins;

use Weble\ZohoBackstageApi\Client;
use Weble\ZohoBackstageApi\Modules\Module;

/**
 * Trait ProvidesModules
 * @package Weble\ZohoBackstageApi\Mixins
 *
 * @property Client $client
 * @property array  $availableModules
 */
trait ProvidesModules
{
    public function createModule($name): ?Module
    {
        if ($this->hasModule($name)) {
            $class =  $this->availableModules[$name];
            return new $class($this->oAuthClient);
        }

        return null;
    }

    public function getAvailableModules(): array
    {
        return $this->availableModules;
    }

    public function hasModule($name): bool
    {
        return isset($this->getAvailableModules()[$name]);
    }
}