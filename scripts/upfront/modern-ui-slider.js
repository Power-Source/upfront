define(['nouislider'], function (noUiSlider) {
	'use strict';

	var $ = jQuery,
		DATA_KEY = 'upfront-slider';

	function Slider(element, options) {
		this.element = element;
		this.$element = $(element);
		this.options = $.extend({min: 0, max: 100, step: 1, range: 'min', orientation: 'horizontal'}, options);
		this.create();
	}

	Slider.prototype.getStart = function () {
		if (Array.isArray(this.options.values)) return this.options.values;
		return [this.options.value !== undefined ? this.options.value : this.options.min];
	};

	Slider.prototype.getConnect = function (handles) {
		if (handles > 1) return this.options.range === true ? [false, true, false] : false;
		if (this.options.range === 'max') return [false, true];
		if (this.options.range === 'min' || this.options.range === true) return [true, false];
		return false;
	};

	Slider.prototype.getUi = function (values, handle) {
		var normalized = $.map(values, function (value) { return Number(value); });
		return {
			handle: this.$element.find('.noUi-handle').eq(handle),
			value: normalized[handle || 0],
			values: normalized
		};
	};

	Slider.prototype.trigger = function (name, values, handle, tap) {
		if ($.isFunction(this.options[name])) {
			this.options[name].call(this.element, $.Event(name, {originalEvent: tap || null}), this.getUi(values, handle));
		}
	};

	Slider.prototype.applyLegacyClasses = function () {
		var orientation = this.options.orientation === 'vertical' ? 'vertical' : 'horizontal';
		this.$element.addClass('ui-slider ui-slider-' + orientation);
		this.$element.find('.noUi-handle').addClass('ui-slider-handle ui-state-default');
		this.$element.find('.noUi-connect').addClass('ui-slider-range ui-slider-range-' + (this.options.range === 'max' ? 'max' : 'min'));
	};

	Slider.prototype.create = function () {
		var me = this,
			start = this.getStart(),
			max = Number(this.options.max),
			min = Number(this.options.min);

		if (this.element.noUiSlider) this.element.noUiSlider.destroy();
		this.$element.empty().removeClass('ui-slider ui-slider-horizontal ui-slider-vertical');
		if (!isFinite(min)) min = 0;
		if (!isFinite(max) || max <= min) max = min + 1;

		noUiSlider.create(this.element, {
			start: start,
			connect: this.getConnect(start.length),
			range: {min: min, max: max},
			step: Number(this.options.step) || 1,
			orientation: this.options.orientation === 'vertical' ? 'vertical' : 'horizontal',
			direction: window.Upfront && Upfront.mainData && Upfront.mainData.isRTL ? 'rtl' : 'ltr',
			format: {
				to: function (value) { return Number(value); },
				from: function (value) { return Number(value); }
			}
		});
		this.applyLegacyClasses();
		this.element.noUiSlider.on('start', function (values, handle, unencoded, tap) { me.trigger('start', unencoded, handle, tap); });
		this.element.noUiSlider.on('slide', function (values, handle, unencoded, tap) { me.trigger('slide', unencoded, handle, tap); });
		this.element.noUiSlider.on('change', function (values, handle, unencoded, tap) {
			me.userChange = true;
			me.trigger('change', unencoded, handle, tap);
		});
		this.element.noUiSlider.on('set', function (values, handle, unencoded, tap) {
			if (!me.userChange) me.trigger('change', unencoded, handle, tap);
			me.userChange = false;
		});
		this.element.noUiSlider.on('end', function (values, handle, unencoded, tap) { me.trigger('stop', unencoded, handle, tap); });
	};

	Slider.prototype.getValue = function () {
		var value = this.element.noUiSlider.get();
		return Array.isArray(value) ? Number(value[0]) : Number(value);
	};

	Slider.prototype.setValue = function (value) {
		this.options.value = value;
		this.element.noUiSlider.set(value);
	};

	Slider.prototype.setOptions = function (options) {
		$.extend(this.options, options);
		this.create();
	};

	Slider.prototype.destroy = function () {
		this.element.noUiSlider.destroy();
		this.$element.empty().removeClass('ui-slider ui-slider-horizontal ui-slider-vertical');
		this.$element.removeData(DATA_KEY).removeData('ui-slider');
	};

	$.fn.slider = function (command, key, value) {
		var first = this.first().data(DATA_KEY), options;
		if (command === 'value' && key === undefined) return first ? first.getValue() : undefined;
		if (command === 'values' && key === undefined) return first && Array.isArray(first.element.noUiSlider.get()) ? $.map(first.element.noUiSlider.get(), Number) : [];
		if (command === 'option' && value === undefined && typeof key === 'string') return first ? first.options[key] : undefined;

		if (typeof command === 'string') {
			return this.each(function () {
				var instance = $(this).data(DATA_KEY);
				if (!instance) return;
				if (command === 'destroy') instance.destroy();
				else if (command === 'value') instance.setValue(key);
				else if (command === 'values') instance.element.noUiSlider.set(key);
				else if (command === 'option' && typeof key === 'object') instance.setOptions(key);
				else if (command === 'option') {
					options = {};
					options[key] = value;
					instance.setOptions(options);
				}
			});
		}

		return this.each(function () {
			var $element = $(this), instance = $element.data(DATA_KEY);
			if (instance) instance.setOptions(command || {});
			else {
				instance = new Slider(this, command || {});
				$element.data(DATA_KEY, instance).data('ui-slider', instance);
			}
		});
	};

	return Slider;
});