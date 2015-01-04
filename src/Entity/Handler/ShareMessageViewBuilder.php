<?php

/**
 * @file
 * Definition of Drupal\sharemessage\Entity\Handler\ShareMessageViewBuilder.
 */

namespace Drupal\sharemessage\Entity\Handler;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;
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

      // Add addThis buttons.
      if ($view_mode == 'full') {
        $build[$entity->id()]['addthis'] = array(
          '#type' => 'container',
          '#attributes' => $addThis_attributes ? $this->buildAdditionalAttributes($entity, $context) : $this->buildAttributes($entity),
          'services' => array(
            '#markup' => $this->buildServicesPart($entity, $context),
          ),
          'additional_services' => array(
            '#markup' => $this->buildAdditionalServicesPart($entity),
          ),
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

  /**
   * Function that adds icon style as part of addThis widget.
   */
  private function buildAttributes(ShareMessage $entity) {
    $icon_style = !empty($entity->settings['icon_style']) ? $entity->settings['icon_style'] : \Drupal::config('sharemessage.settings')->get('icon_style');
    return array(
      'class' => array('addthis_toolbox', 'addthis_default_style', $icon_style),
    );
  }

  /**
   * Function that adds icon style with addThis:attributes
   * (url, title, description) as part of addThis widget.
   */
  function buildAdditionalAttributes(ShareMessage $entity, $context) {
    $attributes = $this->buildAttributes($entity);
    $attributes['addthis:url'] = $entity->getUrl($context);
    $attributes['addthis:title'] = $entity->getTokenizedField($entity->title, $context);
    $attributes['addthis:description'] = $entity->getTokenizedField($entity->message_long, $context);
    return $attributes;
  }

  /**
   * Function that adds services as part of addThis widget.
   */
  private function buildServicesPart(ShareMessage $entity, $context) {
    $services = !empty($entity->settings['services']) ? $entity->settings['services'] : \Drupal::config('sharemessage.settings')->get('services');

    // Configured.
    // @todo render this as render array and attach js using #attached property.
    $services_HTML = '';
    if (!empty($services)) {
      foreach ($services as $key => $service) {
        if ($key == 'twitter' && $entity->message_short) {
          $services_HTML .= "<script>
<!--//--><![CDATA[//><!-- var addthis_share = { templates: { twitter: '" . $entity->getTokenizedField($entity->message_short, $context) . " } } //--><!]]>
</script>";
        }
        $services_HTML .= '<a class="addthis_button_' . $key . '"></a>';
      }
    }
    else {
      // Default.
      $services_HTML .= '
        <a class="addthis_button_preferred_1"></a>
        <a class="addthis_button_preferred_2"></a>
        <a class="addthis_button_preferred_3"></a>
        <a class="addthis_button_preferred_4"></a>
        <a class="addthis_button_preferred_5"></a>';
    }

    return $services_HTML;
  }

  /**
   * Function that adds additional services as part of addThis widget.
   */
  private function buildAdditionalServicesPart($entity) {
    $additional_services = isset($entity->settings['additional_services']) ? $entity->settings['additional_services'] : \Drupal::config('sharemessage.settings')->get('additional_services');

    $additional = '';
    if ($additional_services) {
      $additional .= '<a class="addthis_button_compact"></a>';
    }
    $counter = isset($entity->settings['counter']) ? $entity->settings['counter'] : \Drupal::config('sharemessage.settings')->get('counter');
    if ($counter) {
      $additional .= '<a class="addthis_counter ' . $counter . '"></a>';
    }
    return $additional;
  }

}
