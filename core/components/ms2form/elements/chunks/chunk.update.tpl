<form class="well" method="post" action="" id="ms2form" role="form">
  <input type="hidden" id="ms2formFormKey" name="form_key" value="[[+formKey]]">
  <input type="hidden" name="pid" value="[[+id]]">
  <input type="hidden" name="parent" value="[[+parent]]">
  <input type="hidden" name="alias" value="[[+alias]]">
  <input type="hidden" name="context_key" value="[[+context_key]]">
  <input type="hidden" name="published" value="1"/>
  <input type="hidden" name="hidemenu" value="0"/>
  <input type="hidden" name="redirectPublished" value="[[+redirectPublished]]"/>
  <input type="hidden" id="ms2formTagsNew" value="[[+tagsNew]]"/>

  <div class="form-group">
    <label>[[%ms2form_pagetitle]]</label>
    <input type="text" class="form-control" placeholder="[[%ms2form_pagetitle]]" name="pagetitle" value="[[+pagetitle]]" maxlength="50" id="ms2form-pagetitle"/>
  </div>

  [[+parentMse2form.element]]

  [[+templates]]

  <div class="form-group">
    <label>[[%ms2form_categories]]</label>
    <br/>
    <input type="hidden" class="form-control" id="ms2formSections">
  </div>

  [[+tags]]

  <div class="form-group">
    <label>Пример TV </label>
    <br/> указать в параметре allowedFields=`parent,pagetitle,content,published,template,hidemenu,tags,tv1`
    <br/>
    <input type="text" value="[[+tv1]]" name="tv1" class="form-control">
  </div>


  <div class="form-group popover-help" id="formGroupContent">
    <input id="content" name="content" type="hidden" value="[[+content:escape]]"/>
    [[$tpl.ms2form.editor.[[+editor]]?content=`[[+content]]`]]
  </div>

  <div class="ticket-form-files popover-help">
    [[+files]]
  </div>

  <div class="form-actions">
    <input type="submit" id="ms2formSubmit" class="btn btn-primary submit" value="[[%ms2form_save]]"/>
  </div>
</form>

<!--pdotools_parentMse2form.element
  <div class="form-group">
    <label>[[%ms2form_category]]</label>
    <span class="text-danger">*</span>
    <input type="text" data-key=[[+mse2formKey]] id="ms2formParentMse2form" class="form-control disable-sisyphus" name="[[+parentMse2form.queryVar]]" placeholder="[[%ms2form_search]]" value="" />
  </div>
-->

<!--pdotools_tags
  <div class="form-group">
    <label>[[%ms2form_tags]]</label>
    <br/>
    <input type="hidden" class="form-control" id="ms2formTags">
  </div>
-->

<!--pdotools_templates
<div class="form-group">
  <label>[[%ms2form_template]]</label>
  <br/>
  <select class="form-control" name="template" id="ms2formTemplate">
    [[+templates]]
  </select>
</div>
-->
<!--pdotools_!templates
<input type="hidden" name="template" value="[[+template]]">
-->
