<?php

/**
 * Implements hook_views_data().
 */
function workbc_cdq_career_match_views_data() {

  $data = [];

  $data['workbc_cdq_career_match'] = [
    'table' => [
      'group' => t('Quiz Career Match'),
      'base' => [
        'field' => 'id',
        'title' => t('Quiz Career Match'),
        'help' => t('Contains top career matches based on quiz results.'),
      ],
    ],
  ];


  $data['workbc_cdq_career_match']['id']= [
    'real field' => 'id',
    'title' => t('id'),
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],  
  ];


  $data['workbc_cdq_career_match']['sid']= [
    'real field' => 'sid',
    'title' => t('sid'),
    'help' => t('webform submission id'),
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],  
  ];

  $data['workbc_cdq_career_match']['nid']= [
    'real field' => 'nid',
    'title' => t('nid'),
    'help' => t('content id'),
    'relationship' => [
      // Views name of the table to join to for the relationship.
      'base' => 'node_field_data',
      // Database field name in the other table to join on.
      'base field' => 'nid',
      // ID of relationship handler plugin to use.
      'id' => 'standard',
      // Default label for relationship in the UI.
      'label' => t('Career Profile node'),
    ],
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],     
  ];


  $data['workbc_cdq_career_match']['career_match']= [
    'real field' => 'career_match',
    'title' => t('Career Match'),
    'field' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],  
  ];

  $data['workbc_cdq_career_match']['selected']= [
    'real field' => 'selected',
    'title' => t('Selected'),
    'field' => [
      'id' => 'boolean',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'boolean',
    ],
    'argument' => [
      'id' => 'numeric',
    ],  
  ];

  return $data;
}
