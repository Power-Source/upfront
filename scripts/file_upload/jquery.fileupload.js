define([], function () {
	'use strict';

	var $ = jQuery,
		DATA_KEY = 'upfront-fileupload';

	function NativeFileUpload(element, options) {
		this.element = element;
		this.$element = $(element);
		this.options = $.extend({
			autoUpload: true,
			dataType: 'json',
			paramName: null,
			formData: null,
			fileInput: undefined
		}, options);
		this.active = 0;
		this.bind();
	}

	NativeFileUpload.prototype.getInputs = function () {
		if (this.options.fileInput === null) return $();
		if (this.options.fileInput !== undefined) return $(this.options.fileInput);
		if (this.$element.is('input[type=file]')) return this.$element;
		return this.$element.find('input[type=file]');
	};

	NativeFileUpload.prototype.bind = function () {
		var me = this;
		this.getInputs().off('.upfrontFileUpload').on('change.upfrontFileUpload', function (event) {
			me.add({files: Array.prototype.slice.call(event.target.files || []), fileInput: $(event.target)}, event);
		});
	};

	NativeFileUpload.prototype.trigger = function (name, event, data) {
		var callback = this.options[name],
			jqueryEvent = event && event.isDefaultPrevented ? event : $.Event(event || name);
		if ($.isFunction(callback)) callback.call(this.element, jqueryEvent, data);
		this.$element.triggerHandler('fileupload' + name, [data]);
		return !jqueryEvent.isDefaultPrevented();
	};

	NativeFileUpload.prototype.createData = function (files, source) {
		var me = this,
			data = $.extend({}, source, {
				files: files,
				autoUpload: this.options.autoUpload
			});
		data.process = function () {
			return $.Deferred().resolve(data).promise();
		};
		data.submit = function () {
			return me.send(data);
		};
		data.abort = function () {
			if (data.jqXHR && data.jqXHR.abort) data.jqXHR.abort();
		};
		return data;
	};

	NativeFileUpload.prototype.add = function (source, event) {
		var me = this,
			files = source.files || (source.fileInput && source.fileInput[0] ? source.fileInput[0].files : []);
		files = Array.prototype.slice.call(files || []);
		$.each(files, function (index, file) {
			var data = me.createData([file], source);
			if ($.isFunction(me.options.add)) me.options.add.call(me.element, event || $.Event('fileuploadadd'), data);
			else if (me.options.autoUpload) data.submit();
		});
	};

	NativeFileUpload.prototype.getFormData = function () {
		var formData = $.isFunction(this.options.formData) ? this.options.formData.call(this.element) : this.options.formData;
		if (formData) return formData;
		if (this.$element.is('form')) return this.$element.serializeArray();
		return [];
	};

	NativeFileUpload.prototype.appendFormData = function (payload, values) {
		if (Array.isArray(values)) {
			$.each(values, function (index, field) { if (field && field.name) payload.append(field.name, field.value); });
		} else {
			$.each(values || {}, function (name, value) { payload.append(name, value); });
		}
	};

	NativeFileUpload.prototype.getUrl = function () {
		return this.options.url || this.$element.data('url') || this.$element.attr('action') || window.location.href;
	};

	NativeFileUpload.prototype.send = function (source) {
		var me = this,
			data = source.submit ? source : this.createData(source.files || [], source),
			payload = new FormData(),
			xhr = new XMLHttpRequest(),
			deferred = $.Deferred(),
			paramName = this.options.paramName || this.getInputs().attr('name') || 'files[]';

		this.appendFormData(payload, this.getFormData());
		$.each(data.files || [], function (index, file) { payload.append(paramName, file, file.name); });
		xhr.open(this.options.type || 'POST', this.getUrl(), true);
		data.jqXHR = xhr;

		xhr.upload.addEventListener('progress', function (event) {
			if (!event.lengthComputable) return;
			data.loaded = event.loaded;
			data.total = event.total;
			me.trigger('progress', event, data);
			me.trigger('progressall', event, data);
		});
		xhr.addEventListener('load', function (event) {
			var result = xhr.responseText;
			if (me.options.dataType === 'json') {
				try { result = JSON.parse(result); }
				catch (error) { me.fail(event, data, deferred, error); me.complete(event, data); return; }
			}
			data.result = result;
			if (xhr.status >= 200 && xhr.status < 300) {
				me.trigger('done', event, data);
				deferred.resolve(result, 'success', xhr);
			} else {
				me.fail(event, data, deferred);
			}
			me.complete(event, data);
		});
		xhr.addEventListener('error', function (event) { me.fail(event, data, deferred); me.complete(event, data); });
		xhr.addEventListener('abort', function (event) { me.fail(event, data, deferred); me.complete(event, data); });

		this.active++;
		if (this.active === 1) this.trigger('start', $.Event('fileuploadstart'), data);
		if (this.trigger('send', $.Event('fileuploadsend'), data)) xhr.send(payload);
		else deferred.reject(xhr, 'abort');
		return deferred.promise(xhr);
	};

	NativeFileUpload.prototype.fail = function (event, data, deferred, error) {
		if (data.jqXHR && this.options.dataType === 'json') {
			try { data.jqXHR.responseJSON = JSON.parse(data.jqXHR.responseText); }
			catch (ignore) {}
		}
		data.errorThrown = error || data.jqXHR.statusText;
		this.trigger('fail', event, data);
		deferred.reject(data.jqXHR, 'error', data.errorThrown);
	};

	NativeFileUpload.prototype.complete = function (event, data) {
		this.trigger('always', event, data);
		this.active = Math.max(0, this.active - 1);
		if (this.active === 0) this.trigger('stop', $.Event('fileuploadstop'), data);
	};

	NativeFileUpload.prototype.option = function (name, value) {
		if (value === undefined) return this.options[name];
		this.options[name] = value;
		if (name === 'fileInput') this.bind();
	};

	NativeFileUpload.prototype.destroy = function () {
		this.getInputs().off('.upfrontFileUpload');
		this.$element.removeData(DATA_KEY);
	};

	$.fn.fileupload = function (command, arg, value) {
		var first = this.first().data(DATA_KEY);
		if (command === 'option' && value === undefined) return first ? first.option(arg) : undefined;
		if (command === 'send') return first ? first.send(arg || {}) : $.Deferred().reject().promise();
		if (command === 'add') return this.each(function () { var instance = $(this).data(DATA_KEY); if (instance) instance.add(arg || {}); });

		if (typeof command === 'string') {
			return this.each(function () {
				var instance = $(this).data(DATA_KEY);
				if (!instance) return;
				if (command === 'destroy') instance.destroy();
				else if (command === 'option') instance.option(arg, value);
			});
		}

		return this.each(function () {
			var $element = $(this), instance = $element.data(DATA_KEY);
			if (instance) instance.destroy();
			instance = new NativeFileUpload(this, command || {});
			$element.data(DATA_KEY, instance);
		});
	};

	return NativeFileUpload;
});