<?php

namespace Drupal\workbc_cdq_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;


/**
 * Provides a WorkBC Career Discover Quizzes block.
 *
 * @Block(
 *   id = "workbc_quizzes_block",
 *   admin_label = @Translation("WorkBC Career Discover Quizzes block"),
 *   category = @Translation("WorkBC - CDQ"),
 * )
 */
class QuizzesBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $quizzes = quizzesList();
    $markup = "";
    foreach ($quizzes as $key => $quiz) {
      $webform = \Drupal::entityTypeManager()->getStorage('webform')->load($key);

      if ($webform) {
        $results = getUserSubmission($key);
        if ($results) {
          $markup .= "<h2>" . $quiz . "</h2>";
          if (!$results->isDraft()) {
            // workbc_cdq_custom.abilities_quiz_results
            $link = Link::fromTextAndUrl(t('View Your Results'), Url::fromUri('route:workbc_cdq_custom.' . $key . '_results'));
            $markup .= "<p>"  . $link->toString() . "</p>";

            $url = Url::fromRoute('entity.webform.canonical', ['webform' => $key, 'token' => $results->getToken()]);
            $link = \Drupal\Core\Link::fromTextAndUrl("Modify Your Answers", $url);
            $markup .= "<p>" . $link->toString() . "</p>";
          }
          else {
            $url = Url::fromRoute('entity.webform.canonical', ['webform' => $key, 'token' => $results->getToken()]);
            $link = \Drupal\Core\Link::fromTextAndUrl("Finish the Quiz", $results->getTokenUrl());
            $markup .= "<p>" . $link->toString() . "</p>";
          }
        }
        else {
          $url = Url::fromRoute('entity.webform.canonical', ['webform' => $key]);
          $link = \Drupal\Core\Link::fromTextAndUrl("Take the Quiz", $url);
          $markup .= "<h2>" . $quiz . "</h2>";
          $markup .= "<p>" .  $link->toString() . "</p>";
        }

      }
      else {
        $markup .= "<h2>" . $quiz . "</h2>";
        $markup .= "<p>not yet available.</p>";
      }
    }


    return array(
      '#type' => 'markup',
      '#markup' => $markup,
    );

  }

  public function getCacheMaxAge() {
      return 0;
  }

}
