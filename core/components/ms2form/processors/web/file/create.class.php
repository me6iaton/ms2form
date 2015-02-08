<?php

class uFileCreateProcessor extends modObjectCreateProcessor {
  /* @var uFile $object */
  public $object;
  public $objectType = 'object';
  public $classKey = 'uFile';
  public $primaryKeyField = 'id';
  public $languageTopics = array('uploadify:default');
  public $permission = '';


  public function beforeSet() {
    if (!$this->getProperty('raw')) {
      return $this->failure($this->modx->lexicon('uf_err_content'));
    }

    return parent::beforeSet();
  }

  public function beforeSave() {
    if (empty($this->modx->request)) {
      $this->modx->getRequest();
    }

    $raw = $this->object->get('raw');
    if (!$url = $this->object->createFile($raw)) {
      return $this->failure($this->modx->lexicon('uf_err_save'));
    }
    $this->object->set('raw', '');

    $this->object->fromArray(array(
      'createdon' => date('Y-m-d H:i:s')
      ,'createdby' => empty($this->modx->user->id) && !empty($_SESSION['uid']) ? $_SESSION['uid'] : $this->modx->user->id
      ,'ip' => $this->modx->request->getClientIp()
      ,'url' => $url
      ,'parent' => 0
      ,'size' => strlen($raw)
    ));

    return parent::beforeSave();
  }
}

return 'uFileCreateProcessor';