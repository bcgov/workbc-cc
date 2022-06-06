<?php
namespace Drupal\work_bc_quiz\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Drupal\work_bc_quiz\Controller\WorkBcQuizController;

/**
 * Validates the EntityChanged constraint.
 */
class CustomEntityChangedConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint) {
    if (isset($entity)) {
      /** @var \Drupal\Core\Entity\EntityInterface $entity */
      if (!$entity->isNew()) {
        $saved_entity = \Drupal::entityTypeManager()->getStorage($entity->getEntityTypeId())->loadUnchanged($entity->id());
        // A change to any other translation must add a violation to the current
        // translation because there might be untranslatable shared fields.
        if ($saved_entity && $saved_entity->getChangedTimeAcrossTranslations() > $entity->getChangedTimeAcrossTranslations()) {
          $add_violation = TRUE;
          $quizTypes = new WorkBcQuizController();
          $quizTypes = $quizTypes->getQuizTypes();
          //$nodeBundle = ['abilities_quiz', 'work_values'];
          if ($entity->getEntityTypeId() == 'node' && in_array($entity->bundle(), $quizTypes) &&
            $this->isValidWebsubmission($entity->id())) {
            $add_violation = FALSE;

            // Store this id.
            work_bc_quiz_preserve_values_from_original_entity($entity->id(), TRUE);
          }

          // Add the violation if necessary.
          if ($add_violation) {
            $this->context->addViolation($constraint->message);
          }
        }
      }
    }
  }

  /**
   * Validate the web submission.
   *
   * @param $value
   *   The value.
   *
   * @see project_form_node_form_alter().
   *
   * @return bool
   */
  public function isValidWebsubmission($value) {
    if (!empty(\Drupal::request()->get('web_submission'))) {
      return \Drupal::csrfToken()->validate(\Drupal::request()->get('web_submission'), $value);
    }

    return FALSE;
  }

}

/**
 * Function which holds a static array with ids of entities which need to
 * preserve values from the original entity.
 *
 * @param $id
 *   The entity id.
 * @param bool $set
 *   Whether to store the id or not. 
 *
 * @return bool
 *   TRUE if id is set in the $ids array or not.
 */
function work_bc_quiz_preserve_values_from_original_entity($id, $set = FALSE) {
  static $ids = [];

  if ($set && !isset($ids[$id])) {
    $ids[$id] = TRUE;
  }

  return isset($ids[$id]) ? TRUE : FALSE;
}
?>