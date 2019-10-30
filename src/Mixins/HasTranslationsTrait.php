<?php


namespace Weble\ZohoBackstageApi\Mixins;

trait HasTranslationsTrait
{
    protected $availableLanguages = [];

    protected $defaultLanguage = 'en';


    public function setTranslationsFrom(array $listTranslations, $languageKey = 'eventLanguage'): self
    {
        $languageId = $listTranslations[$languageKey];
        $language = $this->availableLanguages[$languageId] ?? $this->defaultLanguage();
        foreach ($listTranslations as $key => $value) {
            if (in_array($key, $this->translatedFields())) {
                $this->setTranslation($key, $language, $value);
            }
        }

        return $this;
    }

    public function setTranslation(string $key, string $language, $value): self
    {
        if (!in_array($key, $this->translatedFields())) {
            return $this;
        }

        if (!isset($this->data[$key]) || !is_array($this->data[$key])) {
            $this->data[$key] = [];
        }

        $this->data[$key][$language] = $value;
        return $this;
    }

    public function getTranslation(string $key, string $language = null)
    {
        if ($language === null || !in_array($language, $this->availableLanguages())) {
            $language = $this->defaultLanguage();
        }

        return $this->data[$key][$language];
    }

    public function setAvailableLanguages(array $languages = []): self
    {
        $this->availableLanguages = $languages;
        return $this;
    }

    public function setDefaultLanguage(string $language): self
    {
        $this->defaultLanguage = $language;
        return $this;
    }

    public function availableLanguages(): array
    {
        return $this->availableLanguages;
    }

    public function defaultLanguage(): string
    {
        return $this->defaultLanguage;
    }

}