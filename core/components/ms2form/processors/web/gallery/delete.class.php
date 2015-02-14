<?php

class ms2FormProductFileDeleteProcessor extends modObjectProcessor {
  public $classKey = 'msProductFile';
  public $permission = 'msproductfile_save';
  public $languageTopics = array('ms2form:default');
  public $mediaSource;

  /** {@inheritDoc} */
  public function initialize() {
    if (!$this->modx->hasPermission($this->permission)) {
      return $this->modx->lexicon('ms2form_err_access_denied');
    }
    return true;
  }


  /** {@inheritDoc} */
  public function process() {
    $id = $this->getProperty('id');
    /* @var msProductFile $file */
    if (!$file = $this->modx->getObject($this->classKey, $id)) {
      return $this->failure($this->modx->lexicon('ms2form_err_file_ns'));
    }
    elseif ($file->createdby != $this->modx->user->id) {
      return $this->failure($this->modx->lexicon('ms2form_err_file_owner'));
    }
    if($file->get('product_id') == 0){
      $mediaSource = $this->modx->getObject('sources.modMediaSource', $this->getProperty('source'));
      $mediaSource->set('ctx', $this->modx->context->key);
      if ($mediaSource->initialize()) {
        $this->mediaSource = $mediaSource;
      }else{
        return $this->failure($this->modx->lexicon('ms2form_err_source_initialize'));
      }
      if (!$this->mediaSource->removeObject($file->get('path') . $file->get('file'))) {
        $this->modx->log(xPDO::LOG_LEVEL_ERROR,
          'Could not remove file at "' . $file->get('path') . $file->get('file') . '": ' . $this->mediaSource->errors['file']
        );
      }
      $children = $this->modx->getIterator('msProductFile', array('parent' => $file->get('id')));
      /** @var msProductFile $child */
      foreach ($children as $child) {
        if (!$this->mediaSource->removeObject($child->get('path') . $child->get('file'))) {
          $this->modx->log(xPDO::LOG_LEVEL_ERROR,
            'Could not remove file at "' . $child->get('path') . $child->get('file') . '": ' . $this->mediaSource->errors['file']
          );
        }
      }
      $result = $this->modx->exec("DELETE FROM {$this->modx->getTableName('msProductFile')} WHERE `id` = {$id} OR `parent` = {$id};");

    }else{
      $result = $file->remove();
    }

    if(!$result){
      return $this->failure($this->modx->lexicon('ms2form_err_file_ns'));
    }
    return $this->success();
  }

}
return 'ms2FormProductFileDeleteProcessor';
