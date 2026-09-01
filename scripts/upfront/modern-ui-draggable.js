define(['interact'], function (interact) {
	'use strict';

	var $ = jQuery,
		DATA_KEY = 'upfront-draggable',
		DEFAULT_CANCEL = 'input, textarea, button, select, option, [contenteditable]';

	function Draggable(element, options) {
		this.element = element;
		this.$element = $(element);
		this.options = $.extend({
			disabled: false,
			cancel: DEFAULT_CANCEL,
			helper: 'original',
			revert: false,
			appendTo: 'body',
			zIndex: false
		}, options);
		this.drag = null;
		this.interactable = interact(element);
		this.enable();
	}

	Draggable.prototype.enable = function () {
		var me = this;

		this.options.disabled = false;
		this.$element.addClass('ui-draggable').removeClass('ui-draggable-disabled');
		this.interactable.draggable({
			enabled: true,
			ignoreFrom: this.options.cancel || DEFAULT_CANCEL,
			listeners: {
				start: function (event) { me.start(event); },
				move: function (event) { me.move(event); },
				end: function (event) { me.stop(event); }
			}
		});
		return this;
	};

	Draggable.prototype.disable = function () {
		this.options.disabled = true;
		this.$element.addClass('ui-draggable ui-draggable-disabled');
		this.interactable.draggable({enabled: false});
		return this;
	};

	Draggable.prototype.destroy = function () {
		this.interactable.draggable(false);
		this.$element.removeClass('ui-draggable ui-draggable-disabled ui-draggable-dragging');
		this.$element.removeData(DATA_KEY).removeData('ui-draggable');
	};

	Draggable.prototype.createHelper = function (event) {
		var helper = this.options.helper,
			$helper;

		if ($.isFunction(helper)) {
			$helper = $(helper.call(this.element, event));
		} else if (helper === 'clone') {
			$helper = this.$element.clone().removeAttr('id');
		} else {
			return this.$element;
		}

		if (!$helper.parent().length) $helper.appendTo(this.options.appendTo || 'body');
		$helper.css({
			position: 'absolute',
			left: this.$element.offset().left,
			top: this.$element.offset().top,
			width: this.$element.outerWidth(),
			height: this.$element.outerHeight()
		});
		return $helper;
	};

	Draggable.prototype.getUi = function () {
		return {
			helper: this.drag.$helper,
			position: this.drag.position,
			offset: this.drag.offset,
			originalPosition: this.drag.originalPosition
		};
	};

	Draggable.prototype.start = function (event) {
		var position = this.$element.position(),
			offset = this.$element.offset(),
			$helper = this.createHelper(event);

		this.drag = {
			$helper: $helper,
			isOriginal: $helper[0] === this.element,
			originalPosition: {left: position.left, top: position.top},
			position: {left: position.left, top: position.top},
			offset: {left: offset.left, top: offset.top}
		};
		$helper.addClass('ui-draggable-dragging');
		if (this.options.zIndex !== false) $helper.css('z-index', this.options.zIndex);

		if ($.isFunction(this.options.start) && this.options.start.call(this.element, event, this.getUi()) === false) {
			this.stop(event);
			return false;
		}
		this.$element.triggerHandler('dragstart', [this.getUi()]);
	};

	Draggable.prototype.move = function (event) {
		var ui;
		if (!this.drag) return;

		this.drag.position.left += event.dx;
		this.drag.position.top += event.dy;
		this.drag.offset.left += event.dx;
		this.drag.offset.top += event.dy;
		ui = this.getUi();
		if ($.isFunction(this.options.drag)) this.options.drag.call(this.element, event, ui);

		this.drag.$helper.css({left: ui.offset.left, top: ui.offset.top});
		this.$element.triggerHandler('drag', [ui]);
	};

	Draggable.prototype.stop = function (event) {
		var ui;
		if (!this.drag) return;

		ui = this.getUi();
		if ($.isFunction(this.options.stop)) this.options.stop.call(this.element, event, ui);
		this.$element.triggerHandler('dragstop', [ui]);
		this.drag.$helper.removeClass('ui-draggable-dragging');

		if (!this.drag.isOriginal) {
			this.drag.$helper.remove();
		} else if (this.options.revert) {
			this.$element.css(this.drag.originalPosition);
		}
		this.drag = null;
	};

	Draggable.prototype.setOptions = function (options) {
		$.extend(this.options, options);
		if (this.options.disabled) this.disable();
		else this.enable();
	};

	$.fn.draggable = function (command, key, value) {
		var first = this.first().data(DATA_KEY),
			result = this;

		if (command === 'option' && value === undefined && typeof key === 'string') {
			return first ? first.options[key] : undefined;
		}

		if (typeof command === 'string') {
			this.each(function () {
				var instance = $(this).data(DATA_KEY);
				if (!instance) return;
				if (command === 'enable') instance.enable();
				else if (command === 'disable') instance.disable();
				else if (command === 'destroy') instance.destroy();
				else if (command === 'option' && typeof key === 'object') instance.setOptions(key);
				else if (command === 'option' && value !== undefined) {
					var option = {};
					option[key] = value;
					instance.setOptions(option);
				}
			});
			return result;
		}

		return this.each(function () {
			var $element = $(this),
				instance = $element.data(DATA_KEY);
			if (instance) instance.setOptions(command || {});
			else {
				instance = new Draggable(this, command || {});
				$element.data(DATA_KEY, instance).data('ui-draggable', instance);
				if (instance.options.disabled) instance.disable();
			}
		});
	};

	return Draggable;
});