<?php
if (empty($_REQUEST['action'])) {
  die('Access denied');
}
else {
  $action = $_REQUEST['action'];
}

define('MODX_API_MODE', true);

$productionIndex = dirname(dirname(dirname(dirname(__FILE__)))). '/index.php';
$developmentIndex = dirname(dirname(dirname(dirname(dirname(__FILE__))))). '/index.php';
if (file_exists($productionIndex)){
  require_once $productionIndex;
}else{
  require_once $developmentIndex;
}

$modx->getService('error', 'error.modError');
$modx->getRequest();
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : 'web';
if ($ctx != 'web') {
  $modx->switchContext($ctx);
}

$properties = array();
if (!empty($_REQUEST['form_key']) && isset($_SESSION['ms2form'][$_REQUEST['form_key']])) {
  $properties = $_SESSION['ms2form'][$_REQUEST['form_key']];
} else{
  $message = 'Error missing $_REQUEST[form_key] or not find this in session data';
  $modx->log(modX::LOG_LEVEL_ERROR, $message);
  die($message);
}

/* @var ms2form $ms2form */
$ms2form = $modx->getService('ms2form', 'ms2form', $modx->getOption('ms2form_core_path', null, $modx->getOption('core_path') . 'components/ms2form/') . 'model/ms2form/', $properties);

if ($modx->error->hasError() || !($ms2form instanceof ms2form)) {
  die('Error');
}
switch ($action) {
  case 'config/get': $response = $_SESSION['ms2form'][$_REQUEST['form_key']]; break;
  case 'gallery/upload': $response = $ms2form->fileUpload($_POST);break;
  case 'gallery/delete': $response = $ms2form->fileDelete($_POST['id']); break;

  case 'product/getlist_tag': $response = $ms2form->getListTag($_POST); break;
  case 'product/getlist_category': $response = $ms2form->getListCategory($_POST); break;
  case 'product/update':
  case 'product/save': $response = $ms2form->productSave($_POST); break;
  case 'category/create': $response = $ms2form->categoryCreate($_POST); break;
  default:
    $message = $_REQUEST['action'] != $action ? 'tickets_err_register_globals' : 'tickets_err_unknown';
    $response = $modx->toJSON(array('success' => false, 'message' => $modx->lexicon($message)));
}

if (is_array($response)) {
  $response = $modx->toJSON($response);
}

@session_write_close();
exit($response);
