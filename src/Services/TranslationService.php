<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Services;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\PhpFileLoader;

class TranslationService
{
    private Translator $translator;
    
    public function __construct(string $defaultLocale = 'en')
    {
        $this->translator = new Translator($defaultLocale);
        $this->translator->addLoader('php', new PhpFileLoader());
        
        // Register translation resources
        $this->registerTranslations();
    }
    
    /**
     * Register all available translation files
     */
    private function registerTranslations(): void
    {
        $languages = ['en', 'pl', 'de', 'es', 'fr'];
        $domains = ['messages', 'settings', 'notifications', 'privacy'];
        
        foreach ($languages as $lang) {
            foreach ($domains as $domain) {
                $path = __DIR__ . "/../../translations/{$lang}/{$domain}.php";
                if (file_exists($path)) {
                    $this->translator->addResource('php', $path, $lang, $domain);
                }
            }
        }
    }
    
    /**
     * Translate a message
     */
    public function trans(string $id, array $parameters = [], string $domain = 'messages', ?string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }
    
    /**
     * Set the current locale
     */
    public function setLocale(string $locale): void
    {
        $this->translator->setLocale($locale);
    }
    
    /**
     * Get the current locale
     */
    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }
} 