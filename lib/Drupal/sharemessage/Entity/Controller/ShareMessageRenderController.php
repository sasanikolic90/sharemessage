<?php

/**
 * @file
 * Definition of Drupal\sharemessage\ShareMessageRenderController.
 */

namespace Drupal\sharemessage\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityRenderController;
use Drupal\entity\Entity\EntityDisplay;

/**
 * Render controller for nodes.
 */
class ShareMessageRenderController extends EntityRenderController {

  /**
   * Overrides Drupal\Core\Entity\EntityRenderController::buildContent().
   */
  public function buildContent(array $entities, array $displays, $view_mode, $langcode = NULL) {
    $return = array();
    if (empty($entities)) {
      return $return;
    }

    // Attach user account.
    user_attach_accounts($entities);

    parent::buildContent($entities, $displays, $view_mode, $langcode);

    foreach ($entities as $entity) {
      $bundle = $entity->bundle();
      $display = $displays[$bundle];

      $entity->content['links'] = array(
        '#theme' => 'links__node',
        '#pre_render' => array('drupal_pre_render_links'),
        '#attributes' => array('class' => array('links', 'inline')),
      );

      // Always display a read more link on teasers because we have no way
      // to know when a teaser view is different than a full view.
      $links = array();
      if ($view_mode == 'teaser') {
        $node_title_stripped = strip_tags($entity->label());
        $links['node-readmore'] = array(
          'title' => t('Read more<span class="visually-hidden"> about @title</span>', array(
            '@title' => $node_title_stripped,
          )),
          'href' => 'node/' . $entity->id(),
          'html' => TRUE,
          'attributes' => array(
            'rel' => 'tag',
            'title' => $node_title_stripped,
          ),
        );
      }

      $entity->content['links']['node'] = array(
        '#theme' => 'links__node__node',
        '#links' => $links,
        '#attributes' => array('class' => array('links', 'inline')),
      );

      // Add Language field text element to node render array.
      if ($display->getComponent('language')) {
        $entity->content['language'] = array(
          '#type' => 'item',
          '#title' => t('Language'),
          '#markup' => language_name($langcode),
          '#prefix' => '<div id="field-language-display">',
          '#suffix' => '</div>'
        );
      }
    }



    $profileid = variable_get('sharemessage_addthis_profile_id', 1);

    $context = array('sharemessage' => $this);
    if ($node = menu_get_object()) {
      $context['node'] = $node;
    }
    drupal_alter('sharemessage_token_context', $this, $context);

    // Add OG Tags to the page.
    $addThis_attributes = FALSE;
    if (strpos(drupal_get_html_head(), 'property="og:') == FALSE && empty($context['_force_attributes'])) {
      $this->addOGTags($context);
    }
    else {
      $addThis_attributes = TRUE;
    }
    unset($context['_force_attributes']);

    // Add addThis buttons.
    $content = array();
    if ($view_mode == 'full') {
      $content['addthis'] = array(
        '#type' => 'container',
        '#attributes' => $addThis_attributes ? $this->buildAdditionalAttributes($context) : $this->buildAttributes(),
        'services' => array(
          '#markup' => $this->build_services_part($context),
        ),
        'additional_services' => array(
          '#markup' => $this->build_additional_services_part(),
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

  /**
   * Overrides Drupal\Core\Entity\EntityRenderController::alterBuild().
   */
  protected function alterBuild(array &$build, EntityInterface $entity, EntityDisplay $display, $view_mode, $langcode = NULL) {
    parent::alterBuild($build, $entity, $display, $view_mode, $langcode);
    if ($entity->id()) {
      $build['#contextual_links']['node'] = array('node', array($entity->id()));
    }

    // The node 'submitted' info is not rendered in a standard way (renderable
    // array) so we have to add a cache tag manually.
    $build['#cache']['tags']['user'][] = $entity->uid;
  }

  /**
   * Function that adds OG tags in the header of the page.
   */
  private function addOGTags($context) {

    // Basic Metadata (og:title, og:type, og:image, og:url).

    // OG: Title.
    $og_title = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'property' => 'og:title',
        'content' => $this->getTokenizedField('sharemessage_title', $context),
      ),
    );
    drupal_add_html_head($og_title, 'og_title');

    // OG: Type.
    $og_type = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'property' => 'og:type',
        'content' => 'website',
      ),
    );
    drupal_add_html_head($og_type, 'og_type');

    // OG: Image.
    if (isset($this->sharemessage_image[LANGUAGE_NONE][0]['uri'])) {
      $image_url = file_create_url($this->sharemessage_image[LANGUAGE_NONE][0]['uri']);
    }
    else {
      $image_url = $this->getTokenizedField('sharemessage_image_url', $context);
    }
    if ($image_url) {
      $og_image = array(
        '#type' => 'html_tag',
        '#tag' => 'meta',
        '#attributes' => array(
          'property' => 'og:image',
          'content' => $image_url,
        ),
      );
      drupal_add_html_head($og_image, 'og_image');
    }

    // OG: URL.
    $og_url = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'property' => 'og:url',
        'content' => $this->getUrl($context),
      ),
    );
    drupal_add_html_head($og_url, 'og_url');

    // OG: Description.
    $og_description = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'property' => 'og:description',
        'content' => $this->getTokenizedField('sharemessage_long', $context),
      ),
    );
    drupal_add_html_head($og_description, 'og_description');
  }

  /**
   * Function that adds icon style as part of addThis widget.
   */
  private function buildAttributes() {
    $icon_style = !empty($this->settings['icon_style']) ? $this->settings['icon_style'] : variable_get('sharemessage_default_icon_style', 'addthis_16x16_style');
    return array(
      'class' => array('addthis_toolbox', 'addthis_default_style', $icon_style),
    );
  }

  /**
   * Function that adds icon style with addThis:attributes
   * (url, title, description) as part of addThis widget.
   */
  function buildAdditionalAttributes($context) {
    $attributes = $this->buildAttributes();
    $attributes['addthis:url'] = $this->getUrl($context);
    $attributes['addthis:title'] = $this->getTokenizedField('sharemessage_title', $context);
    $attributes['addthis:description'] = $this->getTokenizedField('sharemessage_long', $context);
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
  protected function getTokenizedField($field_name, $context, $default = '') {
    if ($items = field_get_items('sharemessage', $this, $field_name)) {
      return strip_tags(token_replace($items[0]['value'], $context));
    }
    return $default;
  }

  /**
   * Function that adds services as part of addThis widget.
   */
  private function build_services_part($context) {
    $services = !empty($this->settings['services']) ? $this->settings['services'] : variable_get('sharemessage_default_services', array());

    // Configured.
    $services_HTML = '';
    if (!empty($services)) {
      foreach ($services as $key => $service) {
        if ($key == 'twitter' && field_get_items('sharemessage', $this, 'sharemessage_short')) {
          // @todo. This doesn't work, should be printed here.
          drupal_add_js("var addthis_share = { templates: { twitter: '" . $this->getTokenizedField('sharemessage_short', $context) . "', } }", array('type' => 'inline'));
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
  private function getUrl($context) {
    $options = array('absolute' => TRUE);
    if (!empty($this->settings['enforce_usage'])) {
      $options['query'] = array('smid' => $this->smid);
    }
    return url($this->getTokenizedField('sharemessage_url', $context, current_path()), $options);
  }

  /**
   * Function that adds additional services as part of addThis widget.
   */
  private function build_additional_services_part() {
    $additional_services = isset($this->settings['additional_services']) ? $this->settings['additional_services'] : variable_get('sharemessage_default_additional_services', TRUE);

    $additional = '';
    if ($additional_services) {
      $additional .= '<a class="addthis_button_compact"></a>';
    }
    $counter = isset($this->settings['counter']) ? $this->settings['counter'] : variable_get('sharemessage_default_counter', FALSE);
    if ($counter) {
      $additional .= '<a class="addthis_counter ' . $counter . '"></a>';
    }
    return $additional;
  }

}
