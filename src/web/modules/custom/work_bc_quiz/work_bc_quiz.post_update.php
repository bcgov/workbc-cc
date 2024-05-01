<?php

use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * NOC 2021 taxonomy migration.
 *
 * As per ticket NOC-350.
 */
function work_bc_quiz_post_update_350_1_taxonomy_migration(&$sandbox = NULL) {


  $markup = "<<< NOC 2021 Education Migration >>><br><br>";

  $updates = array();
  $updates[] = array(
    'old' => 'Apprenticeship Certificate',
    'teer' => '0',
    'term' => 'Management',
    'description' => 'Management responsibilities'
  );
  $updates[] = array(
    'old' => 'Degree',
    'teer' => '1',
    'term' => 'University Degree',
    'description' => "Completion of a university degree (bachelor's, master's, or doctorate); or Previous experience and expertise in subject matter knowledge from a related occupation found in TEER category 2 (when applicable).",
  );
  $updates[] = array(
    'old' => 'Diploma/Certificate Excluding Apprenticeship',
    'teer' => '2',
    'term' => 'College Diploma or Apprenticeship, 2 or more years',
    'description' => "Completion of a post-secondary education program of two to three years at community college, institute of technology, or CÉGEP; or Completion of an apprenticeship training program of two to five years; or Occupations with supervisory or significant safety (e.g. police officers and firefighters) responsibilities; or Several years of experience in a related occupation from TEER category 3 (when applicable).",
  );
  $updates[] = array(
    'old' => 'High School',
    'teer' => '3',
    'term' => 'College Diploma or Apprenticeship, less than 2 years',
    'description' => "Completion of a post-secondary education program of less than two years at community college, institute of technology or CÉGEP; or Completion of an apprenticeship training program of less than two years; or More than six months of on-the-job training, training courses or specific work experience with some secondary school education; or Several years of experience in a related occupation from TEER category 4 (when applicable).",
  );
  $updates[] = array(
    'old' => 'Less than High School',
    'teer' => '4',
    'term' => 'High School Diploma',
    'description' => "Completion of secondary school; or Several weeks of on-the-job training with some secondary school education; or Experience in a related occupation from TEER category 5 (when applicable).",
  );
  $updates[] = array(
    'old' => '',
    'teer' => '5',
    'term' => 'No Formal Education',
    'description' => "Short work demonstration and no formal educational requirements",
  );

  foreach ($updates as $update) {
    if (!empty($update['old'])) {
      $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['name' => $update['old'], 'vid' => 'education_level']);
      $term = $terms[array_key_first($terms)];
      if ($term) {
        $term->name = $update['term'];
        $term->field_teer = $update['teer'];
        $term->description = $update['description'];
        $term->save();
      }
    }
    else {
      // create new term
      $term = \Drupal\taxonomy\Entity\Term::create([
        'name' => $update['term'],
        'vid' => 'education_level',
        'description' => $update['description'],
        'field_teer' => $update['teer'],
      ])->save();
    }
  }

  return t('[NOC-350] NOC 2021 taxonomy migration.');
}


/**
 * NOC 2021 data migration.
 *
 * As per ticket NOC-350.
 */
function work_bc_quiz_post_update_350_2_noc_migration(&$sandbox = NULL) {
  if (!isset($sandbox['concordance'])) {
    $sandbox['concordance'] = loadConcordance();
    $sandbox['provincial'] = loadCareerProvincial();
    $sandbox['wages'] = loadWages();
    $sandbox['education'] = loadEducation();
    $sandbox['count'] = count($sandbox['concordance']);
    $sandbox['last_noc'] = 0;
    $sandbox['last_noc_2016'] = 0;
    $sandbox['last_noc_nid'] = 0;
    $sandbox['last_noc_type'] = "";
    $sandbox['noc_map'] = [];
    $sandbox['lookup'] = [];

    // create lookup map for checking if creating a split is necessary.
    // no split required if an existing career profile will be updated to this
    // NOC 2021 number later in the migration
    foreach ($sandbox['concordance'] as $key => $noc) {
      if ($noc[0] <> "0000" && !empty($noc[3])) {
        $sandbox['lookup'][$noc[3]] = $noc[0];
      }
    }

    // save original Career Profile paths for post migration validation.
    saveCareerProfilePaths();
  }

  $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();

  $workbc_url = rtrim(getenv('WORKBC_URL'), '/') . '/';

  $message = "No action taken.";
  $noc = array_shift($sandbox['concordance']);
  if (!empty($noc)) {
    // split
    if ($noc[0] ==  "0000") {
      // assumes all split NOCs immediately follow the original NOC in concordance
      // only create split if doesn't already exist or will be created later in the migration
      if (!isset($sandbox['noc_map'][$noc[3]]) && !array_key_exists($noc[3], $sandbox['lookup'])) {
        // load source node
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($sandbox['last_noc_nid']);

        // clone node and update
        $split = $node->createDuplicate();
        $split->title = $noc[3];
        $split->field_noc_name = $noc[4];

        $split->field_workbc_link = $workbc_url . "career/" . $noc[3];
        $split->field_find_job = $workbc_url . "Jobs-Careers/Find-Jobs/Jobs.aspx?Searchnoc=" . $sandbox['last_noc_2016'];

        $data = getNocData($noc[3], $sandbox['education']);
        $terms = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadByProperties(['field_teer' => $data[2], 'vid' => 'education_level']);
        $term = array_shift($terms);
        $split->field_education_level = $term->id();

        $data = getNocData($noc[3], $sandbox['provincial']);
        $split->field_job_openings = $data[6];

        $data = getNocData($noc[3], $sandbox['wages']);
        $split->field_median_salary = round($data[5]);

        $split->field_opening_from_to = "(2023 - 2033)";

        $split->created = time();
        $split->setPublished(TRUE);
        $split->save();

        $sandbox['noc_map'][$noc[3]] = ['noc2016' => $split->field_noc_2016->value, 'nid' => $split->id()];

        if ($sandbox['last_noc_type'] == "merge") {
          $message = "NOC 2021 data migration: Split after Merge " . $node->field_noc_2016->value . " -> " . $split->title->value;
        }
        else {
          $message = "NOC 2021 data migration: Split " . $node->field_noc_2016->value . " -> " . $split->title->value;
        }
      }
      else {
        if (isset($sandbox['noc_map'][$noc[3]])) {
          $message = "NOC 2021 data migration: Split already exists " . $sandbox['last_noc_2016'] . " -> " . $noc[3];
        }
        else {
          $message = "NOC 2021 data migration: Split not required " . $sandbox['last_noc_2016'] . " -> " . $noc[3];
        }
      }
    }
    else {
      $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['title' => $noc[0]]);
      $node = array_shift($nodes);

      $node->field_noc_2016 = $node->title;

      // if record for NOC 2021 already exists merge
      if (isset($sandbox['noc_map'][$noc[3]])) {
        $node->setUnpublished();

        $node->title = "[ARCHIVED] " . $node->title->value;
        $node->field_noc_name = "[ARCHIVED] " . $node->field_noc_name->value;

        $message = "NOC 2021 data migration: Merge " . $node->field_noc_2016->value . " -> " . $noc[3];
        $sandbox['last_noc_type'] = "merge";

        // save old path alias
        // $old_path = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $node->id(), $langcode);
        $node->save();

        // $old_path = ltrim($old_path, '/');
        // $new_path = 'node/'. $sandbox['noc_map'][$noc[3]]['nid'];

        // $redirect_storage = \Drupal::entityTypeManager()->getStorage('redirect');
        // $redirects = $redirect_storage->loadByProperties(['redirect_source__path' => $old_path]);
        // $redirect = array_shift($redirects);
        // if ($redirect) {
        //   $redirect->setRedirect($new_path);
        //   $redirect->save();
        // }
      }
      // else update
      else {
        $node->title = $noc[3];
        $node->field_noc_name = $noc[4];

        $node->field_workbc_link = $workbc_url . "career/" . $noc[3];
        $node->field_find_job = $workbc_url . "Jobs-Careers/Find-Jobs/Jobs.aspx?Searchnoc=" . $sandbox['last_noc_2016'];

        $data = getNocData($noc[3], $sandbox['education']);
        $terms = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadByProperties(['field_teer' => $data[2], 'vid' => 'education_level']);
        $term = array_shift($terms);
        $node->field_education_level = $term->id();

        $data = getNocData($noc[3], $sandbox['provincial']);
        $node->field_job_openings = $data[6];

        $data = getNocData($noc[3], $sandbox['wages']);
        $node->field_median_salary = round($data[5]);

        $node->field_opening_from_to = "(2023 - 2033)";

        $sandbox['noc_map'][$noc[3]] = ['noc2016' => $node->field_noc_2016->value, 'nid' => $node->id()];
        $sandbox['last_noc_type'] = "update";

        $message = "NOC 2021 data migration: Update " . $node->field_noc_2016->value . " -> " . $node->title->value;

        $node->save();
      }

      // save noc and nid in case needed for split
      $sandbox['last_noc'] = $noc[3];
      $sandbox['last_noc_2016'] = $noc[0];
      $sandbox['last_noc_nid'] = $node->id();
    }
  }

  if (empty($sandbox['concordance'])) {
    // archive noc 2214
    $noc = "2214";
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['title' => $noc]);
    $node = array_shift($nodes);

    $node->field_noc_2016 = $noc;

    $node->setUnpublished();

    $node->title = "[ARCHIVED] " . $node->title->value;
    $node->field_noc_name = "[ARCHIVED] " . $node->field_noc_name->value;

    $message = "NOC 2021 data migration: Archive " . $node->field_noc_2016->value;
    $sandbox['last_noc_type'] = "archive";

    $node->save();
  }

  $sandbox['#finished'] = empty($sandbox['concordance']) ? 1 : ($sandbox['count'] - count($sandbox['concordance'])) / $sandbox['count'];
  return t("[NOC-350] $message");
}


/**
 * NOC 2021 data migration.
 *
 * As per ticket NOC-350.
 */
function work_bc_quiz_post_update_350_3_noc_migration(&$sandbox = NULL) {
  if (!isset($sandbox['profiles'])) {
    $nids = \Drupal::entityQuery('node')->condition('type','career_profile')->execute();
    $sandbox['profiles'] = \Drupal\node\Entity\Node::loadMultiple($nids);
    $sandbox['provincial'] = loadCareerProvincial();
    $sandbox['wages'] = loadWages();
    $sandbox['count'] = count($sandbox['profiles']);
  }

  $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();

  $message = "Skip unpublished Career Profile";
  $profile = array_shift($sandbox['profiles']);
  if (!empty($profile) && $profile->isPublished()) {
    $noc = $profile->title->value;

    $data = getNocData($noc, $sandbox['provincial']);
    if (empty($data[6]) || is_null($data[6]) || $data[6] == "NA") {
      $profile->field_job_openings = null;
    }
    else {
      $profile->field_job_openings = $data[6];
    }

    $data = getNocData($noc, $sandbox['wages']);
    $profile->field_median_salary = ($data[5] == "NA") ? null : round($data[5]);
    
    $profile->save();
    
    $message = "NOC 2021 data migration: Update Data " . $profile->field_noc->value . " - " . $profile->title->value;
  }

  $sandbox['#finished'] = empty($sandbox['profiles']) ? 1 : ($sandbox['count'] - count($sandbox['profiles'])) / $sandbox['count'];
  return t("[NOC-350] $message");
}


/**
 * NOC 2021 data migration.
 *
 * As per tickets NOC-359 & NOC-94 update job descriptions.
 */
function work_bc_quiz_post_update_350_4_noc_migration(&$sandbox = NULL) {
  if (!isset($sandbox['profiles'])) {
    $nids = \Drupal::entityQuery('node')->condition('type','career_profile')->execute();
    $sandbox['profiles'] = \Drupal\node\Entity\Node::loadMultiple($nids);
    $sandbox['summaries'] = loadOccupationSummaries();
    $sandbox['count'] = count($sandbox['profiles']);
  }

  $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();

  $message = "Skip unpublished Career Profile";
  $profile = array_shift($sandbox['profiles']);
  if (!empty($profile) && $profile->isPublished()) {
    $noc = $profile->title->value;

    $data = getNocData($noc, $sandbox['summaries']);
    $profile->field_job_summary = $data[2];
   
    $profile->save();
    
    $message = "NOC 2021 data migration: Update career description " . $profile->field_noc->value . " - " . $profile->title->value;
  }

  $sandbox['#finished'] = empty($sandbox['profiles']) ? 1 : ($sandbox['count'] - count($sandbox['profiles'])) / $sandbox['count'];
  return t("[NOC-350] $message");
}

