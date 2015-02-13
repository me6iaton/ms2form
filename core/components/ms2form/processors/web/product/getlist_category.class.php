<?php
/**
 * Get a list tag of product
 *
 * @package ms2form
 * @subpackage processors
 */
class ms2formCategoryGetListProcessor  extends modObjectProcessor{
  public $classKey = 'msCategory';
  /** @var pdoFetch $pdoFetch */
  private $pdoFetch;
  private $pid;
  private $config;
  public function initialize() {
    $fqn = $this->modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
    if (!$pdoClass = $this->modx->loadClass($fqn, '', false, true)) {return false;}
    $this->pdoFetch = new $pdoClass( $this->modx, (empty($this->getProperties()))?  array() : $this->getProperties());

    $this->pid = $this->getProperty('pid');
    $this->config = $this->getProperty('config');
    return true;
  }
  public function process(){
    $result = array();
    $result['all'] = array();
    $config = array(
      'hideUnsearchable' => '1',
      'parents' => $this->config['parents'],
      'resources' => $this->config['resources'],
      'includeTVs' => $this->config['parentsIncludeTVs'],
      'sortby' => $this->config['parentsSortby'],
      'sortdir' => $this->config['parentsSortdir'],
      'return' => 'data',
    );
    $msCategories = $this->pdoFetch->getCollection($this->classKey, array('class_key'=>$this->classKey ),$config);
    $result['all'] = array_map(function($msCategory) {
      return array(
        'id' => $msCategory['id'],
        'text' => $this->pdoFetch->getChunk($this->config['tplSectionRow'],$msCategory,false)
      );
    }, $msCategories);

    if($this->pid !== '0'){
      $categoryMembers = array_map(function($msMember){ return $msMember['category_id'];},$this->pdoFetch->getCollection('msCategoryMember', array('product_id'=> $this->pid)));
      $msCategoriesIds = array_map(function($msCategory){ return $msCategory['id'];},$msCategories);
      $result['product'] = array_values(array_filter($categoryMembers,function($categoryMember) use ($msCategoriesIds){
        if(in_array($categoryMember,$msCategoriesIds)) return true;
      }));
    }
    return $this->success('', $result);
  }
}
return 'ms2formCategoryGetListProcessor';
