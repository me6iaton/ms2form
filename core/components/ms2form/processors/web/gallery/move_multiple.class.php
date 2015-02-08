<?php
require_once MODX_CORE_PATH . 'components/gdriver/model/gdriver/gdrivermsproduct.trait.php';

/**
 * Class ms2FormProductFileMoveMultipleProcessor
 *
 * @property modMediaSource $mediaSource
 *
 */
class ms2FormProductFileMoveMultipleProcessor extends modObjectProcessor {
  use gdriverMsProduct;
  public $classKey = 'msProductFile';
  public $permission = 'msproductfile_save';
  public $languageTopics = array('minishop2:default','minishop2:product');
  public $mediaSource;
  private $mediaSourceClassKey;
  private $productId;
  private $files;

  /** {@inheritDoc} */
  public function initialize() {
    if (!$this->modx->hasPermission($this->permission)) {
      return $this->modx->lexicon('access_denied');
    }
    $this->productId = $this->getProperty('productId');
    if(!isset($this->productId)){
      return "not data property productId";
    }

    if(!$this->files = $this->getProperty('files')){
      return "not data property files";
    }

    if ($source = $this->getProperty('source')) {
      $mediaSource = $this->modx->getObject('sources.modMediaSource', $source);
      $mediaSource->set('ctx', $this->modx->context->key);
      if ($mediaSource->initialize()) {
        $this->mediaSource = $mediaSource;
      }
    }

    if (!$this->mediaSource) {
      return $this->modx->lexicon('ms2_gallery_err_no_source');
    }
    $this->mediaSourceClassKey = $this->mediaSource->get('class_key');
    return true;
  }
  /** {@inheritDoc} */
  public function process() {
    $criteria = $this->modx->newQuery('msProductFile');
    $criteria->where(['product_id' => 0]);
    foreach ($this->files as $fileId) {
      $criteria->orCondition(['id' => $fileId], null, 1);
      $criteria->orCondition(['parent' => $fileId], null, 1);
    }
    $msProductFiles = $this->modx->getCollection('msProductFile', $criteria);
    /** @var msProductFile $msProductFile */
    foreach ($msProductFiles as $msProductFile) {
      // rename files
      if ($this->mediaSourceClassKey == 'gdriverMediaSource') {
        if($msProductFile->get('productId') == 0){
          $filename = preg_replace('/^\d+_/','',$msProductFile->get('file'));
          $msProductFile->set('file', $filename);
          $properties =  $msProductFile->get('properties');
          if($msProductFile->get('parent') == 0){
            $this->mediaSource->renameObject($properties['gdriveFileId'],$filename);
            // replace image urls
            $urlsReplaceFrom[] = $msProductFile->get('url');
            $urlsReplaceTo[] =  $properties['urlGdrive'];

            $msProductFile->set('url',$properties['urlGdrive']);
          }
        }
      }else{
        //todo-me add filesMove in fileMediaSource
        return $this->error('filesMove in not fileMediaSource to-do');
      }
      $msProductFile->set('product_id', $this->productId);
      $msProductFile->save();
    }
    // replace image urls
    if(isset($urlsReplaceFrom) && isset($urlsReplaceTo))
      $this->replaceImageUrl($this->productId, $urlsReplaceFrom,$urlsReplaceTo,false);

    if ($this->mediaSourceClassKey == 'gdriverMediaSource') {
      /** @var gdriverMediaSource $mediaSource */
      $mediaSource = $this->mediaSource;
      $mediaSource->properties['intervalUpdateThumbnails'] = 30;

      $response = $this->addFilesToQueueMove($this->productId, $this->files, $mediaSource->get('id'), $mediaSource->properties['intervalUpdateThumbnails']);

      if ($response) {
        return $this->success();
      } else {
        return $this->error('ms2FormProductFileMoveMultipleProcessor: error addFilesToQueueMove');
      }
    } else {
      //todo-me add filesMove in fileMediaSource
      return $this->error('filesMove in not fileMediaSource to-do');
    }
  }

}
return 'ms2FormProductFileMoveMultipleProcessor';
