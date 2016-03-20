<?php

class ms2FormProductFileSortProcessor extends modObjectProcessor {
    public $classKey = 'msProductFile';
    public $permission = 'msproductfile_save';


    /** {@inheritDoc} */
    public function initialize() {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('ms2form_err_access_denied');
        }
        return true;
    }


    /** {@inheritDoc} */
    public function process() {
        $rank = $this->getProperty('rank');
        /** @var msProductFile $files */
        foreach($rank as $idx => $id){
            if (!$file = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('ms2form_err_file_ns'));
            }
            elseif ($file->createdby != $this->modx->user->id && !$this->modx->user->isMember('Administrator')) {
                return $this->failure($this->modx->lexicon('ms2form_err_file_owner'));
            }

            $file->set('rank', $idx);
            $file->save();
            
            foreach ($this->modx->getIterator('msProductFile', array('parent' => $file->get('id'))) as $child) {
                $child->set('rank', $idx);
                $child->save();
            }
        }

        return $this->success();
    }

}
return 'ms2FormProductFileSortProcessor';
