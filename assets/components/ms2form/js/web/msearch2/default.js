var mSearch2 = {
	options: {
		wrapper: '#mse2_mfilter'
		,filters: '#mse2_filters'
		,results: '#mse2_results'
		,total: '#mse2_total'
		,pagination: '#mse2_pagination'
		,sort: '#mse2_sort'
		,limit: '#mse2_limit'
		,slider: '.mse2_number_slider'
		,selected: '#mse2_selected'

		,pagination_link: '#mse2_pagination a'
		,sort_link: '#mse2_sort a'
		,tpl_link: '#mse2_tpl a'
		,selected_tpl: '<a href="#" data-id="[[+id]]" class="mse2_selected_link"><em>[[+title]]</em><sup>x</sup></a>'

		,active_class: 'active'
		,disabled_class: 'disabled'
		,disabled_class_fieldsets: 'disabled_fieldsets'
		,prefix: 'mse2_'
		,suggestion: 'sup' // inside filter item, e.g. #mse2_filters
	}
	,sliders: {}
	,initialize: function(selector) {
		var elements = ['filters','results','pagination','total','sort','selected','limit'];
		for (var i in elements) {
			if (elements.hasOwnProperty(i)) {
				var elem = elements[i];
				this[elem] = $(selector).find(this.options[elem]);
				if (!this[elem].length) {
					//console.log('Error: could not initialize element "' + elem + '" with selector "' + this.options[elem] + '".');
				}
			}
		}

		this.handlePagination();
		this.handleSort();
		this.handleTpl();
		this.handleSlider();
		this.handleLimit();

		$(document).on('submit', this.options.filters, function(e) {
			mse2Config.page = '';
			mSearch2.load();
			return false;
		});

		$(document).on('change', this.options.filters, function(e) {
			return $(this).submit();
		});

		if (this.selected) {
			var selectors = [
				this.options.filters + ' input[type="checkbox"]',
				this.options.filters + ' input[type="radio"]',
				this.options.filters + ' select'
			];
			$(document).on('change', selectors.join(', '), function(e) {
				mSearch2.handleSelected($(this));
			});

			selectors = [
				'input[type="checkbox"]:checked',
				'input[type="radio"]:checked',
				'select'
			];
			this.filters.find(selectors.join(', ')).each(function(e) {
				mSearch2.handleSelected($(this));
			});

			$(document).on('click', this.options.selected + ' a', function(e) {
				var id = $(this).data('id').replace(mse2Config.filter_delimeter, "\\" + mse2Config.filter_delimeter);
				var elem = $('#' + id);
				if (elem[0]) {
					switch (elem[0].tagName) {
						case 'INPUT':
							elem.trigger('click');
							break;
						case 'SELECT':
							elem.find('option:first').prop('selected',true).trigger('change');
							break;
					}
				}
				return false;
			});
		}
		mSearch2.setEmptyFieldsets();
		mSearch2.setTotal(this.total.text());
		return true;
	}


	,handlePagination: function() {
		$(document).on('click', this.options.pagination_link, function(e) {
			if (!$(this).hasClass(mSearch2.options.active_class)) {
				$(mSearch2.options.pagination).removeClass(mSearch2.options.active_class);
				$(this).addClass(mSearch2.options.active_class);

				var tmp = $(this).prop('href').match(/page[=|\/](\d+)/);
				var page = tmp && tmp[1] ? Number(tmp[1]) : 1;
				mse2Config.page = (page != mse2Config.start_page) ? page : '';

				mSearch2.load('', function() {
					$('html, body').animate({
						scrollTop: $(mSearch2.options.wrapper).position().top || 0
					}, 0);
				});
			}

			return false;
		});
	}

	,handleSort: function() {
		var params = this.Hash.get();
		if (params.sort) {
			var sorts = params.sort.split(mse2Config.values_delimeter);
			for (var i = 0; i < sorts.length; i++) {
				var tmp = sorts[i].split(mse2Config.method_delimeter);
				if (tmp[0] && tmp[1]) {
					$(this.options.sort_link +'[data-sort="' + tmp[0] + '"]').data('dir', tmp[1]).attr('data-dir', tmp[1]).addClass(this.options.active_class);
				}
			}
		}

		$(document).on('click', this.options.sort_link, function(e) {
			$(mSearch2.options.sort_link).removeClass(mSearch2.options.active_class);
			$(this).addClass(mSearch2.options.active_class);
			var dir;
			if ($(this).data('dir').length == 0) {
				dir = $(this).data('default');
			}
			else {
				dir = $(this).data('dir') == 'desc'
					? 'asc'
					: 'desc';
			}
			$(mSearch2.options.sort_link).data('dir', '').attr('data-dir', '');
			$(this).data('dir', dir).attr('data-dir', dir);

			var sort = $(this).data('sort');
			if (dir) {
				sort += mse2Config.method_delimeter + dir;
			}
			mse2Config.sort = (sort != mse2Config.start_sort) ? sort : '';
			mSearch2.load();

			return false;
		});
	}

	,handleTpl: function() {
		$(document).on('click', this.options.tpl_link, function(e) {
			if (!$(this).hasClass(mSearch2.options.active_class)) {
				$(mSearch2.options.tpl_link).removeClass(mSearch2.options.active_class);
				$(this).addClass(mSearch2.options.active_class);

				var tpl = $(this).data('tpl');
				mse2Config.tpl = (tpl != mse2Config.start_tpl && tpl != 0) ? tpl : '';

				mSearch2.load();
			}

			return false;
		});
	}

	,handleSlider: function() {
		if (!$(mSearch2.options.slider).length) {
			return false;
		}
		else if (!$.ui || !$.ui.slider) {
			return mSearch2.loadJQUI(mSearch2.handleSlider);
		}
		$(mSearch2.options.slider).each(function() {
			var fieldset = $(this).parents('fieldset');
			var imin = fieldset.find('input:first');
			var imax = fieldset.find('input:last');
			var vmin = Number(imin.val());
			var vmax = Number(imax.val());
			var $this = $(this);

			$this.slider({
				min: vmin
				,max: vmax
				,values: [vmin, vmax]
				,range: true
				//,step: 0.1
				,stop: function(event, ui) {
					imin.val($this.slider('values',0)/*.toFixed(2)*/);
					imax.val($this.slider('values',1)/*.toFixed(2)*/);
					imin.trigger('change');
				},
				slide: function(event, ui){
					imin.val($this.slider('values',0)/*.toFixed(2)*/);
					imax.val($this.slider('values',1)/*.toFixed(2)*/);
				}
			});

			var name = imin.prop('name');
			var values = mSearch2.Hash.get();
			if (values[name]) {
				var tmp = values[name].split(mse2Config.values_delimeter);
				if (tmp[0].match(/(?!^-)[^0-9\.]/g)) {tmp[0] = tmp[0].replace(/(?!^-)[^0-9\.]/g, '');}
				if (tmp.length > 1) {
					if (tmp[1].match(/(?!^-)[^0-9\.]/g)) {tmp[1] = tmp[1].replace(/(?!^-)[^0-9\.]/g, '');}
				}
				imin.val(tmp[0]);
				imax.val(tmp.length > 1 ? tmp[1] : tmp[0]);
			}

			//imin.attr('readonly', true);
			imin.on('change keyup input click', function(e) {
				if (this.value.match(/(?!^-)[^0-9\.]/g)) {this.value = this.value.replace(/(?!^-)[^0-9\.]/g, '');}
				if (e.type != 'keyup' && e.type != 'input') {
					if (this.value > vmax) {this.value = vmax;}
					else if (this.value < vmin) {this.value = vmin;}
				}
				$this.slider('values',0,this.value);
			});
			//imax.attr('readonly', true);
			imax.on('change keyup input click', function(e) {
				if (this.value.match(/(?!^-)[^0-9\.]/g)) {this.value = this.value.replace(/(?!^-)[^0-9\.]/g, '');}
				if (e.type != 'keyup' && e.type != 'input') {
					if (this.value > vmax) {this.value = vmax;}
					else if (this.value < vmin) {this.value = vmin;}
				}
				$this.slider('values',1,this.value);
			});

			if (values[name]) {
				imin.add(imax).trigger('click');
			}

			mSearch2.sliders[name] = [vmin,vmax];
		});
		return true;
	}

	,handleLimit: function() {
		$(document).on('change', this.options.limit, function(e) {
			var limit = $(this).val();
			mse2Config.page = '';
			if (limit == mse2Config.start_limit) {
				mse2Config.limit = '';
			}
			else {
				mse2Config.limit = limit;
			}
			mSearch2.load();
		});
	}

	,handleSelected: function(input) {
		if (!input[0]) {return;}
		var id = input.prop('id');
		var title = '';
		var elem = '';

		switch (input[0].tagName) {
			case 'INPUT':
				var label = input.parents('label');
				var match = label.html().match(/>(.*?)</);
				if (match && match[1]) {
					title = match[1].replace(/(\s+$)/, '');
				}
				if (input.is(':checked')) {
					elem = this.options.selected_tpl.replace('[[+id]]', id).replace('[[+title]]', title);
					this.selected.find('span').append(elem);
				}
				else {
					$('[data-id="' + id + '"]', this.selected).remove();
				}
				break;

			case 'SELECT':
				var option = input.find('option:selected');
				$('[data-id="' + id + '"]', this.selected).remove();
				if (input.val()) {
					title = ' ' + option.text().replace(/(\(.*\)$)/, '');
					elem = this.options.selected_tpl.replace('[[+id]]', id).replace('[[+title]]', title);
					this.selected.find('span').append(elem);
				}
				break;
		}

		if (this.selected.find('a').length) {
			this.selected.show();
		}
		else {
			this.selected.hide();
		}
	}

	,load: function(params, callback) {
		if (!params) {
			params = this.getFilters();
		}
		if (mse2Config[mse2Config.queryVar] != '') {params[mse2Config.queryVar] = mse2Config[mse2Config.queryVar];}
		if (mse2Config[mse2Config.parentsVar] != '') {params[mse2Config.parentsVar] = mse2Config[mse2Config.parentsVar];}
		if (mse2Config.sort != '') {params.sort = mse2Config.sort;}
		if (mse2Config.tpl != '') {params.tpl = mse2Config.tpl;}
		if (mse2Config.page > 0) {params.page = mse2Config.page;}
		if (mse2Config.limit > 0) {params.limit = mse2Config.limit;}

		for (var i in this.sliders) {
			if (this.sliders.hasOwnProperty(i) && params[i]) {
				if (this.sliders[i].join(mse2Config.values_delimeter) == params[i]) {
					delete params[i];
				}
			}
		}

		this.Hash.set(params);
		params.action = 'filter';
		params.pageId = mse2Config.pageId;

		this.beforeLoad();
		params.key = mse2Config.key;
		$.post(mse2Config.actionUrl, params, function(response) {
			mSearch2.afterLoad();
			if (response.success) {
				mSearch2.Message.success(response.message);
				mSearch2.results.html(response.data.results);
				mSearch2.pagination.html(response.data.pagination);
				mSearch2.setTotal(response.data.total);
				mSearch2.setSuggestions(response.data.suggestions);
				mSearch2.setEmptyFieldsets();
				if (response.data.log) {
					$('.mFilterLog').html(response.data.log);
				}
				if (callback && $.isFunction(callback)) {
					callback.call(this, response, params);
				}
				$(document).trigger('mse2_load', response);
			}
			else {
				mSearch2.Message.error(response.message);
			}
		}, 'json');
	}

	,getFilters: function() {
		var data = {};
		$.map(this.filters.serializeArray(), function(n, i) {
			if (n['value'] === '') {return;}
			if (data[n['name']]) {
				data[n['name']] += mse2Config.values_delimeter + n['value'];
			}
			else {
				data[n['name']] = n['value'];
			}
		});

		return data;
	}

	,setSuggestions: function(suggestions) {
		for (var filter in suggestions) {
			if (suggestions.hasOwnProperty(filter)) {
				var arr = suggestions[filter];
				for (var value in arr) {
					if (arr.hasOwnProperty(value)) {
						var count = arr[value];
						var selector = filter.replace(mse2Config.filter_delimeter, "\\" + mse2Config.filter_delimeter);
						var input = $('#' + mSearch2.options.prefix + selector, mSearch2.filters).find('[value="' + value + '"]');
						if (!input[0]) {continue;}

						switch (input[0].tagName) {
							case 'INPUT':
								var proptype = input.prop('type');
								if (proptype != 'checkbox' && proptype != 'radio') {continue;}
								var label = $('#' + mSearch2.options.prefix + selector, mSearch2.filters).find('label[for="' + input.prop('id') + '"]');
								var elem = input.parent().find(mSearch2.options.suggestion);
								elem.text(count);

								if (count == 0) {
									input.prop('disabled', true);
									label.addClass(mSearch2.options.disabled_class);
									if (input.is(':checked')) {
										input.prop('checked', false);
										mSearch2.handleSelected(input);
									}
								}
								else {
									input.prop('disabled', false);
									label.removeClass(mSearch2.options.disabled_class);
								}

								if (input.is(':checked')) {
									elem.hide();
								}
								else {
									elem.show();
								}
								break;

							case 'OPTION':
								var text = input.text();
								var matches = text.match(/\s\(.*\)$/);
								var src = matches
									? matches[0]
									: '';
								var dst = '';

								if (!count) {
									input.prop('disabled', true).addClass(mSearch2.options.disabled_class);
									if (input.is(':selected')) {
										input.prop('selected', false);
										mSearch2.handleSelected(input);
									}
								}
								else {
									dst = ' (' + count + ')';
									input.prop('disabled', false).removeClass(mSearch2.options.disabled_class);
								}

								if (input.is(':selected')) {
									dst = '';
								}

								if (src) {
									text = text.replace(src, dst);
								}
								else {
									text += dst;
								}
								//console.log(count,text)
								input.text(text);

								break;
						}
					}
				}
			}
		}
	}
	
	,setEmptyFieldsets: function() {
		this.filters.find('fieldset').each(function(e) {
			var all_children_disabled = $(this).find('label:not(.'+mSearch2.options.disabled_class+')').length == 0;
			if (all_children_disabled) {
				$(this).addClass(mSearch2.options.disabled_class_fieldsets);
			}
			if (!all_children_disabled && $(this).hasClass(mSearch2.options.disabled_class_fieldsets)) {
				$(this).removeClass(mSearch2.options.disabled_class_fieldsets);
			}
		});
	}

	,setTotal: function(total) {
		if (this.total.length != 0) {
			if (!total || total == 0) {
				this.total.parent().hide();
				this.limit.parent().hide();
				this.sort.hide();
				this.total.text(0);
			}
			else {
				this.total.parent().show();
				this.limit.parent().show();
				this.sort.show();
				this.total.text(total);
			}
		}
	}

	,beforeLoad: function() {
		this.results.css('opacity', .5);
		$(this.options.pagination_link).addClass(this.options.active_class);
		this.filters.find('input, select').prop('disabled', true).addClass(this.options.disabled_class);
	}

	,afterLoad: function() {
		this.results.css('opacity', 1);
		this.filters.find('.' + this.options.disabled_class).prop('disabled', false).removeClass(this.options.disabled_class);
	}

	,loadJQUI: function(callback, parameters) {
		$('<link/>', {
			rel: 'stylesheet',
			type: 'text/css',
			href: mse2Config.cssUrl + 'redmond/jquery-ui-1.10.4.custom.min.css'
		}).appendTo('head');

		return $.getScript(mse2Config.jsUrl + 'lib/jquery-ui-1.10.4.custom.min.js', function() {
			if (typeof callback == 'function') {
				callback(parameters);
			}
		});
	}

};

mSearch2.Form = {
	initialize: function(selector) {

		$(selector).each(function() {
			var form = $(this);
			var config = mse2FormConfig[form.data('key')];
			var cache = {};

			if (config.autocomplete == '0' || config.autocomplete == 'false') {
				return false;
			}
			else if (!$.ui || !$.ui.autocomplete) {
				return mSearch2.loadJQUI(mSearch2.Form.initialize, selector);
			}

			form.find('input[name="' + config.queryVar + '"]').autocomplete({
				source: function(request, callback) {
					if (request.term in cache) {
						callback(cache[request.term]);
						return;
					}
					var data = {
						action: 'search'
						,key: form.data('key')
						,pageId: config.pageId
					};
					data[config.queryVar] = request.term;
					$.post(mse2Config.actionUrl, data, function(response) {
						if (response.data.log) {
							$('.mSearchFormLog').html(response.data.log);
						}
						else {
							$('.mSearchFormLog').html('');
						}
						cache[request.term] = response.data.results;
						callback(response.data.results)
					}, 'json');
				}
				,minLength: config.minQuery || 3
				,select: function(event,ui) {
					if (ui.item.url) {
						document.location.href = ui.item.url;
					}
					else {
						setTimeout(function() {
							form.submit();
						}, 100);
					}
				}
			})
			.data("ui-autocomplete")._renderItem = function(ul, item) {
				return $("<li></li>")
					.data("item.autocomplete", item)
					.addClass("mse2-ac-wrapper")
					.append("<a class=\"mse2-ac-link\">"+ item.label + "</a>")
					.appendTo(ul);
			};
			return true;
		});
	}
};

mSearch2.Message = {
	success: function(message) {

	}
	,error: function(message) {
		alert(message);
	}
};

mSearch2.Hash = {
	get: function() {
		var vars = {}, hash, splitter, hashes;
		if (!this.oldbrowser()) {
			var pos = window.location.href.indexOf('?');
			hashes = (pos != -1) ? decodeURIComponent(window.location.href.substr(pos + 1)) : '';
			splitter = '&';
		}
		else {
			hashes = decodeURIComponent(window.location.hash.substr(1));
			splitter = '/';
		}

		if (hashes.length == 0) {return vars;}
		else {hashes = hashes.split(splitter);}

		for (var i in hashes) {
			if (hashes.hasOwnProperty(i)) {
				hash = hashes[i].split('=');
				if (typeof hash[1] == 'undefined') {
					vars['anchor'] = hash[0];
				}
				else {
					vars[hash[0]] = hash[1];
				}
			}
		}
		return vars;
	}
	,set: function(vars) {
		var hash = '';
		for (var i in vars) {
			if (vars.hasOwnProperty(i)) {
				hash += '&' + i + '=' + vars[i];
			}
		}

		if (!this.oldbrowser()) {
			if (hash.length != 0) {
				hash = '?' + hash.substr(1);
			}
			window.history.pushState(hash, '', document.location.pathname + hash);
		}
		else {
			window.location.hash = hash.substr(1);
		}
	}
	,add: function(key, val) {
		var hash = this.get();
		hash[key] = val;
		this.set(hash);
	}
	,remove: function(key) {
		var hash = this.get();
		delete hash[key];
		this.set(hash);
	}
	,clear: function() {
		this.set({});
	}
	,oldbrowser: function() {
		return !(window.history && history.pushState);
	}
};

// Initialize Filters
if ($('#mse2_mfilter').length) {
	if (window.location.hash != '' && mSearch2.Hash.oldbrowser()) {
		var uri = window.location.hash.replace('#', '?');
		window.location.href = document.location.pathname + uri;
	}
	else {
		//mSearch2.initialize('#mse2_mfilter');
		mSearch2.initialize('body');
	}
}
// Initialize Form
if ($('form.msearch2').length) {
	mSearch2.Form.initialize('form.msearch2');
}
