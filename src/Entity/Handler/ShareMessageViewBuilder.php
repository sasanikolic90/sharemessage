<?php

/**
 * @file
 * Definition of Drupal\sharemessage\Entity\Handler\ShareMessageViewBuilder.
 */

namespace Drupal\sharemessage\Entity\Handler;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Render controller for nodes.
 */
class ShareMessageViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $build = $this->viewMultiple(array($entity), $view_mode, $langcode);
    return reset($build);
  }

  /**
   * {@inheritdoc}
   */
  public function viewMultiple(array $entities = array(), $view_mode = 'full', $langcode = NULL) {
    if (empty($entities)) {
      return array();
    }

    $build = array();
    foreach ($entities as $entity) {
      /* @var \Drupal\sharemessage\Entity\ShareMessage $entity */

      // EntityViewController expects the entity to be in #sharemessage.
      $build[$entity->id()]['#sharemessage'] = $entity;

      $context = $entity->getContext();

      $is_overridden = \Drupal::request()->query->get('smid') && \Drupal::config('sharemessage.addthis')->get('message_enforcement');

      // Add OG Tags to the page.
      $plugin_attributes = FALSE;
      if (!$is_overridden && empty($context['_force_attributes'])) {
        $build[$entity->id()]['#attached']['html_head'] = $this->mapHeadElements($entity->buildOGTags($context));
      }
      else {
        $plugin_attributes = TRUE;
      }
      unset($context['_force_attributes']);

      if ($entity->hasPlugin() && $view_mode == 'full') {
        $build[$entity->id()][$entity->getPluginID()] = $entity->getPlugin()->build($context, $plugin_attributes);
      }
    }
    return $build;
  }

  /**
   * Modifies a buildOGTags structure to work with drupal_add_html_head.
   */
  protected function mapHeadElements(array $elements) {
    $mapped = array();
    foreach ($elements as $element) {
      $mapped[] = array(
        $element,
        str_replace(':', '_', $element['#attributes']['property']),
      );
    }
    return $mapped;
  }

}
