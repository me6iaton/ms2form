<?php

class ms2FormProductFileDeleteProcessor extends modObjectProcessor {
  public $classKey = 'msProductFile';
  public $permission = 'msproductfile_save';
  public $languageTopics = array('minishop2:default', 'minishop2:product','tickets:default');


  /** {@inheritDoc} */
  public function initialize() {
    if (!$this->modx->hasPermission($this->permission)) {
      return $this->modx->lexicon('access_denied');
    }
    return true;
  }


  /** {@inheritDoc} */
  public function process() {
    $id = $this->getProperty('id');
    /* @var msProductFile $file */
    if (!$file = $this->modx->getObject($this->classKey, $id)) {
      return $this->failure($this->modx->lexicon('ticket_err_file_ns'));
    }
    elseif ($file->createdby != $this->modx->user->id) {
      return $this->failure($this->modx->lexicon('ticket_err_file_owner'));
    }
    $result = $file->remove();
    if(!$result){
      return $this->failure($this->modx->lexicon('ticket_err_file_ns'));
    }
    return $this->success();
  }

}
return 'ms2FormProductFileDeleteProcessor';
