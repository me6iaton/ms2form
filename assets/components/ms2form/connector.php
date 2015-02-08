<?php
/**
 * miniShop2 Connector
 *
 * @package minishop2
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('ms2form_core_path',null,$modx->getOption('core_path').'components/ms2form/');
//require_once $corePath.'model/ms2form/ms2form.class.php';
//$modx->miniShop2 = new miniShop2($modx);

$modx->lexicon->load('minishop2:default');
$modx->lexicon->load('ms2form:default');

/* handle request */
$path = $modx->getOption('processorsPath', $modx->ms2form->config, $corePath.'processors/');
$modx->request->handleRequest(array(
  'processors_path' => $path,
  'location' => '',
));
