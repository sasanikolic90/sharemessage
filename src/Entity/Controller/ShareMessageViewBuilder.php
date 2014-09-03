<?php

/**
 * @file
 * Definition of Drupal\sharemessage\ShareMessageRenderController.
 */

namespace Drupal\sharemessage\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\file\Entity\File;

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
      $profileid = \Drupal::config('sharemessage.settings')->get('addthis_profile_id');

      $context = array('sharemessage' => $entity);
      if ($node = \Drupal::request()->attributes->get('node')) {
        $context['node'] = $node;
      }

      // Let other modules alter the sharing context that will be used for token
      // as base for replacements.
      \Drupal::moduleHandler()->alter('sharemessage_token_context', $this, $context);

      // Add OG Tags to the page.
      $addThis_attributes = FALSE;
      if (strpos(drupal_get_html_head(), 'property="og:') == FALSE && empty($context['_force_attributes'])) {
        $build[$entity->id()]['#attached']['drupal_add_html_head'] = $this->buildOGTags($entity, $context);
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
          'addthis_js' => array(
            '#attached' => array(
              'js' => array(
                array(
                  'data' => array(
                    'addthis_config' => array(
                      'data_track_addressbar' => TRUE,
                    ),
                  ),
                  'type' => 'setting',
                ),
                array(
                  'data' => '//s7.addthis.com/js/300/addthis_widget.js#pubid=' . $profileid,
                  'type' => 'external',
                ),
              ),
            ),
          ),
        );
      }
    }
    return $build;
  }

  /**
   * Function that adds OG tags in the header of the page.
   */
  private function buildOGTags($entity, $context) {
    $tags = array();

    // OG: Title.
    $tags[] = array(
      array(
        '#type' => 'html_tag',
        '#tag' => 'meta',
        '#attributes' => array(
          'property' => 'og:title',
          'content' => $this->getTokenizedField($entity->title, $context),
        ),
      ),
      'og_title',
    );

    // OG: Type.
    $tags[] = array(
      array(
        '#type' => 'html_tag',
        '#tag' => 'meta',
        '#attributes' => array(
          'property' => 'og:type',
          // @todo don't hardcode this, make configurable per sharemessage entity.
          'content' => 'website',
        ),
      ),
      'og_type',
    );

    $image_url = $this->getTokenizedField($entity->image_url, $context);
    // If the returned image URl is empty, try to use the fallback image if
    // one is defined.
    if (!$image_url && !empty($entity->fallback_image)) {
      $image = \Drupal::entityManager()->loadEntityByUuid('file', $entity->fallback_image);
      if ($image) {
        $image_url = file_create_url($image->getFileUri());
      }
    }
    if ($image_url) {
      $tags[] = array(
        array(
          '#type' => 'html_tag',
          '#tag' => 'meta',
          '#attributes' => array(
            'property' => 'og:image',
            'content' => $image_url,
          ),
        ),
        'og_image',
      );
    }

    // OG: URL.
    $tags[] = array(
      array(
        '#type' => 'html_tag',
        '#tag' => 'meta',
        '#attributes' => array(
          'property' => 'og:url',
          'content' => $this->getUrl($entity, $context),
        ),
      ),
      'og_url',
    );

    // OG: Description.
    $tags[] = array(
      array(
        '#type' => 'html_tag',
        '#tag' => 'meta',
        '#attributes' => array(
          'property' => 'og:description',
          'content' => $this->getTokenizedField($entity->message_long, $context),
        ),
      ),
      'og_description',
    );
    return $tags;
  }

  /**
   * Function that adds icon style as part of addThis widget.
   */
  private function buildAttributes($entity) {
    $icon_style = !empty($entity->settings['icon_style']) ? $entity->settings['icon_style'] : \Drupal::config('sharemessage.settings')->get('icon_style');
    return array(
      'class' => array('addthis_toolbox', 'addthis_default_style', $icon_style),
    );
  }

  /**
   * Function that adds icon style with addThis:attributes
   * (url, title, description) as part of addThis widget.
   */
  function buildAdditionalAttributes($entity, $context) {
    $attributes = $this->buildAttributes($entity);
    $attributes['addthis:url'] = $this->getUrl($entity, $context);
    $attributes['addthis:title'] = $this->getTokenizedField($entity->title, $context);
    $attributes['addthis:description'] = $this->getTokenizedField($entity->message_long, $context);
    return $attributes;
  }

  /**
   * Gets a field value and runs it through token_replace().
   *
   * @param $field_name
   * @param $context
   * @param $default
   *
   * @return
   *   If existent, the field value with tokens replace, the default otherwise.
   */
  protected function getTokenizedField($property_value, $context, $default = '') {
    if ($property_value) {
      return strip_tags(\Drupal::token()->replace($property_value, $context, array('clear' => TRUE)));
    }
    return $default;
  }

  /**
   * Function that adds services as part of addThis widget.
   */
  private function buildServicesPart($entity, $context) {
    $services = !empty($entity->settings['services']) ? $entity->settings['services'] : \Drupal::config('sharemessage.settings')->get('services');

    // Configured.
    // @todo render this as render array and attach js using #attached property.
    $services_HTML = '';
    if (!empty($services)) {
      foreach ($services as $key => $service) {
        if ($key == 'twitter' && $entity->message_short) {
          // @todo. This doesn't work, should be printed here.
          _drupal_add_js("var addthis_share = { templates: { twitter: '" . $this->getTokenizedField($entity->message_short, $context) . "', } }", array('type' => 'inline'));
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
   * Getter for the share URL.
   *
   * @param array $context
   *   The context for the token replacements.
   *
   * @return string
   *   The URL for this share message.
   */
  private function getUrl($entity, $context) {
    $options = array('absolute' => TRUE);
    if (!empty($entity->settings['enforce_usage'])) {
      $options['query'] = array('smid' => $entity->id);
    }
    return url($this->getTokenizedField($entity->share_url, $context, current_path()), $options);
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
