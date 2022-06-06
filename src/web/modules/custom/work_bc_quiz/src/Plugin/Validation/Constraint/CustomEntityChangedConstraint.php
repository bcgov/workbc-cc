<?php
namespace Drupal\work_bc_quiz\Plugin\Validation\Constraint;
use Symfony\Component\Validator\Constraint;

/**
 * Custom implementation of the validation constraint for the entity changed timestamp.
 */
class CustomEntityChangedConstraint extends Constraint {
  public $message = 'The content has either been modified by another user, or you have already submitted modifications. As a result, your changes cannot be saved. In case you still see this, then you are really unlucky this time!';
}
?>