<form class="well create" method="post" action="" id="ms2form" role="form">
  <span id="question" class="icon icon-question-circle"></span>

  <div id="ticket-preview-placeholder"></div>

  <input type="hidden" name="pid" value="0">
  <input type="hidden" name="parent" value="[[+parent]]">
  <input type="hidden" name="published" value="1"/>
  <input type="hidden" name="hidemenu" value="0"/>

  <div class="form-group">
    <label>[[%ticket_pagetitle]]</label>
    <span class="text-danger">*</span>
    <input type="text" class="form-control" placeholder="[[%ticket_pagetitle]]" name="pagetitle" value="" maxlength="50"
           id="ticket-pagetitle"/>
  </div>

  [[+templates]]

  <div class="form-group">
    <label>[[%ms2form_categories]]</label>
    <br/>
    <input type="hidden" class="form-control" id="ms2formCategories">
  </div>
  <div class="form-group">
    <label>[[%ms2form_tags]]</label>
    <br/>
    <input type="hidden" class="form-control" id="ms2formTags">
  </div>


  <div class="form-group popover-help" id="form-group-content">
    <input id="content" name="content" type="hidden" value="[[+content]]"/>

    <p data-trigger="manual" class="popover-help" data-html="true" data-toggle="popover" data-placement="bottom"
       data-content="[[%ms2form_help_editor]]"></p>

    <div id="ms2form-editor">
    </div>
  </div>

  <div class="form-group">
    <div class="ticket-form-files popover-help" data-toggle="popover" data-placement="top"
         data-content="[[%ms2form_help_files]]">
      [[+files]]
    </div>
  </div>

  <div class="form-actions">
    <input type="submit" class="btn btn-primary submit" value="[[%ticket_save]]" title="Ctrl + Shift + Enter"/>
  </div>
</form>

<!--pdotools_templates
  <div class="form-group">
    <label>[[%ms2form_template]] [[+template]]</label>
    <br/>
    <select class="form-control popover-help" name="template" id="ms2formTemplate" data-html="true"
            data-toggle="popover" data-placement="bottom" data-content="[[%ms2form_help_template]]">
      [[+templates]]
    </select>
  </div>
-->
<!--pdotools_!templates
  <input type="hidden" name = "template" value="[[+template]]">
-->