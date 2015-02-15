<?php

/**
 * The base class for ms2form.
 *
 * @package ms2form
 */
class ms2form
{
  /* @var modX $modx */
  public $modx;
  /* @var pdoTools $pdoTools */
  public $pdoTools;
  public $mediaSource;
  public $initialized = array();
  public $authenticated = false;

  /**
   * @param modX $modx
   * @param array $config
   */
  function __construct(modX &$modx, array $config = array())
  {
    $this->modx =& $modx;
    $corePath = $this->modx->getOption('ms2form_core_path', $config, $this->modx->getOption('core_path') . 'components/ms2form/');
    $assetsUrl = $this->modx->getOption('ms2form_assets_url', $config, $this->modx->getOption('assets_url') . 'components/ms2form/');
    $actionUrl = $this->modx->getOption('ms2form_action_url', $config, $assetsUrl . 'action.php');
    if (empty($config['source'])) {
      $config['source'] = $this->modx->getOption('ms2_product_source_default');
    }
    $connectorUrl = $assetsUrl . 'connector.php';

    $this->config = array_merge(array(
      'assetsUrl' => $assetsUrl
    , 'cssUrl' => $assetsUrl . 'css/'
    , 'vendorUrl' => $assetsUrl . 'vendor/'

    , 'connectorUrl' => $connectorUrl
    , 'actionUrl' => $actionUrl
    , 'modelPath' => $corePath . 'model/'
    , 'corePath' => $corePath

    , 'cultureKey' => $this->modx->getOption('cultureKey')

    , 'json_response' => true
    ), $config);

    $this->modx->lexicon->load('ms2form:default');

    $this->authenticated = $this->modx->user->isAuthenticated($this->modx->context->get('key'));
  }

  /**
   * Initializes component into different contexts.
   *
   * @param string $ctx The context to load. Defaults to web.
   * @param array $scriptProperties
   *
   * @return boolean
   */
  public function initialize($ctx = 'web')
  {
    if (!$this->pdoTools) {
      $this->loadPdoTools();
    }
    $this->pdoTools->setConfig($this->config);

    $this->config['ctx'] = $ctx;
    $this->initializeMediaSource($this->config['ctx']);
    if (!empty($this->initialized[$ctx])) {
      return true;
    }
    switch ($ctx) {
      case 'mgr':
        break;
      default:
        if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
          $sorceProperties = $this->mediaSource->properties;
          $config_js = preg_replace(array('/^\n/', '/\t{6}/'), '', '
            Ms2formConfig = {
              ctx: "' . $ctx . '"
              ,cultureKey: "' . $this->config['cultureKey'] . '"
              ,vendorUrl: "' . $this->config['vendorUrl'] . '"
              ,cssUrl: "' . $this->config['cssUrl'] . 'web/"
              ,actionUrl: "' . $this->config['actionUrl'] . '"
              ,close_all_message: "' . $this->modx->lexicon('ms2form_message_close_all') . '"
              ,source: {
                allowedFileTypes: "' . $sorceProperties['allowedFileTypes']['value'] . '"
                ,maxUploadWidth: "' . $sorceProperties['maxUploadWidth']['value'] . '"
                ,maxUploadHeight: "' . $sorceProperties['maxUploadHeight']['value'] . '"
                ,maxUploadSize: "' . $sorceProperties['maxUploadSize']['value'] . '"
              }
            };
          ');
          $config_js = "<script type=\"text/javascript\">\n" . $config_js . "\n</script>";
          $this->modx->regClientStartupScript($config_js, true);
          if ($css = trim($this->modx->getOption('ms2form_frontend_css'))) {
            $this->modx->regClientCSS($css);
          }
          if ($js = trim($this->modx->getOption('ms2form_frontend_js'))) {
            if (!empty($js) && preg_match('/\.js/i', $js)) {
              $jsCurl = '
                <script type="text/javascript">
                if(typeof curl == "undefined") {
                  document.write("<script src=\"' . $this->config['vendorUrl'] . 'curl/dist/curl-with-js-and-domReady/curl.js\" type=\"text/javascript\"><\/script>");
                  }
              </script>';
              $jsCurl = preg_replace(array('/^\n/', '/\t{7}/'), '', $jsCurl);

              $this->modx->regClientStartupScript($jsCurl, true);
              $this->modx->regClientStartupScript($js);
            }
          }
        }
        $this->initialized[$ctx] = true;
        break;
    }
    return true;
  }

  /**
   * Loads an instance of pdoTools
   *
   * @return boolean
   */
  public function loadPdoTools()
  {
    if (!is_object($this->pdoTools) || !($this->pdoTools instanceof pdoTools)) {
      /** @var pdoFetch $pdoFetch */
      $fqn = $this->modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
      if ($pdoClass = $this->modx->loadClass($fqn, '', false, true)) {
        $this->pdoTools = new $pdoClass($this->modx, $this->config);
      }
      return true;
    }
    return false;
  }

  /**
   * Method for transform array to placeholders
   *
   * @var array $array With keys and values
   * @var string $prefix Prefix for array keys
   *
   * @return array $array Two nested arrays with placeholders and values
   */
  public function makePlaceholders(array $array = array(), $prefix = '')
  {
    if (!$this->pdoTools) {
      $this->loadPdoTools();
    }
    return $this->pdoTools->makePlaceholders($array, $prefix);
  }

  /**
   * Upload file for msproduct
   *
   * @param $data
   *
   * @return array|string
   */
  public function fileUpload($data)
  {
    if (!$this->authenticated || empty($this->config['allowFiles'])) {
      return $this->error('ms2form_err_access_denied');
    }
    $data['file'] = $_FILES['file'];
    $data['rank'] = $_REQUEST['rank'];
    $data['source'] = $this->config['source'];
    /* @var modProcessorResponse $response */
    $response = $this->modx->runProcessor('web/gallery/upload', $data, array('processors_path' => dirname(dirname(dirname(__FILE__))) . '/processors/'));
    if ($response->isError()) {
      return $this->error($response->getMessage());
    }
    $file = $response->getObject();
    $tpl = $file['type'] == 'image'
      ? $this->config['tplImage']
      : $this->config['tplFile'];
    $html = $this->getChunk($tpl, $file);
    return $this->success("ok", ['html' => $html, 'file' => $file]);
  }

  /**
   * Delete uploaded file
   *
   * @param $id
   *
   * @return array|string
   */
  public function fileDelete($id)
  {
    if (!$this->authenticated || empty($this->config['allowFiles'])) {
      return $this->error('ms2form_err_access_denied');
    }
    /** @var modProcessorResponse $response */
    $response = $this->modx->runProcessor('web/gallery/delete', array('id' => $id, 'source' => $this->config['source']), array('processors_path' => dirname(dirname(dirname(__FILE__))) . '/processors/'));
    if ($response->isError()) {
      return $this->error($response->getMessage());
    }

    return $this->success('', array('id' => $id));
  }

  /**
   * This method returns an error
   *
   * @param string $message A lexicon key for error message
   * @param array $data .Additional data, for example cart status
   * @param array $placeholders Array with placeholders for lexicon entry
   *
   * @return array|string $response
   */
  public function error($message = '', $data = array(), $placeholders = array())
  {
    $response = array(
      'success' => false
    , 'message' => $this->modx->lexicon($message, $placeholders)
    , 'data' => $data
    );
    $this->modx->log(modX::LOG_LEVEL_ERROR, $response['message']);

    return $this->config['json_response']
      ? $this->modx->toJSON($response)
      : $response;
  }

  /**
   * Process and return the output from a Chunk by name.
   *
   * @param string $name The name of the chunk.
   * @param array $properties An associative array of properties to process the Chunk with, treated as placeholders within the scope of the Element.
   * @param boolean $fastMode If false, all MODX tags in chunk will be processed.
   *
   * @return string The processed output of the Chunk.
   */
  public function getChunk($name, array $properties = array(), $fastMode = false)
  {
    if (!$this->modx->parser) {
      $this->modx->getParser();
    }
    if (!$this->pdoTools) {
      $this->loadPdoTools();
    }

    return $this->pdoTools->getChunk($name, $properties, $fastMode);
  }

  /**
   * This method returns an success
   *
   * @param string $message A lexicon key for success message
   * @param array $data .Additional data, for example cart status
   * @param array $placeholders Array with placeholders for lexicon entry
   *
   * @return array|string $response
   * */
  public function success($message = '', $data = array(), $placeholders = array())
  {
    $response = array(
      'success' => true
    , 'message' => $this->modx->lexicon($message, $placeholders)
    , 'data' => $data
    );
    return $this->config['json_response']
      ? $this->modx->toJSON($response)
      : $response;
  }


  /**
   * Create Product through processor and redirect to it
   *
   * @param array $data section, pagetitle, text, etc
   *
   * @return array
   */
  public function productSave(array $data)
  {
    $source = $this->config['source'];

    $allowedFields = array_map('trim', explode(',', $this->config['allowedFields']));
    $allowedFields = array_unique(array_merge($allowedFields, array('parent', 'pagetitle', 'content')));
    $requiredFields = array_map('trim', explode(',', $this->config['requiredFields']));
    $requiredFields = array_unique(array_merge($requiredFields, array('parent', 'pagetitle', 'content')));

    $fields = array();
    foreach ($allowedFields as $field) {
      if (in_array($field, $allowedFields) && array_key_exists($field, $data)) {
        $value = $data[$field];
        if ($field !== 'content' && $field !== 'tags') {
          $value = $this->sanitizeString($value);
        }
        $fields[$field] = $value;
      }
    }
    // fix empty tags bug
    if (!$data['tags']) {
      $fields['tags'] = array();
    }

    $errors = array();
    foreach ($requiredFields as $v) {
      if (empty($fields[$v])) {
        $errors[$v] = $this->modx->lexicon('field_required');
      }
    }
    if (!empty($errors)) {
      return $this->error($this->modx->lexicon('ms2form_err_form'), $errors);
    }

    $fields['class_key'] = 'msProduct';
    $fields['source'] = $source;

    if (!empty($data['pid'])) {
      $fields['id'] = (integer)$data['pid'];
      $fields['context_key'] = $data['context_key'];
      $fields['alias'] = $data['alias'];
      $response = $this->modx->runProcessor('mgr/product/update', $fields, array('processors_path' => MODX_CORE_PATH . 'components/minishop2/processors/'));
      $flagNew = false;
    } else {
      $response = $this->modx->runProcessor('mgr/product/create', $fields, array('processors_path' => MODX_CORE_PATH . 'components/minishop2/processors/'));
      $flagNew = true;
    }
    /* @var modProcessorResponse $response */
    if ($response->isError()) {
      $message = $response->getMessage();
      if (empty($message)) {
        $message = $this->modx->lexicon('ms2form_err_form');
      }
      $tmp = $response->getFieldErrors();
      $errors = array();
      foreach ($tmp as $v) {
        $errors[$v->field] = $v->message;
      }
      return $this->error($message, $errors);
    }
    $productId = $response->response['object']['id'];

    //msProductCategoreisMemberProcessor
    $responseCategories = $this->modx->runProcessor('web/product/categories', array('productId' => $productId, 'categories' => $data['parents'], 'new' => $flagNew), array('processors_path' => dirname(dirname(dirname(__FILE__))) . '/processors/'));
    if ($responseCategories->isError()) {
      return $this->error($responseCategories->getMessage());
    }

    // move msProductFiles
    if (empty($data['pid']) and $data['files']) {
      /** @var modProcessorResponse $responseMove */
      $responseMove = $this->modx->runProcessor('web/gallery/move_multiple', array('productId' => $productId, 'files' => $data['files'], 'source' => $source), array('processors_path' => dirname(dirname(dirname(__FILE__))) . '/processors/'));
      if ($responseMove->isError()) {
        return $this->error($responseMove->getMessage(), $responseMove->getFieldErrors());
      }
    }

    // send email
    if ($bcc = $this->modx->getOption('ms2form_mail_bcc')) {
      $bcc = array_map('trim', explode(',', $bcc));
      if (!empty($bcc) && $resource = $this->modx->getObject('msProduct', $productId)) {
        $resource = $resource->toArray();
        foreach ($bcc as $uid) {
          if (!$this->modx->getOption('ms2form_mail_createdby')) {
            if ($uid == $resource['createdby']) {
              continue;
            }
          }
          $this->sendMail(
            $uid,
            $this->modx->lexicon('ms2form_email_bcc', $resource),
            $this->getChunk($this->config['tplEmailBcc'], $resource, false)
          );
        }
      }
    }


    // updateProductImage
    /* @var msProduct $product */
    $product = $this->modx->getObject('msProduct', $productId);
    $product->updateProductImage();

    if (empty($data['published'])) {
      $productId = $data['parent'];
    }
    $redirect = $this->modx->makeUrl($productId, '', '', 'full');

    return $this->success('', array('redirect' => $redirect));
  }

  public function initializeMediaSource($ctx = '')
  {
    if (is_object($this->mediaSource) && $this->mediaSource instanceof modMediaSource) {
      return $this->mediaSource;
    } else {
      if ($this->mediaSource = $this->modx->getObject('sources.modMediaSource', $this->config['source'])) {
        if (empty($ctx)) {
          $ctx = $this->config['ctx'];
        }
        $this->mediaSource->set('ctx', $ctx);
        $this->mediaSource->initialize();
        $this->mediaSource->properties = $this->mediaSource->getProperties();
        return $this->mediaSource;
      } else {
        return false;
      }
    }
  }

  /**
   * Sanitize MODX tags
   *
   * @param string $string Any string with MODX tags
   *
   * @return string String with html entities
   */
  public function sanitizeString($string = '')
  {
    $string = htmlentities(trim($string), ENT_QUOTES, "UTF-8");
    $string = preg_replace('/^@.*\b/', '', $string);

    $arr1 = array('[', ']', '`');
    $arr2 = array('&#091;', '&#093;', '&#096;');
    return str_replace($arr1, $arr2, $string);
  }

  public function getListTag($data)
  {
    /** @var modProcessorResponse $response */
    $response = $this->modx->runProcessor('web/product/getlist_tag', $data, array('processors_path' => dirname(dirname(dirname(__FILE__))) . '/processors/'));
    if ($response->isError()) {
      return $this->error($response->getMessage());
    }
    $tags = $response->getObject();
    return $this->success('', $tags);
  }

  public function getListCategory($data)
  {
    $data['config'] = $this->config;
    /** @var modProcessorResponse $response */
    $response = $this->modx->runProcessor('web/product/getlist_category', $data, array('processors_path' => dirname(dirname(dirname(__FILE__))) . '/processors/'));
    if ($response->isError()) {
      return $this->error($response->getMessage());
    }
    $tags = $response->getObject();
    return $this->success('', $tags);
  }

  public function sendMail($uid, $subject, $body)
  {
    /* @var modPHPMailer $mail */
    $mail = $this->modx->getService('mail', 'mail.modPHPMailer');
    $mail->setHTML(true);
    $mail->set(modMail::MAIL_SUBJECT, $subject);
    $mail->set(modMail::MAIL_BODY, $body);
    $mail->set(modMail::MAIL_SENDER, $this->modx->getOption('ms2form.mail_from', null, $this->modx->getOption('emailsender'), true));
    $mail->set(modMail::MAIL_FROM, $this->modx->getOption('ms2form.mail_from', null, $this->modx->getOption('emailsender'), true));
    $mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('ms2form.mail_from_name', null, $this->modx->getOption('site_name'), true));

    /* get user and profile by user id */
    if ($user = $this->modx->getObject('modUser', $uid)) {
      $profile = $user->getOne('Profile');
      if (!$user->get('active') || $profile->get('blocked')) {
        return 'This user is not active.';
      }
      $email = $profile->get('email');
    } else {
      return "can not get user $uid";
    }

    $mail->address('to', $email);
    if (!$mail->send()) {
      $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: ' . $mail->mailer->ErrorInfo);

      $mail->reset();
      return false;
    }

    $mail->reset();
    return true;

  }
}
