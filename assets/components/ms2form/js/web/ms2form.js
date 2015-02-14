(function() {
  var ms2form = {
    config : {
      vendorUrl : Ms2formConfig.vendorUrl,
      cssUrl : Ms2formConfig.cssUrl,
      actionUrl : Ms2formConfig.actionUrl,
      formBefore : 0,
      close_all_message : Ms2formConfig.close_all_message,
      tpanel : 1,
      thread_depth : 0,
      enable_editor : 1,
      locale: Ms2formConfig.cultureKey
    },
    initialize : function(callback) {
      var firstLibs;
      if (typeof jQuery == "undefined"){
        firstLibs = [
          ms2form.config.vendorUrl + 'when/when'
          ,'js!' + ms2form.config.vendorUrl + 'jquery/jquery.min.js'
        ];
      } else{
        firstLibs = [
          ms2form.config.vendorUrl + 'when/when'
        ]
      }
      curl(firstLibs).then(function(when) {
        var deferreds = [];

        if (!jQuery().ajaxForm){
          deferreds.push(curl([ms2form.config.vendorUrl + 'jquery-form/jquery.form.js' ]));
        }

        if (!jQuery().jGrowl){
          deferreds.push(curl(['js!' + ms2form.config.vendorUrl + 'jgrowl/jquery.jgrowl.min.js']));
        }

        if (!jQuery().sisyphus){
          deferreds.push(curl([ 'js!' + ms2form.config.vendorUrl + 'sisyphus/sisyphus.js' ]));
        }

        if (typeof marked !== 'function') {
          deferreds.push(curl([
            ms2form.config.vendorUrl + 'marked/marked.min.js'
          ]).then(function (marked) {
            window.marked = marked
          }));
        }

        if(!jQuery().markdown){
          deferreds.push(curl([
            'js!' + ms2form.config.vendorUrl + 'he/he.js'
            , 'js!' + ms2form.config.vendorUrl + 'to-markdown/src/to-markdown.js'
          ]).next(['js!' + ms2form.config.vendorUrl + 'bootstrap-markdown/js/bootstrap-markdown.js'])
            .next(['js!' + ms2form.config.vendorUrl + 'bootstrap-markdown/locale/bootstrap-markdown.' + ms2form.config.locale + '.js']));
        }


        if (!jQuery().select2){
          deferreds.push(curl(
            [ 'js!' + ms2form.config.vendorUrl + 'select2/select2.min.js' ]
          ).next([
              'js!' + ms2form.config.vendorUrl + 'select2/select2_locale_' + ms2form.config.locale + '.js'
            ]));
        }

        if(typeof plupload == "undefined"){
          deferreds.push(curl([ 'js!' + ms2form.config.vendorUrl + 'plupload/js/plupload.full.min.js' ]).next([ 'js!' + ms2form.config.vendorUrl + 'plupload/js/i18n/'+ ms2form.config.locale+'.js']));
        }

        when.all(deferreds).then(callback)
      })
    },
    product : {
      editor: null,
      content: null,
      save : function(form, button) {
        // save content
        ms2form.product.content.val(ms2form.product.editor.parseContent());

        var parent =  $('input[name="parent"]', form).val();
        var parents =  $.map($("#ms2formCategories").select2("data"), function(val){
          return val.id
        });

        if(parent == '0'){
          if(parents[0]){
            parent = parents[0];
            parents.splice(0, 1);
          }else{
            ms2form.Message.error('parent is empty');
            return false;
          }
        } else {
          if (parents.indexOf(parent) > -1) {
            parents.splice(parents.indexOf(parent), 1);
          }
        }

        $(form).ajaxSubmit({
          data : {
            action : 'product/save',
            parent:  parent,
            parents:  parents,
            tags : $.map($("#ms2formTags").select2("data"), function(val){
              return val.text
            }),
            files: $(form).find('.ticket-file').map(function(){
              return $(this).attr('data-id')
            }).get()
          },
          url : ms2form.config.actionUrl,
          form : form,
          button : button,
          dataType : 'json',
          beforeSubmit : function(formData, jqForm, options) {
            $(button).attr('disabled', 'disabled');
            $('.error', form).text('');
            return true;
          },
          success : function(response) {
            $('#ms2form.create').sisyphus().manuallyReleaseData();
            if (response.success) {
              if (response.message) {
                ms2form.Message.success(response.message);
                $(button).removeAttr('disabled');
              }else if (response.data.redirect) {
                document.location.href = response.data.redirect;
              }
            } else {
              // form error report
              $(button).removeAttr('disabled');
              ms2form.Message.error(response.message);
              if (response.data) {
                var i, message;
                for (i in response.data) {
                  message = response.data[i];
                  $(form).find('[name="' + i + '"]').closest('.form-group').addClass('has-error');
                }
              }
            }
          }
        });
      }
    }
  };

  ms2form.Message = {
    success: function(message) {
      if (message) {
        $.jGrowl(message, {theme: 'tickets-message-success'});
      }
    }
    ,error: function(message) {
      if (message) {
        $.jGrowl(message, {theme: 'tickets-message-error'/*, sticky: true*/});
      }
    }
    ,info: function(message) {
      if (message) {
        $.jGrowl(message, {theme: 'tickets-message-info'});
      }
    }
    ,close: function() {
      $.jGrowl('close');
    }
  };

  ms2form.initialize(function() {

    var form = $('#comment-form');
    if (!form.length) form = $('#ms2form');
    var pid = form.find('[name="pid"]').val();
    var form_key = form.find('[name="form_key"]').val();

    //  bootstrap-markdown init
    if (ms2form.config.enable_editor == true) {
      ms2form.product.content =  $('#content');
      $("#ms2form-editor").append(ms2form.product.content.val());
      $("#ms2form-editor").markdown({
        resize: true
        ,language: ms2form.config.locale
      });
      ms2form.product.editor = $('#form-group-content textarea').data('markdown');
    }

    $(document).on('click', '#question', function(e) {
      e.preventDefault();
      $('.popover-help').popover('toggle');
      return false;
    });
    $(document).on('click', '.popover', function (e) {
      $(this).prev('.popover-help').popover('toggle');
    });
    $(document).on('click', '.popover a', function (e) {
      e.stopPropagation();
    });

    // init list catecories product
    var categories;
    $.post(ms2form.config.actionUrl, {action: 'product/getlist_category', pid: pid, form_key: form_key}, function(response,  textStatus, jqXHR) {
      if (response.success) {
        categories = response.data.all;
        $('#ms2formCategories').select2({
          multiple : true,
          placeholder : 'Категории',
          tags:  categories
        });
        if(response.data.product){
          $('#ms2formCategories').select2('val',response.data.product);
        }
      }
      else {
        ms2form.Message.error(response.message);
      }
    }, 'json');

    // init list tags products
    var tags;
    $.post(ms2form.config.actionUrl, {action: 'product/getlist_tag', pid: pid}, function(response,  textStatus, jqXHR) {
      if (response.success) {
        tags = response.data.all;
        $('#ms2formTags').select2({
          multiple : true,
          placeholder : 'Теги',
          tags:  tags
        });
        if(response.data.product){
          $('#ms2formTags').select2('val',response.data.product);
        }

      }
      else {
        ms2form.Message.error(response.message);
      }
    }, 'json');

    // Uploader
    ms2form.Uploader = new plupload.Uploader({
      runtimes : 'html5,flash,silverlight,html4',
      browse_button : 'ticket-files-select',
      //upload_button: document.getElementById('ticket-files-upload'),
      container : 'ticket-files-container',
      filelist : 'ticket-files-list',
      progress : 'ticket-files-progress',
      progress_bar : 'ticket-files-progress-bar',
      progress_count : 'ticket-files-progress-count',
      progress_percent : 'ticket-files-progress-percent',
      form : form,

      multipart_params : {
        action : $('#' + this.container).data('action') || 'gallery/upload',
        pid : pid,
        form_key: form_key
      },
      drop_element : 'ticket-files-list',
      url : ms2form.config.actionUrl,
      filters : {
        max_file_size : 200000000,
        mime_types : [ {
          title : 'Files',
          extensions : 'jpg,raw,webp,gif'
        } ]
      },
      resize : {
        width : 2048,
        height : 2048,
        quality : 100
      },
      flash_swf_url : ms2form.config.vendorUrl + 'lib/plupload/js/Moxie.swf',
      silverlight_xap_url : ms2form.config.vendorUrl + 'lib/plupload/js/Moxie.xap',
      init : {
        Init : function(up) {
          if (this.runtime == 'html5') {
            var element = $(this.settings.drop_element);
            element.addClass('droppable');
            element.on('dragover', function() {
              if (!element.hasClass('dragover')) {
                element.addClass('dragover');
              }
            });
            element.on('dragleave drop', function() {
              element.removeClass('dragover');
            });
          }
        },
        PostInit : function(up) {
        },
        FilesAdded : function(up, files) {
          this.settings.form.find('[type="submit"]').attr('disabled', true);
          up.start();
        },
        UploadProgress : function(up, file) {
          $(up.settings.browse_button).hide();
          $('#' + up.settings.progress).show();
          $('#' + up.settings.progress_count).text((up.total.uploaded + 1) + ' / ' + up.files.length);
          $('#' + up.settings.progress_percent).text(up.total.percent + '%');
          $('#' + up.settings.progress_bar).css('width', up.total.percent + '%');
        },
        FileUploaded : function(up, file, response) {
          response = $.parseJSON(response.response);
          if (response.success) {
            $('#' + up.settings.filelist + ' .note').hide();
            // Successfull action
            var files = $('#' + up.settings.filelist);
            var clearfix = files.find('.clearfix');
            if (clearfix.length != 0) {
              $(response.data.html).insertBefore(clearfix);
            } else {
              files.append(response.data.html);
            }
          } else {
            ms2form.Message.error(response.message);
          }
        },
        UploadComplete : function(up, file, response) {
          $(up.settings.browse_button).show();
          $('#' + up.settings.progress).hide();
          up.total.reset();
          up.splice();
          this.settings.form.find('[type="submit"]').attr('disabled', false);
        },
        Error : function(up, err) {
          ms2form.Message.error(err.message);
        }
      }
    });
    ms2form.Uploader.init();

    // init form save sisyphus
    $("#ms2form.create").sisyphus({
      excludeFields: $('#ms2form .disable-sisyphus')
    });

    // Forms listeners
    $(document).on('click', '.ms2-file-delete', function(e) {
      e.preventDefault();
      var $this = $(this);
      var $form = $this.parents('form');
      var $parent = $this.parents('.ticket-file');
      var id = $parent.data('id');
      var form_key = $form.find('[name="form_key"]').val();

      $.post(ms2form.config.actionUrl, {action: 'gallery/delete', id: id, form_key: form_key}, function(response,  textStatus, jqXHR) {
        if (response.success) {
            $('.ticket-file[data-id="'+response.data.id+'"]').remove();
        }
        else {
          ms2form.Message.error(response.message);
        }
      }, 'json');
      return false;
    });
    $(document).on('click', '.ms2-file-insert', function(e) {
      e.preventDefault();
      var $text = $('#form-group-content .md-input');
      var srcImage = $(this).parents('.ticket-file').find('.ticket-file-link').attr('href');
      var template = '![]('+srcImage+')';
      $text.focus();
      ms2form.product.editor.replaceSelection(template);
      return false;
    });
    $(document).on('click', '.btn.preview', function(e){
      e.preventDefault();
    })
    $(document).on('submit', '#ms2form', function(e) {
      e.preventDefault();
      ms2form.product.save(this, $(this).find('[type="submit"]')[0]);
      return false;
    });
    $('#btn-send').removeAttr('disabled');
  });

})();
