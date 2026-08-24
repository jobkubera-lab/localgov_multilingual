<?php

namespace Drupal\localgov_multilingual;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Resolves languages that have real translations for content entities.
 */
final class AvailableTranslationsResolver {

  /**
   * Constructs the resolver.
   */
  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Returns available languages for an entity, keyed by language code.
   *
   * The source language is always returned. Other languages are returned only
   * when the entity reports that an actual translation exists.
   *
   * @return \Drupal\Core\Language\LanguageInterface[]
   *   Available languages keyed by language code.
   */
  public function resolve(ContentEntityInterface $entity): array {
    $available = [];
    $source_language = $entity->language();
    $available[$source_language->getId()] = $source_language;

    foreach ($this->languageManager->getLanguages() as $langcode => $language) {
      if ($langcode === $source_language->getId()) {
        continue;
      }

      if ($entity->hasTranslation($langcode)) {
        $available[$langcode] = $language;
      }
    }

    return $available;
  }

}
