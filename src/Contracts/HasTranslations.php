<?php


namespace Weble\ZohoBackstageApi\Contracts;


interface HasTranslations
{
    public function translatedFields(): array;

    public function availableLanguages(): array;

    public function setAvailableLanguages(array $languages);

    public function setDefaultLanguage(string $language);

    public function setTranslation(string $key, string $language, $value);

    public function getTranslation(string $key, string $language = null);

}