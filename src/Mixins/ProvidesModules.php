<?php

namespace Weble\ZohoBackstageApi\Mixins;

use Weble\ZohoBackstageApi\Modules\Module;

trait ProvidesModules
{
    public function createModule($name): Module
    {
        if ($this->hasModule($name)) {
            $class =  $this->availableModules[$name];
            return new $class($this->client);
        }
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