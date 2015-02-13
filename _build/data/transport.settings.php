<?php

$settings = array();

$tmp = array(
  'core_path' => array(
    'value' => PKG_CORE_PATH,
    'xtype' => 'textfield',
    'area' => 'ms2form.main',
  ),
  'assets_url' => array(
    'value' => PKG_ASSETS_URL,
    'xtype' => 'textfield',
    'area' => 'ms2form.main',
  ),
  'frontend_css' => array(
    'value' => PKG_ASSETS_URL . 'css/web/ms2form.css',
    'xtype' => 'textfield',
    'area' => 'ms2form.main',
  ),
  'frontend_js' => array(
    'value' => PKG_ASSETS_URL . 'js/web/ms2form.js',
    'xtype' => 'textfield',
    'area' => 'ms2form.main',
  ),
  'mail_bcc' => array(
    'xtype' => 'textfield',
    'value' => '1',
    'area' => 'ms2form.mail',
  ),
  'mail_from' => array(
    'xtype' => 'textfield',
    'value' => '',
    'area' => 'ms2form.mail',
  ),
  'mail_from_name' => array(
    'xtype' => 'textfield',
    'value' => '',
    'area' => 'ms2form.mail',
  ),
  'mail_createdby' => array(
    'xtype' => 'combo-boolean',
    'value' => true,
    'area' => 'ms2form.mail',
  ),
);


foreach ($tmp as $k => $v) {
  /* @var modSystemSetting $setting */
  $setting = $modx->newObject('modSystemSetting');
  $setting->fromArray(array_merge(
    array(
      'key' => 'ms2form_' . $k,
      'namespace' => PKG_NAME_LOWER,
    ), $v
  ), '', true, true);

  $settings[] = $setting;
}

unset($tmp);
return $settings;
