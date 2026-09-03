define(['sortable'], function (Sortable) {
	'use strict';

	var $ = jQuery,
		DATA_KEY = 'upfront-sortable';

	function normalizeItems(selector) {
		selector = selector || '> *';
		return selector.replace(/^\s*>\s*/, '');
	}

	function SortableAdapter(element, options) {
		this.element = element;
		this.$element = $(element);
		this.options = $.extend({disabled: false}, options);
		this.create();
	}

	SortableAdapter.prototype.getUi = function (event) {
		return {
			item: $(event.item),
			helper: $(event.item),
			placeholder: $(event.clone || []),
			sender: event.from === event.to ? null : $(event.from)
		};
	};

	SortableAdapter.prototype.create = function () {
		var me = this,
			placeholder = this.options.placeholder || 'ui-sortable-placeholder';

		this.$element.addClass('ui-sortable');
		this.sortable = Sortable.create(this.element, {
			draggable: normalizeItems(this.options.items),
			handle: this.options.handle || null,
			filter: this.options.cancel || 'input, textarea, button, select, option, [contenteditable]',
			preventOnFilter: false,
			disabled: !!this.options.disabled,
			direction: this.options.axis === 'x' ? 'horizontal' : 'vertical',
			ghostClass: placeholder,
			chosenClass: 'ui-sortable-helper',
			dragClass: 'ui-sortable-helper',
			group: this.options.connectWith ? {name: this.options.connectWith, pull: true, put: true} : undefined,
			onStart: function (event) {
				me.$element.addClass('upfront-sortable-active');
				if (typeof me.options.start === 'function') me.options.start.call(me.element, event.originalEvent || event, me.getUi(event));
			},
			onAdd: function (event) {
				if (typeof me.options.receive === 'function') me.options.receive.call(me.element, event.originalEvent || event, me.getUi(event));
			},
			onUpdate: function (event) {
				if (typeof me.options.update === 'function') me.options.update.call(me.element, event.originalEvent || event, me.getUi(event));
			},
			onEnd: function (event) {
				me.$element.removeClass('upfront-sortable-active');
				if (typeof me.options.stop === 'function') me.options.stop.call(me.element, event.originalEvent || event, me.getUi(event));
			}
		});
		this.setDisabled(this.options.disabled);
	};

	SortableAdapter.prototype.setDisabled = function (disabled) {
		this.options.disabled = !!disabled;
		this.sortable.option('disabled', this.options.disabled);
		this.$element.toggleClass('ui-sortable-disabled', this.options.disabled);
	};

	SortableAdapter.prototype.setOptions = function (options) {
		var recreate = options.items !== undefined || options.handle !== undefined || options.cancel !== undefined || options.axis !== undefined || options.connectWith !== undefined;
		$.extend(this.options, options);
		if (recreate) {
			this.sortable.destroy();
			this.create();
		} else {
			this.setDisabled(this.options.disabled);
		}
	};

	SortableAdapter.prototype.toArray = function (options) {
		var attribute = options && options.attribute ? options.attribute : 'id';
		return this.$element.children(normalizeItems(this.options.items)).map(function () {
			return $(this).attr(attribute) || '';
		}).get();
	};

	SortableAdapter.prototype.destroy = function () {
		this.sortable.destroy();
		this.$element.removeClass('ui-sortable ui-sortable-disabled upfront-sortable-active');
		this.$element.removeData(DATA_KEY).removeData('ui-sortable');
	};

	$.fn.sortable = function (command, key, value) {
		var first = this.first().data(DATA_KEY), options;

		if (command === 'toArray') return first ? first.toArray(key) : [];
		if (command === 'option' && value === undefined && typeof key === 'string') return first ? first.options[key] : undefined;

		if (typeof command === 'string') {
			return this.each(function () {
				var instance = $(this).data(DATA_KEY);
				if (!instance) return;
				if (command === 'destroy') instance.destroy();
				else if (command === 'disable') instance.setDisabled(true);
				else if (command === 'enable') instance.setDisabled(false);
				else if (command === 'refresh') instance.setOptions({});
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
				instance = new SortableAdapter(this, command || {});
				$element.data(DATA_KEY, instance).data('ui-sortable', instance);
			}
		});
	};

	$.fn.disableSelection = function () {
		return this.addClass('upfront-selection-disabled').css('user-select', 'none');
	};

	$.fn.enableSelection = function () {
		return this.removeClass('upfront-selection-disabled').css('user-select', '');
	};

	return SortableAdapter;
});