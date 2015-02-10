<?php

$chunks = array();

$tmp = array(
  'tpl.ms2form.create' => array(
    'file' => 'create',
    'description' => '',
  ),
  'tpl.ms2form.update' => array(
    'file' => 'update',
    'description' => '',
  ),
  'tpl.ms2form.email.create' => array(
    'file' => 'email_create',
    'description' => '',
  ),
  'tpl.ms2form.file' => array(
    'file' => 'file',
    'description' => '',
  ),
  'tpl.ms2form.files' => array(
    'file' => 'files',
    'description' => '',
  ),
  'tpl.ms2form.image' => array(
    'file' => 'image',
    'description' => '',
  ),
  'tpl.ms2form.section.row' => array(
    'file' => 'section_row',
    'description' => '',
  ),
  'tpl.ms2form.tag.row' => array(
    'file' => 'tag_row',
    'description' => '',
  ),
);

// Save chunks for setup options
$BUILD_CHUNKS = array();

foreach ($tmp as $k => $v) {
  /* @avr modChunk $chunk */
  $chunk = $modx->newObject('modChunk');
  $chunk->fromArray(array(
    'id' => 0,
    'name' => $k,
    'description' => @$v['description'],
    'snippet' => file_get_contents($sources['source_core'] . '/elements/chunks/chunk.' . $v['file'] . '.tpl'),
    'static' => BUILD_CHUNK_STATIC,
    'source' => 1,
    'static_file' => STATIC_PATH . '/elements/chunks/chunk.' . $v['file'] . '.tpl',
  ), '', true, true);

  $chunks[] = $chunk;

  $BUILD_CHUNKS[$k] = file_get_contents($sources['source_core'] . '/elements/chunks/chunk.' . $v['file'] . '.tpl');
}

unset($tmp);
return $chunks;