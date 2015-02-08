<?php
require_once MODX_CORE_PATH . 'components/gdriver/model/gdriver/gdrivermsproduct.trait.php';
class ms2FormProductFileUpdatePicasaMultipleProcessor extends modObjectProcessor {
  use gdriverMsProduct;
  public $classKey = 'msProductFile';
  public $permission = 'msproductfile_save';
  /* @var gdriverMediaSource $mediaSource */
  public $mediaSource;
  private $productId;
  private $files;

  /** {@inheritDoc} */
  public function initialize() {
    if (!$this->modx->hasPermission($this->permission)) {
      return $this->modx->lexicon('access_denied');
    }
    $this->productId = $this->getProperty('pid');
    if(!isset($this->productId)){
      return "not data property pid";
    }

    if ($source = $this->getProperty('source')) {
      /** @var modMediaSource $mediaSource */
      $mediaSource = $this->modx->getObject('sources.modMediaSource', $source);
      $mediaSource->set('ctx', $this->modx->context->key);
      if ($mediaSource->initialize()) {
        $this->mediaSource = $mediaSource;
      }
    }

    if (!$this->mediaSource) {
      return $this->modx->lexicon('ticket_err_source_initialize');
    }

    $this->files = $this->getProperty('files');
    return true;
  }

  /** {@inheritDoc} */
  public function process() {
    if ($this->mediaSource->get('class_key') == 'gdriverMediaSource') {
      /** @var gdriverMediaSource $mediaSource */
      $mediaSource = $this->mediaSource;
      $response = $this->addThumbnailsToQueueUpdatePicasa($this->productId, $this->files, $mediaSource->properties['intervalUpdateThumbnailsNotParent'], $mediaSource->get('id'));
      if ($response) {
        return $this->success();
      } else {
        return $this->error();
      }
    } else {
      return $this->success();
    }
  }

}
return 'ms2FormProductFileUpdatePicasaMultipleProcessor';
