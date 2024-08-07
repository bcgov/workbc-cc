<?php

namespace Drupal\workbc_cdq_ssot\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Component\Utility\Timer;

/**
 * SSOT data fetcher.
 *
 * @QueueWorker(
 *   id = "ssot_downloader",
 *   title = @Translation("SSOT Downloader"),
 *   cron = {"time" = 60}
 * )
 */
class SsotDownloader extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  private Array $levels;

  /**
  * Main constructor.
  *
  * @param array $configuration
  *   Configuration array.
  * @param mixed $plugin_id
  *   The plugin id.
  * @param mixed $plugin_definition
  *   The plugin definition.
  */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   *
   * Download the requested SSOT dataset.
   * Update the corresponding fields in each career_profile accordingly.
   * Update the local dataset update date.
   */
  public function processItem($data) {
    \Drupal::logger('workbc')->notice('Updating SSOT datasets: @datasets', [
      '@datasets' => join(', ', array_map(function($dataset) {
        return $dataset['endpoint'];
      }, $data['datasets']))
    ]);
    Timer::start('ssot_downloader');

    // Load all career profiles.
    $storage = \Drupal::service('entity_type.manager')->getStorage('node');
    $careers = $storage->loadByProperties([
      'type' => 'career_profile',
    ]);

    // Load all updated datasets.
    $updated_datasets = [];
    foreach ($data['datasets'] as &$dataset) {
      $metadata = SSOT_DATASETS[$dataset['endpoint']];

      // Formulate SSOT query given dataset information.
      $endpoint = $dataset['endpoint'] . '?' . http_build_query(array_merge(
        ['select' => $metadata['fields']],
        array_key_exists('filters', $metadata) ? $metadata['filters'] : [],
        array_key_exists('order', $metadata) ? ['order' => $metadata['order']] : [],
      ));
      $result = ssot($endpoint);
      if (!$result) {
        \Drupal::logger('workbc')->warning('Error fetching SSOT dataset @dataset at @endpoint. Ignoring.', [
          '@dataset' => $dataset['endpoint'],
          '@endpoint' => $endpoint,
        ]);
        continue;
      };

      // SPECIAL CASE
      // For career_provincial, delete the cached job openings range which may change now.
      if ($dataset['endpoint'] === 'career_provincial') {
        \Drupal::state()->delete('workbc.ssot_job_openings_range');
      }

      // Index the dataset by NOC.
      $entries = array_reduce(json_decode($result->getBody(), true), function($entries, $entry) use($metadata) {
        $entries[$entry[$metadata['noc_key']]] = $entry;
        return $entries;
      }, []);

      // update each career with the dataset-specific update function.
      $method = 'update_' . $dataset['endpoint'];
      $missing_nocs = [];
      foreach ($careers as &$career) {
        $noc = $career->get('field_noc')->value;
        if (!array_key_exists($noc, $entries)) {
          $missing_nocs[] = $noc;
          continue;
        }
        $entry = $entries[$noc];
        $this->$method($dataset['endpoint'], $entry, $career);
      }
      if (!empty($missing_nocs)) {
        \Drupal::logger('workbc')->warning('Could not find the following NOCs in dataset @dataset: @nocs', [
          '@nocs' => join(', ', $missing_nocs),
          '@dataset' => $dataset['endpoint'],
        ]);
      }

      // Indicate we have updated this dataset.
      $updated_datasets[$dataset['endpoint']] = $dataset['ssot_date'];
    }

    // Save the careers.
    foreach ($careers as &$career) {
      $career->setNewRevision(true);
      $career->setRevisionLogMessage('Updating SSOT datasets: ' . join(', ', array_keys($updated_datasets)));
      $career->setRevisionCreationTime(time());
      $career->setRevisionUserId(1);
      $career->save();
    }

    // Update local date for updated datasets.
    $local_dates = $data['local_dates'];
    foreach ($updated_datasets as $endpoint => $ssot_date) {
      $local_dates[$endpoint] = $ssot_date;
    }
    \Drupal::state()->set('workbc.ssot_dates', $local_dates);

    Timer::stop('ssot_downloader');
    \Drupal::logger('workbc')->notice('Updated SSOT datasets in @time: @datasets', [
      '@datasets' => join(', ', array_keys($updated_datasets)),
      '@time' => Timer::read('ssot_downloader') . 'ms'
    ]);
  }

  private function update_nocs($endpoint, $entry, &$career) {
    if (empty($this->levels)) {
      $this->levels = array_reduce(\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'education_level']), function($levels, $level) {
        $levels[strval($level->field_teer->value)] = $level->id();
        return $levels;
      }, []);
    }
    $career->setTitle($entry['label_en']);
    $career->set('body', [
      'value' => $entry['definition_en'],
      'format' => 'plain_text',
    ]);
    $career->set('field_education_level', [
      ['target_id' => $this->levels[strval($entry['teer_level'])]]
    ]);
  }

  private function update_wages($endpoint, $entry, &$career) {
    $career->set('field_annual_salary', $entry['calculated_median_annual_salary']);
  }

  private function update_career_provincial($endpoint, $entry, &$career) {
    $career->set('field_job_openings', $entry['expected_job_openings_10y']);
  }

  private function update_career_trek($endpoint, $entry, &$career) {
    $career->set('field_video_id', str_replace('https://youtu.be/', '', $entry['youtube_link']));
  }
}
