<?php
/**
 * @file
 * Definition of ShareMessage config entity class.
 */

namespace Drupal\sharemessage\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Entity class for the Share Message entity.
 *
 * @ConfigEntityType(
 *   id = "sharemessage",
 *   label = @Translation("Share Message"),
 *   handlers = {
 *     "access" = "Drupal\sharemessage\Entity\Handler\ShareMessageAccessControlHandler",
 *     "view_builder" = "Drupal\sharemessage\Entity\Handler\ShareMessageViewBuilder",
 *     "list_builder" = "Drupal\sharemessage\Entity\Handler\ShareMessageListBuilder",
 *     "form" = {
 *       "add" = "Drupal\sharemessage\Form\ShareMessageForm",
 *       "edit" = "Drupal\sharemessage\Form\ShareMessageForm",
 *       "delete" = "Drupal\sharemessage\Form\ShareMessageDeleteForm"
 *     }
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "edit-form" = "sharemessage.sharemessage_edit"
 *   }
 * )
 */
class ShareMessage extends ConfigEntityBase {

  /**
   * The machine name of this sharemessage.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the sharemessage.
   *
   * @var string
   */
  public $label;

  /**
   * The flag for default overrides of the sharemessage.
   *
   * @var string
   */
  public $override_default_settings;

  /**
   * The settings of the sharemessage.
   *
   * @var string
   */
  public $settings;

  /**
   * The title of the sharemessage.
   *
   * @var string
   */
  public $title;

  /**
   * The long share text of the sharemessage.
   *
   * @var string
   */
  public $message_long;

  /**
   * The short text of the sharemessage, used for twitter.
   *
   * @var string
   */
  public $message_short;

  /**
   * The image URL that will be used for sharing.
   *
   * @var string
   */
  public $image_url;

  /**
   * An optional fallback image as file UUID if the image URL does not resolve.
   *
   * @var string
   */
  public $fallback_image;

  /**
   * Specific URL that will be shared, defaults to the current page
   *
   * @var string
   */
  public $share_url;

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->get('label');
  }

  /**
   * {@inheritdoc}
   */
  public function setLabel($label) {
    $this->set('label', $label);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->get('label');
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($label) {
    $this->set('label', $label);
    return $this;
  }

  /**
   * Returns a context for tokenizing.
   *
   * @return array
   *   An array containing the following elements:
   *     - sharemessage: This entity.
   *     - node: The node target for the current request, if any.
   *   The array is altered by modules implementing
   *   hook_sharemessage_token_context().
   */
  public function getContext() {
    $context = array('sharemessage' => $this);
    if ($node = \Drupal::request()->attributes->get('node')) {
      $context['node'] = $node;
    }

    // Let other modules alter the sharing context that will be used for token
    // as base for replacements.
    \Drupal::moduleHandler()->alter('sharemessage_token_context', $this, $context);

    return $context;
  }

  /**
   * Returns Open Graph meta tags for <head>.
   */
  public function buildOGTags($context) {
    $tags = array();

    // OG: Title.
    $tags[] = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'property' => 'og:title',
        'content' => $this->getTokenizedField($this->title, $context),
      ),
    );

    // OG: Type.
    $tags[] = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'property' => 'og:type',
        // @todo don't hardcode this, make configurable per sharemessage entity.
        'content' => 'website',
      ),
    );

    $image_url = $this->getTokenizedField($this->image_url, $context);
    // If the returned image URl is empty, try to use the fallback image if
    // one is defined.
    if (!$image_url && !empty($this->fallback_image)) {
      $image = \Drupal::entityManager()->loadEntityByUuid('file', $this->fallback_image);
      if ($image) {
        $image_url = file_create_url($image->getFileUri());
      }
    }
    if ($image_url) {
      $tags[] = array(
        '#type' => 'html_tag',
        '#tag' => 'meta',
        '#attributes' => array(
          'property' => 'og:image',
          'content' => $image_url,
        ),
      );
    }

    // OG: URL.
    $tags[] = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'property' => 'og:url',
        'content' => $this->getUrl($context),
      ),
    );

    // OG: Description.
    $tags[] = array(
      '#type' => 'html_tag',
      '#tag' => 'meta',
      '#attributes' => array(
        'property' => 'og:description',
        'content' => $this->getTokenizedField($this->message_long, $context),
      ),
    );
    return $tags;
  }

  /**
   * Tokenizes a field, if it is set.
   *
   * @param string $property_value
   *   A field value.
   * @param array $context
   *   A context array for Token::replace().
   * @param string $default
   *   (optional) Default value if field value is not set.
   *
   * @return string
   *   If existent, the field value with tokens replace, the default otherwise.
   */
  public function getTokenizedField($property_value, $context, $default = '') {
    if ($property_value) {
      return strip_tags(\Drupal::token()->replace($property_value, $context, array('clear' => TRUE)));
    }
    return $default;
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
  public function getUrl($context) {
    $options = array('absolute' => TRUE);
    if (!empty($this->settings['enforce_usage'])) {
      $options['query'] = array('smid' => $this->id);
    }
    return url($this->getTokenizedField($this->share_url, $context, current_path()), $options);
  }

}
