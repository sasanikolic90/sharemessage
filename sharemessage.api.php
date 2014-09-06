<?php

/**
 * @file
 * Hooks provided by share message module.
 */

/**
 * Allow other modules to alter sharemessage token context.
 *
 * @param \Drupal\sharemessage\Entity\ShareMessage $sharemessage
 *   Currently loaded sharemessage object.
 * @param array $context
 *   Token Context.
 */
function hook_sharemessage_token_context_alter(Drupal\sharemessage\Entity\ShareMessage $sharemessage, &$context) {
  // Alter sharemessage title.
  $sharemessage->title = 'Altered Title';

  // Add taxonomy_vocabulary object type in a $context array.
  $context['taxonomy_vocabulary'] = \Drupal::routeMatch()->getParameter('taxonomy_vocabulary');
}
