<?php
/**
 * Get a list tag of product
 *
 * @package ms2form
 * @subpackage processors
 */
class ms2FormProductGetTagsListProcessor  extends modObjectProcessor{
  public $classKey = 'msProductOption';
  /** @var pdoFetch $pdoFetch */
  private $pdoFetch;
  private $pid;
  public $languageTopics = array('ms2form:default');

  public function initialize() {
    $fqn = $this->modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
    if (!$pdoClass = $this->modx->loadClass($fqn, '', false, true)) {return false;}
    $this->pdoFetch = new $pdoClass( $this->modx, (empty($this->getProperties()))?  array() : $this->getProperties());

    $this->pid = $this->getProperty('pid');

    return true;
  }
  public function process(){
    $result = array();
    $queryAll = array(
    'class' => $this->classKey
    ,'where' => $this->modx->toJSON(['key'=>'tags'])
    ,'select' => $this->modx->toJSON(['value'=>'msProductOption.value'])
    ,'groupby' => 'msProductOption.value'
    ,'limit' => 0
    ,'fastMode' => true
    ,'sortby' => 'msProductOption.value'
    ,'sortdir' => 'ASC'
    ,'return' => 'data'
    );
    $this->pdoFetch->setConfig($queryAll);
    $tagsAll = $this->pdoFetch->run();
    $tagsAll = array_map(function($tag) {
      return array(
        'id' => $tag['value'],
        'text' => $tag['value']
      );
    }, $tagsAll);
    $result['all'] =  $tagsAll;

    if($this->pid !== '0'){
      $queryProduct = array(
      'class' => $this->classKey
      ,'where' => $this->modx->toJSON(['product_id'=>$this->pid,'key'=>'tags'])
      ,'select' => $this->modx->toJSON(['value'=>'msProductOption.value'])
      ,'groupby' => 'msProductOption.value'
      ,'limit' => 0
      ,'fastMode' => true
      ,'sortby' => 'msProductOption.value'
      ,'sortdir' => 'ASC'
      ,'return' => 'data'
      );
      $this->pdoFetch->setConfig($queryProduct);
      $tagsProduct = $this->pdoFetch->run();
      $tagsProduct = array_map(function($tag) {return $tag['value'];}, $tagsProduct);
      $result['product'] =  $tagsProduct;
    }
    return $this->success('', $result);
  }
}
return 'ms2FormProductGetTagsListProcessor';
