(function() {
    var ms2form = {
        config : {
            actionUrl : Ms2formConfig.actionUrl
            ,assetsUrl : Ms2formConfig.assetsUrl
            ,vendorUrl : Ms2formConfig.vendorUrl
            ,locale: Ms2formConfig.cultureKey
            ,editor: Ms2formConfig.editor
        }
        ,selectors: {
            form: '#ms2form'
            , formKey: '#ms2formFormKey'
            , mse2form: '#ms2formParentMse2form'
            , content: '#ms2form #content'
            , editor: '#ms2formEditor'
            , editorId: 'ms2formEditor'
            , editorContainer: '#formGroupContent'
            , editorToolbar: '#ms2formEditorToolbar'
            , tags: '#ms2formTags'
            , categories: '#ms2formSections'
            , tagsNew: '#ms2formTagsNew'
            , file: '.ticket-file'
            , fileLink: '.ticket-file-link'
            , fileInsert: '.ms2-file-insert'
            , fileDelete: '.ms2-file-delete'
            , sisyphus: '#ms2form.create'
            , sisyphusDisable: '#ms2form .disable-sisyphus, #ms2form :hidden'
            , uploader: {
                browse_button: 'ticket-files-select'
                //, upload_button: document.getElementById('ticket-files-upload')
                , container: 'ticket-files-container'
                , filelist: 'ticket-files-list'
                , progress: 'ticket-files-progress'
                , progress_bar: 'ticket-files-progress-bar'
                , progress_count: 'ticket-files-progress-count'
                , progress_percent: 'ticket-files-progress-percent'
                , drop_element: 'ticket-files-list'
            }
        }
        ,_loadConfig: function (actionUrl, callback){
            var request = new XMLHttpRequest();
            actionUrl = actionUrl + '?action=config/get&form_key='
                + $(ms2form.selectors.formKey).val();
            request.open('GET', actionUrl, true);

            request.onload = function () {
                if (request.status >= 200 && request.status < 400) {
                    // Success!
                    var data = JSON.parse(request.responseText);
                    $.extend(ms2form.config, data);
                    callback();
                } else {
                    console.error('We reached our target server, but it returned an error')
                }
            };
            request.onerror = function () {
                console.error('There was a connection error of some sort')
            };
            request.send();
        }
        ,_loadScripts: function(callback) {
            var firstLibs;
            if (typeof jQuery == "undefined") {
                firstLibs = [
                    ms2form.config.vendorUrl + 'when/when'
                    , 'js!' + ms2form.config.vendorUrl + 'jquery/jquery.min.js'
                ];

            } else {
                firstLibs = [
                    ms2form.config.vendorUrl + 'when/when'
                ]
            }
            curl(firstLibs).then(function(when) {
                var deferreds = [];

                if (!jQuery().autocomplete){
                    deferreds.push(curl(['js!' + ms2form.config.assetsUrl + '/js/web/msearch2/lib/jquery-ui-1.10.4.custom.min.js']));
                }

                if (!jQuery().sortable){
                    deferreds.push(curl(['js!' + ms2form.config.vendorUrl + 'jquery-ui-sortable/jquery-ui-1.10.4.sortable.min.js']));
                }

                if (!jQuery().ajaxForm){
                    deferreds.push(curl(['js!' + ms2form.config.vendorUrl + 'jquery-form/jquery.form.js' ]));
                }

                if (!jQuery().jGrowl){
                    deferreds.push(curl(['js!' + ms2form.config.vendorUrl + 'jgrowl/jquery.jgrowl.min.js']));
                }

                if (!jQuery().sisyphus){
                    deferreds.push(curl([ 'js!' + ms2form.config.vendorUrl + 'sisyphus/sisyphus.js' ]));
                }

                //editor load
                if(ms2form.config.editor == 'bootstrapMarkdown'){
                    if (typeof marked !== 'function') {
                        deferreds.push(curl([
                            ms2form.config.vendorUrl + 'marked/marked.min.js'
                        ]).then(function (marked) {
                            window.marked = marked
                        }));
                    }
                    if (!jQuery().markdown) {
                        deferreds.push(curl([
                            'js!' + ms2form.config.vendorUrl + 'he/he.js'
                            , 'js!' + ms2form.config.vendorUrl + 'to-markdown/src/to-markdown.js'
                        ]).next(['js!' + ms2form.config.vendorUrl + 'bootstrap-markdown/js/bootstrap-markdown.js'])
                            .next(['js!' + ms2form.config.vendorUrl + 'bootstrap-markdown/locale/bootstrap-markdown.' + ms2form.config.locale + '.js']));
                    }
                }
                else if(ms2form.config.editor == 'quill'){
                    if (typeof Quill == "undefined"){
                        deferreds.push(curl([ms2form.config.vendorUrl + 'quill/dist/quill.js'])
                            .then(function(quill){
                                ms2form.editor.constructor = quill;
                            }));
                    }
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
        }
        ,load: function(callback){
            ms2form._loadScripts(function(){
                ms2form._loadConfig(ms2form.config.actionUrl, function(){
                    callback()
                })
            })
        }
        , editor: {
            initialize: function (name) {
                ms2form.product.$content = $(ms2form.selectors.content);
                jQuery.extend(this, this._editors[name]);
                this.init();
            }
            ,insertFile: function (element) {}
            ,getContent: function () {}
            ,constructor: null
            ,_editors: {
                bootstrapMarkdown: {
                    init: function () {
                        $(ms2form.selectors.editorContainer).append(
                            '<div id="'+ms2form.selectors.editorId+'"></div>'
                        );
                        $(ms2form.selectors.editor).append(ms2form.product.$content.val());
                        $(ms2form.selectors.editor).markdown({
                            resize: true
                            , language: ms2form.config.locale
                        });
                        ms2form.editor._inst = $('#formGroupContent textarea').data('markdown');
                    }
                    , insertFile: function (element) {
                        var $text = $('#formGroupContent .md-input');
                        var srcImage = $(element).parents(ms2form.selectors.file).find(ms2form.selectors.fileLink).attr('href');
                        var template = '![](' + srcImage + ')';
                        $text.focus();
                        ms2form.editor._inst.replaceSelection(template);
                    }
                    , getContent: function () {
                        return this._inst.parseContent()
                    }
                }
                ,quill: {
                    init: function () {
                        this._inst = new this.constructor(ms2form.selectors.editor, {
                            modules: {
                                'toolbar': {container: ms2form.selectors.editorToolbar}
                                , 'image-tooltip': true
                                , 'link-tooltip': true
                            },
                            theme: 'snow'
                        });
                    }
                    , insertFile: function (element) {
                        var srcImage = $(element).parents(ms2form.selectors.file).find(ms2form.selectors.fileLink).attr('href');
                        this._inst.focus();
                        var index = this._inst.getSelection().end;
                        this._inst.insertEmbed(index, 'image', srcImage);
                    }
                    , getContent: function () {
                        return this._inst.getHTML()

                    }
                }
            }
            , _inst: null
        }
        ,initialize: function(){
            var form = $(ms2form.selectors.form);
            var pid = form.find('[name="pid"]').val();
            var form_key = ms2form.config.formKey;

            //  content editor init
            if (ms2form.config.editor !== '0') {
                ms2form.editor.initialize(ms2form.config.editor);
            }

            $(document).on('click', '#question', function (e) {
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
            $.post(ms2form.config.actionUrl, {
                action: 'product/getlist_category',
                pid: pid,
                form_key: form_key
            }, function (response, textStatus, jqXHR) {
                if (response.success) {
                    categories = response.data.all;
                    $(ms2form.selectors.categories).select2({
                        multiple: true,
                        placeholder: 'Категории',
                        tags: categories
                    });
                    if (response.data.product) {
                        $(ms2form.selectors.categories).select2('val', response.data.product);
                    }
                }
                else {
                    ms2form.message.error(response.message);
                }
            }, 'json');

            // init list tags products
            var tags;
            $.post(ms2form.config.actionUrl, {
                action: 'product/getlist_tag',
                pid: pid,
                form_key: form_key,
        allowedTags: ( typeof Ms2formConfig.allowedTags == 'string' ) ? Ms2formConfig.allowedTags : ''
            }, function (response, textStatus, jqXHR) {
                if (response.success) {
                    tags = response.data.all;
                    var select2TagsConfig = {
                        multiple: true,
                        placeholder: 'Теги'
                    };
                    // check allow add new tags
                    if (form.find(ms2form.selectors.tagsNew).val() === '1') {
                        select2TagsConfig.tags = tags
                    } else {
                        select2TagsConfig.data = tags
                    }
                    $(ms2form.selectors.tags).select2(select2TagsConfig);
                    if (response.data.product) {
                        $(ms2form.selectors.tags).select2('val', response.data.product);
                    }

                }
                else {
                    ms2form.message.error(response.message);
                }
            }, 'json');

            // Uploader
            ms2form.Uploader = new plupload.Uploader({
                runtimes: 'html5,flash,silverlight,html4',
                browse_button: ms2form.selectors.uploader.browse_button,
                //upload_button: document.getElementById('ticket-files-upload'),
                container: ms2form.selectors.uploader.container,
                filelist: ms2form.selectors.uploader.filelist,
                progress: ms2form.selectors.uploader.progress,
                progress_bar: ms2form.selectors.uploader.progress_bar,
                progress_count: ms2form.selectors.uploader.progress_count,
                progress_percent: ms2form.selectors.uploader.progress_percent,
                drop_element: ms2form.selectors.uploader.drop_element,
                form: form,
                multipart_params: {
                    action: $('#' + this.container).data('action') || 'gallery/upload',
                    pid: pid,
                    form_key: form_key
                },
                url: ms2form.config.actionUrl,
                filters: {
                    max_file_size: ms2form.config.sourceProperties.maxUploadSize.value,
                    mime_types: [{
                        title: 'Files',
                        extensions: ms2form.config.sourceProperties.allowedFileTypes.value
                    }]
                },
                resize: {
                    width: ms2form.config.sourceProperties.maxUploadWidth.value,
                    height: ms2form.config.sourceProperties.maxUploadHeight.value,
                    quality: 100
                },
                flash_swf_url: ms2form.config.vendorUrl + 'lib/plupload/js/Moxie.swf',
                silverlight_xap_url: ms2form.config.vendorUrl + 'lib/plupload/js/Moxie.xap',
                init: {
                    Init: function (up) {
                        if (this.runtime == 'html5') {
                            var element = $(this.settings.drop_element);
                            element.addClass('droppable');
                            element.on('dragover', function () {
                                if (!element.hasClass('dragover')) {
                                    element.addClass('dragover');
                                }
                            });
                            element.on('dragleave drop', function () {
                                element.removeClass('dragover');
                            });
                        }
                    },
                    PostInit: function (up) {
                    },
                    FilesAdded: function (up, files) {
                        this.settings.form.find('[type="submit"]').attr('disabled', true);
                        up.start();
                    },
                    UploadProgress: function (up, file) {
                        $(up.settings.browse_button).hide();
                        $('#' + up.settings.progress).show();
                        $('#' + up.settings.progress_count).text((up.total.uploaded + 1) + ' / ' + up.files.length);
                        $('#' + up.settings.progress_percent).text(up.total.percent + '%');
                        $('#' + up.settings.progress_bar).css('width', up.total.percent + '%');
                    },
                    FileUploaded: function (up, file, response) {
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
                            ms2form.message.error(response.message);
                        }
                    },
                    UploadComplete: function (up, file, response) {
                        $(up.settings.browse_button).show();
                        $('#' + up.settings.progress).hide();
                        up.total.reset();
                        up.splice();
                        this.settings.form.find('[type="submit"]').attr('disabled', false);
                    },
                    Error: function (up, err) {
                        ms2form.message.error(err.message);
                    }
                }
            });
            ms2form.Uploader.init();

            // init form save sisyphus
            $(ms2form.selectors.sisyphus).sisyphus({
                excludeFields: $(ms2form.selectors.sisyphusDisable)
            });

            //Sort files
            $('#' + ms2form.selectors.uploader.filelist).sortable({
                    items: ms2form.selectors.file,
                    update: function( event, ui ) {
                            var rank = {};
                            $('#' + ms2form.selectors.uploader.filelist).find(ms2form.selectors.file).each(function(i){
                                    rank[i] = $(this).data('id');
                            });

                            var data = {
                                    action: 'gallery/sort'
                                    , rank: rank
                                    , form_key: ms2form.config.formKey
                            };

                            $.post(ms2form.config.actionUrl, data, function(response) {
                                    if (!response.success) {
                                            ms2form.message.error(response.message);
                                    }
                            }, 'json');
                    }
            });

            // Forms listeners
            $(document).on('click', ms2form.selectors.fileDelete, function (e) {
                e.preventDefault();
                var $this = $(this);
                var $form = $this.parents('form');
                var $parent = $this.parents(ms2form.selectors.file);
                var id = $parent.data('id');

                $.post(ms2form.config.actionUrl, {
                    action: 'gallery/delete',
                    id: id,
                    form_key: form_key
                }, function (response, textStatus, jqXHR) {
                    if (response.success) {
                        $(ms2form.selectors.file + '[data-id="' + response.data.id + '"]').remove();
                    }
                    else {
                        ms2form.message.error(response.message);
                    }
                }, 'json');
                return false;
            });
            $(document).on('click', ms2form.selectors.fileInsert, function (e) {
                e.preventDefault();
                ms2form.editor.insertFile(this);
                return false;
            });
            $(document).on('click', '.btn.preview', function (e) {
                e.preventDefault();
            });
            $(document).on('submit', ms2form.selectors.form , function (e) {
                e.preventDefault();
                ms2form.product.save(this, $(this).find('[type="submit"]')[0]);
                return false;
            });
            $('#btn-send').removeAttr('disabled');
        }
        ,form: null
        ,button: null
        ,product: {
            content: null,
            $content: null,
            parent: null,
            parents: null,
            save: function (form, button) {
                ms2form.form = form;
                ms2form.button = button;
                // set content
                this._setContent();

                // set parent
                if (!this._setParents()) return;

                // mse2form processing
                var $mse2form = $(ms2form.selectors.mse2form);
                if($mse2form.length){
                    var categoryId = $mse2form.data('id');
                    if(categoryId){
                        this.parent = categoryId;
                        this._ajaxSubmit()
                    }else{
                        var categoryTitle = $mse2form.val();
                        var data = {
                            action: 'category/create'
                            , form_key: ms2form.config.formKey
                            , parent: ms2form.config.parent
                            , pagetitle: categoryTitle
                        };
                        data[ms2form.config.parentMse2form.queryVar] = categoryTitle;
                        $.post(ms2form.config.actionUrl, data)
                            .done(function (response) {
                                response = JSON.parse(response);
                                if (response.success) {
                                    ms2form.product.parent = response.data.id;
                                    ms2form.product._ajaxSubmit()
                                }
                            })
                            .fail(ms2form.product._error);
                    }
                }else{
                    this._ajaxSubmit()
                }
            }
            ,_setContent: function() {
                var content = ms2form.editor.getContent();
                this.$content.val(content);
                this.content = content;
            }
            ,_setParents: function (){
                var parent = $('input[name="parent"]',ms2form.form).val();
                var parents = $.map($(ms2form.selectors.categories).select2("data"), function (val) {
                    return val.id
                });
                if (parent == '0') {
                    if (parents[0]) {
                        parent = parents[0];
                        parents.splice(0, 1);
                    } else {
                        ms2form.message.error('parent is empty');
                        return false;
                    }
                } else {
                    if (parents.indexOf(parent) > -1) {
                        parents.splice(parents.indexOf(parent), 1);
                    }
                }
                this.parent = parent;
                this.parents = parents;
                return true
            }
            ,_ajaxSubmit: function (){
                $(ms2form.form).ajaxSubmit({
                    data: {
                        action: 'product/save',
                        content: ms2form.product.content,
                        parent: ms2form.product.parent,
                        parents: ms2form.product.parents,
                        tags: $.map($(ms2form.selectors.tags).select2("data"), function (val) {
                            return val.text
                        }),
                        files: $(ms2form.form).find(ms2form.selectors.file).map(function () {
                            return $(this).attr('data-id')
                        }).get()
                    },
                    url: ms2form.config.actionUrl,
                    form: ms2form.form,
                    button: ms2form.button,
                    dataType: 'json',
                    beforeSubmit: function (formData, jqForm, options) {
                        $(ms2form.button).attr('disabled', 'disabled');
                        $('.error', ms2form.form).text('');
                        return true;
                    },
                    success: ms2form.product._success,
                    error: ms2form.product._error
                });
            }
            ,_success: function (response){
                $(ms2form.selectors.sisyphus).sisyphus().manuallyReleaseData();
                if (response.success) {
                    if (response.message) {
                        ms2form.message.success(response.message);
                    } else if (response.data.redirect) {
                        document.location.href = response.data.redirect;
                    }
                    $(ms2form.form).resetForm();
                    $(ms2form.button).removeAttr('disabled');
                } else {
                    ms2form.product._error(response)
                }
            }
            ,_error: function (response){
                if(!response.message){
                    response = JSON.parse(response.responseText);
                }
                var message = response.message;
                console.error(response.data);
                if (response.data) {
                    var i;
                    for (i in response.data) {
                        $(ms2form.form).find('[name="' + i + '"]').closest('.form-group').addClass('has-error');
                    }
                    message = response.message + '<br>' + JSON.stringify(response.data)
                }
                ms2form.message.error(message);
                // form error report
                $(ms2form.button).removeAttr('disabled');
            }
        }
        ,message: {
            success: function (message) {
                if (message) {
                    $.jGrowl(message, {theme: 'tickets-message-success'});
                }
            }
            ,error: function (message) {
                if (message) {
                    $.jGrowl(message, {
                        theme: 'tickets-message-error'
                        //, sticky: true
                    });
                }
            }
            ,info: function (message) {
                if (message) {
                    $.jGrowl(message, {theme: 'tickets-message-info'});
                }
            }
            ,close: function () {
                $.jGrowl('close');
            }
        }
    };

    var mse2form = {
        initialize: function (selector) {
            var $this = $(selector);
            if (!$this.length) return;
            var config = ms2form.config.parentMse2form;
            var cache = {};

            $this.autocomplete({
                source: function (request, callback) {
                    if (request.term in cache) {
                        callback(cache[request.term]);
                        return;
                    }
                    var data = {
                        action: 'search'
                        , key: $this.data('key')
                        , pageId: config.pageId
                    };
                    data[config.queryVar] = request.term;
                    $.post(config.actionUrl, data, function (response) {
                        if (response.data.log) {
                            ms2form.message.info(response.data.log)
                        }
                        cache[request.term] = response.data.results;
                        callback(response.data.results)
                    }, 'json');
                }
                , minLength: config.minQuery || 3
                , select: function (event, ui) {
                    if (ui.item.id) {
                        $this.data('id', ui.item.id); // save msCategory Id
                        $this.data('title', ui.item.value);
                        console.log('save msCategory Id');
                    }
                }
                , change: function (event, ui){
                    if($this.val() != $this.data('title')){
                        console.log('remove msCategory Id');
                        $this.removeData('id'); // remove msCategory Id
                    }
                }
            })
                .data("ui-autocomplete")._renderItem = function (ul, item) {
                return $("<li></li>")
                    .data("item.autocomplete", item)
                    .addClass("mse2-ac-wrapper")
                    .append("<a class=\"mse2-ac-link\">" + item.label + "</a>")
                    .appendTo(ul);
            };
            // event listeners
            $(document).on('keypress', ms2form.selectors.mse2form, function (e) {
                if (e.which == 13) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    };

    ms2form.load(function() {
        ms2form.initialize();
        mse2form.initialize(ms2form.selectors.mse2form);
    });

    //todo-me delete this
    window.ms2form = ms2form;
})();
