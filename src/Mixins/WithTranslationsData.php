<?php


namespace Weble\ZohoBackstageApi\Mixins;


use Tightenco\Collect\Support\Collection;

trait WithTranslationsData
{
    protected function extractLanguagesFrom(array $list, string $key): array
    {
        return (new Collection($list[$key]))->mapWithKeys(function ($item) {
            return [$item['id'] => $item['language']];
        })->toArray();
    }

    protected function extractDefaultLanguageFrom(array $list, string $key): string
    {
        foreach ($list[$key] as $item) {
            if ($item['isDefault']) {
                return $item['language'];
            }
        }

        return 'en';
    }
}