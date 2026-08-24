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
   * English-only content exposes only its source language.
   */
  public function testSourceLanguageOnly(): void {
    $english = $this->createLanguage('en');
    $welsh = $this->createLanguage('cy');

    $language_manager = $this->createMock(LanguageManagerInterface::class);
    $language_manager->method('getLanguages')->willReturn([
      'en' => $english,
      'cy' => $welsh,
    ]);

    $entity = $this->createMock(ContentEntityInterface::class);
    $entity->method('language')->willReturn($english);
    $entity->method('hasTranslation')->with('cy')->willReturn(FALSE);

    $resolver = new AvailableTranslationsResolver($language_manager);

    $this->assertSame(['en'], array_keys($resolver->resolve($entity)));
  }

  /**
   * Existing translations are exposed alongside the source language.
   */
  public function testExistingTranslationIsAvailable(): void {
    $english = $this->createLanguage('en');
    $welsh = $this->createLanguage('cy');

    $language_manager = $this->createMock(LanguageManagerInterface::class);
    $language_manager->method('getLanguages')->willReturn([
      'en' => $english,
      'cy' => $welsh,
    ]);

    $entity = $this->createMock(ContentEntityInterface::class);
    $entity->method('language')->willReturn($english);
    $entity->method('hasTranslation')->with('cy')->willReturn(TRUE);

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
