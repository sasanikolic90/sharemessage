<?php
/**
 * @file
 * Contains \Drupal\sharemessage\EventSubscriber\SharemessageSubscriber.
 */

namespace Drupal\sharemessage\EventSubscriber;

use Drupal\Core\Page\HtmlPage;
use Drupal\Core\Page\MetaElement;
use Drupal\sharemessage\Entity\ShareMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Event subscriber for Sharemessage.
 */
class SharemessageSubscriber implements EventSubscriberInterface {
  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::VIEW][] = array('enforcedUsageAttach', 60);
    return $events;
  }

  /**
   * Attaches OG tags for a sharemessage if its smid is present in URL query.
   */
  public function enforcedUsageAttach(GetResponseForControllerResultEvent $event) {
    $page = $event->getControllerResult();
    if ($page instanceof HtmlPage) {
      $smid = $event->getRequest()->query->get('smid');
      if (!empty($smid) && \Drupal::config('sharemessage.settings')->get('message_enforcement')) {
        $sharemessage = ShareMessage::load($smid);
        if ($sharemessage) {
          foreach ($sharemessage->buildOGTags($sharemessage->getContext()) as $tag) {
            $page->addMetaElement(new MetaElement(NULL, $tag['#attributes']));
          }
        }
      }
    }
  }

}
