<?php

$properties = array();

$tmp = array(
  'tplCreate' => array(
    'type' => 'textfield',
    'value' => 'tpl.ms2form.create',
  ),
  'tplUpdate' => array(
    'type' => 'textfield',
    'value' => 'tpl.ms2form.update',
  ),
  'tplSectionRow' => array(
    'type' => 'textfield',
    'value' => 'tpl.ms2form.section.row',
  ),
  'tplTagRow' => array(
    'type' => 'textfield',
    'value' => 'tpl.ms2form.tag.row',
  ),
  'allowFiles' => array(
    'type' => 'combo-boolean',
    'value' => true,
  ),
  'tplFiles' => array(
    'type' => 'textfield',
    'value' => 'tpl.ms2form.files',
  ),
  'tplFile' => array(
    'type' => 'textfield',
    'value' => 'tpl.ms2form.file',
  ),
  'tplImage' => array(
    'type' => 'textfield',
    'value' => 'tpl.ms2form.image',
  ),
  'tplEmailBcc' => array(
    'type' => 'textfield',
    'value' => 'tpl.ms2form.email.bcc',
  ),
  'allowedFields' => array(
    'type' => 'textfield',
    'value' => 'parent,pagetitle,content,published,template,hidemenu,tags',
  ),
  'requiredFields' => array(
    'type' => 'textfield',
    'value' => 'parent,pagetitle,content',
  ),
  'redirectPublished' => array(
    'type' => 'textfield',
    'value' => 'new',
    'desc' => 'ms2form_prop_redirectPublished'
  ),
  'redirectScheme' => array(
    'type' => 'textfield',
    'value' => '-1',
    'desc' => 'ms2form_prop_redirectScheme'
  ),
  'parent' => array(
    'type' => 'numberfield',
    'value' => '',
    'desc' => 'ms2form_prop_parent'
  ),
  'parents' => array(
    'type' => 'textfield',
    'value' => '',
    'desc' => 'ms2form_prop_parents'
  ),
  'categoryMse2form' => array(
    'type' => 'textfield',
    'value' => '',
    'desc' => 'ms2form_prop_categoryMse2form'
  ),
  'parentsIncludeTVs' => array(
    'type' => 'textfield',
    'value' => '',
  ),
  'parentsSortby' => array(
    'type' => 'textfield',
    'value' => 'pagetitle',
  ),
  'parentsSortdir' => array(
    'type' => 'list',
    'options' => array(
      array('text' => 'ASC', 'value' => 'ASC'),
      array('text' => 'DESC', 'value' => 'DESC'),
    ),
    'value' => 'ASC',
  ),
  'resources' => array(
    'type' => 'textfield',
    'value' => '',
    'desc' => 'ms2form_prop_resources'
  ),
  'template' => array(
    'type' => 'numberfield',
    'value' => '',
    'desc' => 'ms2form_prop_template'
  ),
  'templates' => array(
    'type' => 'textfield',
    'value' => '1',
    'desc' => 'ms2form_prop_templates'
  ),
  'permissions' => array(
    'type' => 'textfield',
    'value' => 'section_add_children',
    'desc' => 'ms2form_prop_sections_permissions'
  ),
  'source' => array(
    'type' => 'numberfield',
    'value' => '',
  ),
  'tags' => array(
    'type' => 'combo-boolean',
    'value' => true,
    'desc' => 'ms2form_prop_tags'
  ),
  'newTags' => array(
    'type' => 'combo-boolean',
    'value' => true,
    'desc' => 'ms2form_prop_newTags'
  ),
  'editor' => array(
    'type' => 'list',
    'options' => array(
      array('text' => '0', 'value' => '0'),
      array('text' => 'bootstrapMarkdown', 'value' => 'bootstrapMarkdown'),
      array('text' => 'quill', 'value' => 'quill'),
    ),
    'value' => 'quill',
    'desc' => 'ms2form_prop_editor'
  ),
);

foreach ($tmp as $k => $v) {
  $properties[] = array_merge(
    array(
      'name' => $k,
      'desc' => PKG_NAME_LOWER . '_prop_' . $k,
      'lexicon' => PKG_NAME_LOWER . ':properties',
    ), $v
  );
}

return $properties;