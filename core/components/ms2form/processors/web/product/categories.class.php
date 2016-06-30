<?php

class msProductCategoreisMemberProcessor extends modObjectCreateProcessor {
  public $classKey = 'msCategoryMember';
  public $permission = 'msproduct_save';
  public $languageTopics = array('ms2form:default');


  /** {@inheritDoc} */
  public function initialize() {
    if (!$this->modx->hasPermission($this->permission)) {
      return $this->modx->lexicon('ms2form_err_access_denied');
    }
    return parent::initialize();
  }


  /** {@inheritDoc} */
  public function process() {
    $productId = $this->getProperty('productId');
    $catIds = $this->getProperty('categories');
    $flagNew = $this->getProperty('new');
    if (!$productId) {
      return $this->error('empty property product_id');
    }

	foreach ( $catIds as $key => $tmp ) {
		$tmpcat = $this->modx->getObject('msCategory', intval($tmp));
		if ( !$tmpcat ) {
			$tmpcat = $this->modx->newObject('msCategory');
			$tmpcat->fromArray(array(
				'pagetitle' => $tmp,
				'parent' => $this->modx->getOption('ms2form_categories_parent', $scriptProperties, 0),
				'published' => 1
			));
			$tmpcat->save();
			$catIds[$key] = $tmpcat->get('id');
		}
	}

    if (!$flagNew) {
      $msCatMembers = $this->modx->getCollection('msCategoryMember', array('product_id' => $productId));
      if ($msCatMembers) {
        // remove old msCategoryMember
        foreach ($msCatMembers as $msCatMember) {
          $catId = $msCatMember->category_id;
          if($catIds){
            if (!in_array($catId, $catIds)) {
              $msCatMember->remove();
            }
          }else{
            $msCatMember->remove();
          }
        }
        // skip existing msCategoryMember
        if($catIds){
          foreach ($catIds as $key => $catId) {
            if (array_key_exists("$productId-$catId", $msCatMembers)) {
              unset($catIds[$key]);
            }
          }
        }
      }
    }

    if($catIds){
      foreach($catIds as $catId){
        $res = $this->modx->newObject('msCategoryMember');
        $res->set('product_id', $productId);
        $res->set('category_id', $catId);
        $res->save();
      }
    }

    return $this->success('');
  }

}

return 'msProductCategoreisMemberProcessor';
