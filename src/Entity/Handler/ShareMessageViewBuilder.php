<?php

/**
 * @file
 * Definition of Drupal\sharemessage\Entity\Handler\ShareMessageViewBuilder.
 */

namespace Drupal\sharemessage\Entity\Handler;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Template\Attribute;
use Drupal\sharemessage\Entity\ShareMessage;

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

      $is_overridden = \Drupal::request()->query->get('smid') && \Drupal::config('sharemessage.settings')->get('message_enforcement');

      // Add OG Tags to the page.
      $addThis_attributes = FALSE;
      if (!$is_overridden && empty($context['_force_attributes'])) {
        $build[$entity->id()]['#attached']['html_head'] = $this->mapHeadElements($entity->buildOGTags($context));
      }
      else {
        $addThis_attributes = TRUE;
      }
      unset($context['_force_attributes']);

      $attributes = new Attribute(array('class' => array(
        'addthis_toolbox',
        'addthis_default_style',
        !empty($entity->settings['icon_style']) ? $entity->settings['icon_style'] : \Drupal::config('sharemessage.settings')->get('icon_style'),
      )));

      if ($addThis_attributes) {
        $attributes['addthis:url'] = $entity->getUrl($context);
        $attributes['addthis:title'] = $entity->getTokenizedField($entity->title, $context);
        $attributes['addthis:description'] = $entity->getTokenizedField($entity->message_long, $context);
      }

      // Add addThis buttons.
      if ($view_mode == 'full') {
        $build[$entity->id()]['addthis'] = array(
          '#theme' => 'sharemessage_addthis',
          '#attributes' => $attributes,
          '#cache' => [
            'tags' => $entity->getCacheTags(),
          ],
          '#services' => !empty($entity->settings['services']) ? $entity->settings['services'] : \Drupal::config('sharemessage.settings')->get('services'),
          '#additional_services' =>  isset($entity->settings['additional_services']) ? $entity->settings['additional_services'] : \Drupal::config('sharemessage.settings')->get('additional_services'),
          '#counter' => isset($entity->settings['counter']) ? $entity->settings['counter'] : \Drupal::config('sharemessage.settings')->get('counter'),
          '#attached' => array(
            'library' => ['sharemessage/addthis'],
            'drupalSettings' => array(
              'addthis_config' => array(
                'data_track_addressbar' => TRUE,
              ),
            ),
          ),
        );
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
