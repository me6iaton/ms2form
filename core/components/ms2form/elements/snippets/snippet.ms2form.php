<?php
/* @var array $scriptProperties */
/* @var ms2form $ms2form */

$ms2form = $modx->getService('ms2form', 'ms2form', $modx->getOption('ms2form_core_path', null, $modx->getOption('core_path') . 'components/ms2form/') . 'model/ms2form/', $scriptProperties);
$ms2form->initialize($modx->context->key, $scriptProperties);

if (!$modx->user->isAuthenticated()) {
  return $modx->lexicon('ticket_err_no_auth');
}

if (empty($tplFormCreate)) {
  $tplFormCreate = 'tpl.ms2form.create';
}
if (empty($tplFormUpdate)) {
  $tplFormUpdate = 'tpl.ms2form.update';
}
if (empty($tplTagRow)) {
  $tplTagRow = 'tpl.ms2form.tag.row';
}
if (empty($tplFiles)) {
  $tplFiles = 'tpl.ms2form.files';
}
if (empty($tplFile)) {
  $tplFile = $ms2form->config['tplFile'] = 'tpl.ms2form.file';
}
if (empty($tplImage)) {
  $tplImage = $ms2form->config['tplImage'] = 'tpl.ms2form.image';
}
if (empty($templates)) {
  $templates = 0;
}
$ms2_product_thumbnail_size = $modx->getOption('ms2_product_thumbnail_size', $scriptProperties, $modx->getOption('ms2_product_thumbnail_size'));

$pid = !empty($_REQUEST['pid']) ? (integer)$_REQUEST['pid'] : 0;
$data = array();
if (empty($parent)) {
  $data['parent'] = '0';
} else {
  $data['parent'] = $parent;
}

// Update of msProduct
if (!empty($pid)) {
  $tplWrapper = $tplFormUpdate;
  /* @var msProduct $product */
  if ($product = $modx->getObject('msProduct', array('id' => $pid))) {
    if ($product->get('createdby') != $modx->user->id && !$modx->hasPermission('edit_document')) {
      return $modx->lexicon('ticket_err_wrong_user');
    }
    $charset = $modx->getOption('modx_charset');
    $allowedFields = array_map('trim', explode(',', $scriptProperties['allowedFields']));
    $allowedFields = array_unique(array_merge($allowedFields, array('parent', 'pagetitle', 'content')));

    $fields = array_keys($modx->getFieldMeta('msProduct'));
//		$productData = $product->loadData();
//		$categories = $product->getMany('Categories');
    foreach ($allowedFields as $field) {
      if (in_array($field, $fields)) {
        $value = $product->get($field);
      } else {
        $tvId = (int)trim($field, 'tv');
        $value = $product->getTVValue($tvId);
      }
      if (is_string($value)) {
        $value = html_entity_decode($value, ENT_QUOTES, $charset);
        $value = str_replace(array('[^', '^]', '[', ']'), array('&#91;^', '^&#93;', '{{{{{', '}}}}}'), $value);
        $value = htmlentities($value, ENT_QUOTES, $charset);
      }
      $data[$field] = $value;
    }
    $data['id'] = $product->id;
    $data['published'] = $product->published;
    $data['alias'] = $product->alias;
    $data['context_key'] = $product->context_key;
  } else {
    return $modx->lexicon('ticket_err_id', array('id' => $pid));
  }
} else {
  $tplWrapper = $tplFormCreate;
}
// todo-me Get available sections for msProduct create


// Get templates list
$data['templates'] = "";
if ($templates = explode(',', $templates)) {
  foreach ($templates as $template) {
    $selected = '';
    if ($template = explode('==', $template)) {
      if (!empty($pid)) {
        if ($product->template == $template[0]) {
          $selected = 'selected';
        }
      }
      $data['templates'] .= "<option $selected value=\"$template[0]\">$template[1]</option>";
    } else {
      if (!empty($pid)) {
        if ($product->template == $template) {
          $selected = 'selected';
        }
      }
      $data['templates'] .= "<option $selected value=\"$template\">$template</option>";
    }
  }
} else {
  $data['templates'] .= "<option value='0'>0</option>";
}

//<editor-fold desc=" Get modTemplate list">
//if($templates = explode(',',$scriptProperties['templates']) or !$scriptProperties['templates']){
//	/* @var xPDOQuery $query */
//	$query = $this->modx->newQuery('modTemplate');
//	if($templates){
//		foreach ($templates as $templateId){
//		$orConditions[]= array('id'=>$templateId);
//		}
//		$query->where($orConditions,xPDOQuery::SQL_OR);
//	}
//	$modTemplates = $this->modx->getCollection('msProductFile', $criteria);
//	foreach ($modTemplates as $modTemplate){
//		$templteOptions .= "<option value='14'>Блог пост с галлереей</option>";
//	}
//}else{
//
//}
//</editor-fold>


// add multi upload files
if (!empty($allowFiles)) {
  $q = $modx->newQuery('msProductFile');
  if (empty($pid)) {
    $q->where(array('product_id' => 0, 'createdby' => $modx->user->id));
    $q->andCondition(array('path' => '0/' . $ms2_product_thumbnail_size . '/'), null, 1);
    $q->orCondition(array('path' => '0/'), null, 1);
  } else {
//		$q->where(array('product_id' => $pid,'createdby' => $modx->user->id));
    //fix-me  'createdby' => $modx->user->id
    $q->where(array('product_id' => $pid));
    $q->andCondition(array('path' => $pid . '/' . $ms2_product_thumbnail_size . '/'), null, 1);
    $q->orCondition(array('path' => $pid . '/'), null, 1);
//		$q->where(array('product_id' => $pid, 'path'=> $pid.'/'.$ms2_product_thumbnail_size.'/'));
  }
  $q->sortby('createdon', 'ASC');
  $collection = $modx->getIterator('msProductFile', $q);
  $files = '';
  $files_arr = array();
  /** @var msProductFile $item */
  foreach ($collection as $item) {
    $item = $item->toArray();
    /** @var array $item */
//		$item['size'] = round($item['size'] / 1024, 2);
    $itemThumb = new CallbackFilterIterator($collection, function (msProductFile $current) use ($item) {
      if ($current->get('parent') == $item['id']) return true;
    });
    $itemThumb->rewind();
    if ($itemThumb->current()) {
      $item['thumb'] = $itemThumb->current()->get('url');
    }
    $tpl = $item['type'] == 'image'
      ? $tplImage
      : $tplFile;
    $files .= $ms2form->getChunk($tpl, $item);
  }
  $data['files'] = $ms2form->getChunk($tplFiles, array(
    'files' => $files,
  ));
}

$output = $ms2form->getChunk($tplWrapper, $data);
$key = md5($modx->toJSON($ms2form->config));
$_SESSION['ms2form'][$key] = $ms2form->config;

$output = str_ireplace('</form>', "\n<input type=\"hidden\" name=\"form_key\" value=\"{$key}\" />\n</form>", $output);
return $output;
