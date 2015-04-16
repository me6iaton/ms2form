<?php

$policies = array();

$tmp = array(
  'ms2formUserPolicy' => array(
    'description' => 'A policy for create and update minishop products categories and files.',
    'data' => array(
      'mscategory_save' => true,
      'msproduct_save' => true,
      'msproductfile_save' => true,
      'msproductfile_generate' => true,
      'msproductfile_list' => true,
      'publish_document' => true,
    ),
  ),
);

foreach ($tmp as $k => $v) {
  if (isset($v['data'])) {
    $v['data'] = $modx->toJSON($v['data']);
  }

  /* @var $policy modAccessPolicy */
  $policy = $modx->newObject('modAccessPolicy');
  $policy->fromArray(array_merge(array(
      'name' => $k,
      'lexicon' => PKG_NAME_LOWER . ':permissions',
    ), $v)
    , '', true, true);

  $policies[] = $policy;
}

return $policies;