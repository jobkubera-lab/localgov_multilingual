<?php

namespace Drupal\localgov_multilingual;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Resolves languages that have translations for a content entity.
 */
final class AvailableTranslationsResolver {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  private $languageManager;

  /**
   * Constructs the resolver.
   */
  public function __construct(LanguageManagerInterface $language_manager) {
    $this->languageManager = $language_manager;
  }

  /**
   * Returns available languages for an entity, keyed by language code.
   *
   * The source language is always returned. Other enabled languages are
   * returned only when the entity reports that a translation exists.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The content entity to inspect.
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
