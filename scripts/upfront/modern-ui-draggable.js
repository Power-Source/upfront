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
		this.syntheticDrag = null;
		this.enabled = false;
		this.interactable = interact(element);
		this.bindSyntheticMouseBridge();
		if (this.options.disabled) this.disable();
		else this.enable();
	}

	Draggable.prototype.bindSyntheticMouseBridge = function () {
		var me = this,
			namespace = '.upfront-draggable-' + Math.random().toString(36).slice(2);
		this.syntheticNamespace = namespace;
		this.$element.on('mousedown' + namespace, function (event) {
			if (!event.isTrigger || me.options.disabled) return;
			var lastX = event.pageX,
				lastY = event.pageY;
			me.syntheticDrag = {started: false};
			$(document)
				.off(namespace)
				.on('mousemove' + namespace, function (moveEvent) {
					if (!me.syntheticDrag) return;
					moveEvent.dx = moveEvent.pageX - lastX;
					moveEvent.dy = moveEvent.pageY - lastY;
					if (!me.syntheticDrag.started) {
						me.syntheticDrag.started = true;
						me.start(moveEvent);
					}
					me.move(moveEvent);
					lastX = moveEvent.pageX;
					lastY = moveEvent.pageY;
				})
				.one('mouseup' + namespace, function (upEvent) {
					if (me.syntheticDrag && me.syntheticDrag.started) me.stop(upEvent);
					me.syntheticDrag = null;
					$(document).off(namespace);
				});
		});
	};

	Draggable.prototype.enable = function () {
		var me = this;

		if (this.enabled) return this;
		this.enabled = true;
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
		if (!this.enabled) return this;
		this.enabled = false;
		this.interactable.draggable({enabled: false});
		return this;
	};

	Draggable.prototype.destroy = function () {
		this.interactable.draggable(false);
		this.enabled = false;
		this.$element.off(this.syntheticNamespace);
		$(document).off(this.syntheticNamespace);
		this.$element.removeClass('ui-draggable ui-draggable-disabled ui-draggable-dragging');
		this.$element.removeData(DATA_KEY).removeData('ui-draggable');
	};

	Draggable.prototype.createHelper = function (event) {
		var helper = this.options.helper,
			$helper;

		if (typeof helper === 'function') {
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

	Draggable.prototype.triggerDragEvent = function (type, event, ui) {
		var dragEvent = $.Event(type);
		dragEvent.pageX = typeof event.pageX === 'number' ? event.pageX : this.drag.pointer.pageX;
		dragEvent.pageY = typeof event.pageY === 'number' ? event.pageY : this.drag.pointer.pageY;
		dragEvent.originalEvent = event.originalEvent || event;
		this.$element.triggerHandler(dragEvent, [ui]);
	};

	Draggable.prototype.getUi = function () {
		return {
			helper: this.drag.$helper,
			position: this.drag.position,
			offset: this.drag.offset,
			originalPosition: this.drag.originalPosition
		};
	};

	Draggable.prototype._adjustOffsetFromHelper = function (offset) {
		if (!this.drag || !offset) return;
		var pointerX = this.drag.pointer.pageX,
			pointerY = this.drag.pointer.pageY;
		if (typeof offset.left === 'number') this.drag.offset.left = pointerX - offset.left;
		if (typeof offset.top === 'number') this.drag.offset.top = pointerY - offset.top;
		this.drag.$helper.css({left: this.drag.offset.left, top: this.drag.offset.top});
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
			offset: {left: offset.left, top: offset.top},
			pointer: {
				pageX: typeof event.pageX === 'number' ? event.pageX : event.clientX + window.pageXOffset,
				pageY: typeof event.pageY === 'number' ? event.pageY : event.clientY + window.pageYOffset
			}
		};
		$helper.addClass('ui-draggable-dragging');
		if (this.options.zIndex !== false) $helper.css('z-index', this.options.zIndex);

		if (typeof this.options.start === 'function' && this.options.start.call(this.element, event, this.getUi()) === false) {
			this.stop(event);
			return false;
		}
		this.triggerDragEvent('dragstart', event, this.getUi());
	};

	Draggable.prototype.move = function (event) {
		var ui;
		if (!this.drag) return;

		this.drag.position.left += event.dx;
		this.drag.position.top += event.dy;
		this.drag.offset.left += event.dx;
		this.drag.offset.top += event.dy;
		if (typeof event.pageX === 'number') this.drag.pointer.pageX = event.pageX;
		if (typeof event.pageY === 'number') this.drag.pointer.pageY = event.pageY;
		ui = this.getUi();
		if (typeof this.options.drag === 'function') this.options.drag.call(this.element, event, ui);

		this.drag.$helper.css({left: ui.offset.left, top: ui.offset.top});
		this.triggerDragEvent('drag', event, ui);
	};

	Draggable.prototype.stop = function (event) {
		var ui;
		if (!this.drag) return;

		ui = this.getUi();
		this.triggerDragEvent('dragstop', event, ui);
		if (typeof this.options.stop === 'function') this.options.stop.call(this.element, event, ui);
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