<?php

$settings = array();

$tmp = array(
  'core_path' => array(
    'xtype' => 'textfield',
    'value' => PKG_CORE_PATH,
    'area' => 'ms2form.main',
  ),
  'assets_url' => array(
    'xtype' => 'textfield',
    'value' => PKG_ASSETS_URL,
    'area' => 'ms2form.main',
  ),
  'frontend_css' => array(
    'xtype' => 'textfield',
    'value' => PKG_ASSETS_URL . 'css/web/ms2form.css',
    'area' => 'ms2form.main',
  ),
  'frontend_js' => array(
    'xtype' => 'textfield',
    'value' => PKG_ASSETS_URL . 'js/web/ms2form.js',
    'area' => 'ms2form.main',
  ),
  'disable_htmlpurifier' => array(
    'xtype' => 'combo-boolean',
    'value' => false,
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
