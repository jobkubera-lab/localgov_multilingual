<?php

namespace Drupal\Tests\localgov_multilingual\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * Tests the available translations resolver.
 *
 * @group localgov_multilingual
 */
final class AvailableTranslationsResolverTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'text',
    'node',
    'language',
    'content_translation',
    'localgov_multilingual',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installSchema('node', ['node_access']);
    $this->installConfig(['language', 'content_translation']);

    NodeType::create([
      'type' => 'page',
      'name' => 'Page',
    ])->save();
  }

  /**
   * English-only content exposes only its source language.
   */
  public function testSourceLanguageOnly(): void {
    $node = Node::create([
      'type' => 'page',
      'title' => 'Council tax support',
      'langcode' => 'en',
    ]);
    $node->save();

    $languages = $this->container
      ->get('localgov_multilingual.available_translations_resolver')
      ->resolve($node);

    $this->assertSame(['en'], array_keys($languages));
  }

  /**
   * A genuine content translation is exposed alongside the source language.
   */
  public function testExistingTranslationIsAvailable(): void {
    $language_storage = $this->container
      ->get('entity_type.manager')
      ->getStorage('configurable_language');
    $language_storage->create(['id' => 'cy'])->save();

    $node = Node::create([
      'type' => 'page',
      'title' => 'Council tax support',
      'langcode' => 'en',
    ]);
    $node->addTranslation('cy', [
      'title' => 'Cymorth treth gyngor',
    ]);
    $node->save();

    $languages = $this->container
      ->get('localgov_multilingual.available_translations_resolver')
      ->resolve($node);

    $this->assertSame(['en', 'cy'], array_keys($languages));
  }

}
