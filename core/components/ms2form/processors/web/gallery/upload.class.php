<?php
require_once MODX_CORE_PATH . 'components/minishop2/processors/mgr/gallery/upload.class.php';
require_once MODX_CORE_PATH . 'components/gdriver/model/gdriver/gdrivermsproduct.trait.php';

class ms2FormProductFileUploadProcessor extends msProductFileUploadProcessor {
  use gdriverMsProduct;
  /* @var msProduct $product */
  private $product = 0;
  public $languageTopics = array('minishop2:default','minishop2:product');
  /* @var gdriverMediaSource $mediaSource */
  public $mediaSource;

  public function initialize() {
    /* @var msProduct $product */
    $pid = $this->getProperty('pid');
    if (!$this->product = $this->modx->getObject('msProduct', $pid)) {
      $this->product = $this->modx->newObject('msProduct');
      $this->product->set('id', 0);
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
      return $this->modx->lexicon('ms2_gallery_err_no_source');
    }

    return true;
  }

  public function process() {
    $data = $this->properties['file'];
    if (!$data) {
      return $this->failure($this->modx->lexicon('ms2_err_gallery_ns'));
    }
    $data['stream'] = file_get_contents($data['tmp_name']);
    $data['rank'] = $this->properties['rank'];
    $tmp = getimagesize($data['tmp_name']);
    $data['properties'] = array(
      'size' => strlen($data['stream']),
      'width' => $tmp[0],
      'height' => $tmp[1],
      'bits' => $tmp['bits'],
      'mime' => $tmp['mime'],
    );
    unlink($data['tmp_name']);

    switch ($this->mediaSource->get('class_key')) {
      case 'gdriverMediaSource':
        $result = $this->createGriverMsProductFile($data,$this->product->id);
        return $result;
        break;
      default:
//      todo-me add file suorse
//        $this->mediaSource->createContainer($product_file->path, '/');
//        $file = $this->mediaSource->createObject(
//          $product_file->get('path')
//          , $product_file->get('file')
//          , $data['stream']
//        );
//        if ($file) {
//          $url = $this->mediaSource->getObjectUrl($product_file->get('path') . $product_file->get('file'));
//          $product_file->set('url', $url);
//          $product_file->save();
//          $generate = $product_file->generateThumbnails($this->mediaSource);
//          if ($generate !== true) {
//            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not generate thumbnails for image with id = ' . $product_file->get('id') . '. ' . $generate);
//            return $this->failure($this->modx->lexicon('ms2_err_gallery_thumb'));
//          } else {
//            $this->product->updateProductImage();
//            return $this->success($url);
//          }
//        } else {
//          return $this->failure($this->modx->lexicon('ms2_err_gallery_save') . ': ' . print_r($this->mediaSource->getErrors(), 1));
//        }
    }
  }
}

return 'ms2FormProductFileUploadProcessor';
