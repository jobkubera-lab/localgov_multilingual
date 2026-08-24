<?php

namespace Drupal\localgov_multilingual;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Resolves languages that have translations for a content entity.
 *
 * This service does not perform entity access checks. Callers that expose
 * links publicly should still check view access for the selected translation.
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
   * The entity's original/source language is always returned. Other
   * configurable languages are returned only when a translation exists and,
   * by default, that translation is published when publication state exists.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The content entity to inspect.
   * @param bool $include_unpublished
   *   Whether to include unpublished translations. Defaults to FALSE.
   *
   * @return \Drupal\Core\Language\LanguageInterface[]
   *   Available languages keyed by language code.
   */
  public function resolve(ContentEntityInterface $entity, bool $include_unpublished = FALSE): array {
    $available = [];
    $source_language = $entity->getUntranslated()->language();
    $available[$source_language->getId()] = $source_language;

    foreach ($this->languageManager->getLanguages(LanguageInterface::STATE_CONFIGURABLE) as $langcode => $language) {
      if ($langcode === $source_language->getId()) {
        continue;
      }

      if (!$entity->hasTranslation($langcode)) {
        continue;
      }

      $translation = $entity->getTranslation($langcode);
      if (!$include_unpublished && $translation instanceof EntityPublishedInterface && !$translation->isPublished()) {
        continue;
      }

      $available[$langcode] = $language;
    }

    return $available;
  }

}
