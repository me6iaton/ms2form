<form class="well create" method="post" action="" id="ms2form" role="form">
  <input type="hidden" name="pid" value="0">
  <input type="hidden" name="parent" value="[[+parent]]">
  <input type="hidden" name="published" value="1"/>
  <input type="hidden" name="hidemenu" value="0"/>

  <div class="form-group">
    <label>[[%ms2form_pagetitle]]</label>
    <span class="text-danger">*</span>
    <input type="text" class="form-control" placeholder="[[%ms2form_pagetitle]]" name="pagetitle" value="" maxlength="50" id="ms2formPagetitle"/>
  </div>

  [[+templates]]

  <div class="form-group">
    <label>[[%ms2form_sections]]</label>
    <br/>
    <input type="hidden" class="form-control" id="ms2formSections">
  </div>
  <div class="form-group">
    <label>[[%ms2form_tags]]</label>
    <br/>
    <input type="hidden" class="form-control" id="ms2formTags">
  </div>


  <div class="form-group popover-help" id="formGroupContent">
    <input id="content" name="content" type="hidden" value="[[+content]]"/>
    <div id="ms2formEditor">
    </div>
  </div>

  <div class="form-group">
    <div class="ticket-form-files">
      [[+files]]
    </div>
  </div>

  <div class="form-actions">
    <input type="submit" class="btn btn-primary submit" value="[[%ms2form_save]]"/>
  </div>
</form>

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