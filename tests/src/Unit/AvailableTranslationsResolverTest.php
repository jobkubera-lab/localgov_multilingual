<?php

namespace Drupal\Tests\localgov_multilingual\Unit;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\localgov_multilingual\AvailableTranslationsResolver;
use Drupal\Tests\UnitTestCase;

/**
 * Tests the available translations resolver.
 *
 * @group localgov_multilingual
 */
final class AvailableTranslationsResolverTest extends UnitTestCase {

  /**
   * English-only content exposes only its original/source language.
   */
  public function testSourceLanguageOnly(): void {
    $english = $this->createLanguage('en');
    $welsh = $this->createLanguage('cy');

    $language_manager = $this->createMock(LanguageManagerInterface::class);
    $language_manager->expects($this->once())
      ->method('getLanguages')
      ->with(LanguageInterface::STATE_CONFIGURABLE)
      ->willReturn([
        'en' => $english,
        'cy' => $welsh,
      ]);

    $source_entity = $this->createMock(ContentEntityInterface::class);
    $source_entity->method('language')->willReturn($english);

    $entity = $this->createMock(ContentEntityInterface::class);
    $entity->method('getUntranslated')->willReturn($source_entity);
    $entity->expects($this->once())
      ->method('hasTranslation')
      ->with('cy')
      ->willReturn(FALSE);

    $resolver = new AvailableTranslationsResolver($language_manager);

    $this->assertSame(['en'], array_keys($resolver->resolve($entity)));
  }

  /**
   * Existing non-publishable translations are exposed with the source.
   */
  public function testExistingTranslationIsAvailable(): void {
    $english = $this->createLanguage('en');
    $welsh = $this->createLanguage('cy');

    $language_manager = $this->createMock(LanguageManagerInterface::class);
    $language_manager->method('getLanguages')
      ->with(LanguageInterface::STATE_CONFIGURABLE)
      ->willReturn([
        'en' => $english,
        'cy' => $welsh,
      ]);

    $source_entity = $this->createMock(ContentEntityInterface::class);
    $source_entity->method('language')->willReturn($english);

    $translation = $this->createMock(ContentEntityInterface::class);

    $entity = $this->createMock(ContentEntityInterface::class);
    $entity->method('getUntranslated')->willReturn($source_entity);
    $entity->expects($this->once())
      ->method('hasTranslation')
      ->with('cy')
      ->willReturn(TRUE);
    $entity->expects($this->once())
      ->method('getTranslation')
      ->with('cy')
      ->willReturn($translation);

    $resolver = new AvailableTranslationsResolver($language_manager);

    $this->assertSame(['en', 'cy'], array_keys($resolver->resolve($entity)));
  }

  /**
   * Creates a mocked language object.
   */
  private function createLanguage(string $langcode): LanguageInterface {
    $language = $this->createMock(LanguageInterface::class);
    $language->method('getId')->willReturn($langcode);
    return $language;
  }

}
