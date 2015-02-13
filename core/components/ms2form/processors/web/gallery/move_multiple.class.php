<?php
/**
 * Class ms2FormProductFileMoveMultipleProcessor
 *
 * @property modMediaSource $mediaSource
 *
 */
class ms2FormProductFileMoveMultipleProcessor extends modObjectProcessor {
  public $classKey = 'msProductFile';
  public $permission = 'msproductfile_save';
  public $languageTopics = array('minishop2:default','minishop2:product');
  public $mediaSource;
  private $mediaSourceClassKey;
  private $productId;
  /* @var msProduct $product */
  private $product;
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

    if (!$this->product = $this->modx->getObject('msProduct', $this->productId)) {
      return $this->modx->lexicon('ms2_gallery_err_no_product');
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
    $criteria->where(array(
      'product_id' => 0
    , 'createdby' => $this->modx->user->id
    ));
    $msProductFiles = $this->modx->getCollection('msProductFile', $criteria);

    if(empty($msProductFiles)){return $this->success();}

    $this->mediaSource->renameContainer('0/' . $this->modx->user->id, $this->productId);
    if(!$this->mediaSource->moveObject('0/' . $this->productId, '/')){
      return $this->failure('error move path 0/'.$this->productId);
    }

    $subPath = '0/' . $this->modx->user->id . '/';
    /** @var msProductFile $item */
    foreach ($msProductFiles as $item) {
      $file = $item->get('file');
      $path = $item->get('path');
      $item->set('product_id', $this->productId);
      $thumbSize = substr($path, strlen($subPath));
      if($thumbSize){
        $thumbPath = $this->productId . '/' . $thumbSize;
        $item->set('path', $thumbPath);
        $item->set('url', $this->mediaSource->getObjectUrl($thumbPath. $file));
      }else{
        $item->set('path', $this->productId . '/');
        $item->set('url', $this->mediaSource->getObjectUrl($this->productId . '/' . $file));
      }
      $item->save();
    }
//    $this->product->updateProductImage();
    return $this->success();
  }

}
return 'ms2FormProductFileMoveMultipleProcessor';
