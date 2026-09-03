define(['interact'], function (interact) {
	'use strict';

	var $ = jQuery,
		DATA_KEY = 'upfront-resizable';

	function Resizable(element, options) {
		this.element = element;
		this.$element = $(element);
		this.options = $.extend({
			disabled: false,
			handles: 'e,s,se',
			minWidth: 10,
			minHeight: 10
		}, options);
		this.generatedHandles = [];
		this.axis = null;
		this.originalPosition = null;
		this.originalSize = null;
		this.position = null;
		this.size = null;
		this.$helper = null;
		this.parentData = {};
		this.interactable = interact(element);
		this.refresh();
	}

	Resizable.prototype.getHandleSelector = function (direction) {
		var handles = this.options.handles;
		if (typeof handles === 'object' && handles[direction]) return handles[direction];
		return '.ui-resizable-' + direction;
	};

	Resizable.prototype.ensureHandles = function () {
		var me = this,
			handles = this.options.handles,
			directions = typeof handles === 'string' ? handles.split(',') : Object.keys(handles || {});

		$.each(directions, function (index, rawDirection) {
			var direction = (rawDirection || '').toString().trim(),
				selector = me.getHandleSelector(direction);
			if (!direction || me.$element.find(selector).length) return;
			if (typeof handles === 'object') return;
			var $handle = $('<span />').addClass('ui-resizable-handle ui-resizable-' + direction);
			me.$element.append($handle);
			me.generatedHandles.push($handle[0]);
		});
	};

	Resizable.prototype.getEdges = function () {
		var edges = {top: false, right: false, bottom: false, left: false},
			handles = this.options.handles,
			directions = typeof handles === 'string' ? handles.split(',') : Object.keys(handles || {}),
			me = this;

		$.each(directions, function (index, rawDirection) {
			var direction = (rawDirection || '').toString().trim(),
				selector = me.getHandleSelector(direction);
			if (direction.indexOf('n') !== -1) edges.top = selector;
			if (direction.indexOf('e') !== -1) edges.right = selector;
			if (direction.indexOf('s') !== -1) edges.bottom = selector;
			if (direction.indexOf('w') !== -1) edges.left = selector;
		});
		return edges;
	};

	Resizable.prototype.refresh = function () {
		var me = this;
		this.ensureHandles();
		this.$element.addClass('ui-resizable');
		this.interactable.resizable({
			enabled: !this.options.disabled,
			edges: this.getEdges(),
			listeners: {
				start: function (event) { me.start(event); },
				move: function (event) { me.move(event); },
				end: function (event) { me.stop(event); }
			}
		});
		this.$element.toggleClass('ui-resizable-disabled', !!this.options.disabled);
	};

	Resizable.prototype.getAxis = function (event) {
		var axis = '';
		if (event.edges.top) axis += 'n';
		if (event.edges.bottom) axis += 's';
		if (event.edges.left) axis += 'w';
		if (event.edges.right) axis += 'e';
		return axis;
	};

	Resizable.prototype.getUi = function () {
		return {
			originalElement: this.$element,
			element: this.$element,
			helper: this.$helper || this.$element,
			originalPosition: this.originalPosition,
			originalSize: this.originalSize,
			position: this.position,
			size: this.size
		};
	};

	Resizable.prototype._trigger = function (type, event, ui) {
		ui = ui || this.getUi();
		if (typeof this.options[type] === 'function') this.options[type].call(this.element, event, ui);
		this.$element.triggerHandler(type, [ui]);
	};

	Resizable.prototype.start = function (event) {
		var position = this.$element.position(),
			offset;
		this.axis = this.getAxis(event);
		this.originalPosition = {left: position.left, top: position.top};
		this.originalSize = {width: this.$element.width(), height: this.$element.height()};
		this.position = $.extend({}, this.originalPosition);
		this.size = $.extend({}, this.originalSize);
		this.parentData.height = $(document).height();
		if (this.options.helper) {
			offset = this.$element.offset();
			this.$helper = $('<div />').addClass(this.options.helper).css({
				position: 'absolute',
				left: offset.left,
				top: offset.top,
				width: this.originalSize.width,
				height: this.originalSize.height,
				zIndex: this.options.zIndex || ''
			}).appendTo('body');
			this.position = {left: offset.left, top: offset.top};
		}
		this.$element.addClass('ui-resizable-resizing');
		if (typeof this.options.start === 'function') this.options.start.call(this.element, event, this.getUi());
		this.$element.triggerHandler('resizestart', [this.getUi()]);
	};

	Resizable.prototype.constrain = function (value, min, max) {
		value = Math.max(typeof min === 'number' ? min : 0, value);
		return typeof max === 'number' && max > 0 ? Math.min(max, value) : value;
	};

	Resizable.prototype.move = function (event) {
		var width = this.constrain(event.rect.width, this.options.minWidth, this.options.maxWidth),
			height = this.constrain(event.rect.height, this.options.minHeight, this.options.maxHeight),
			left = this.position.left + event.deltaRect.left,
			top = this.position.top + event.deltaRect.top,
			$target = this.$helper || this.$element,
			ui;

		if (this.options.aspectRatio) {
			var ratio = this.options.aspectRatio === true ? this.originalSize.width / this.originalSize.height : this.options.aspectRatio;
			if (this.axis.indexOf('e') !== -1 || this.axis.indexOf('w') !== -1) height = width / ratio;
			else width = height * ratio;
		}
		if (Array.isArray(this.options.grid)) {
			if (this.options.grid[0]) width = Math.round(width / this.options.grid[0]) * this.options.grid[0];
			if (this.options.grid[1]) height = Math.round(height / this.options.grid[1]) * this.options.grid[1];
		}

		this.position = {left: left, top: top};
		this.size = {width: width, height: height};
		ui = this.getUi();
		$target.css({left: ui.position.left, top: ui.position.top, width: ui.size.width, height: ui.size.height});
		if (typeof this.options.resize === 'function') this.options.resize.call(this.element, event, ui);
		this.$element.triggerHandler('resize', [ui]);
	};

	Resizable.prototype.stop = function (event) {
		if (!this.originalSize) return;
		var ui = this.getUi();
		if (this.$helper) {
			this.$element.css({width: this.$helper.width(), height: this.$helper.height()});
		}
		if (typeof this.options.stop === 'function') this.options.stop.call(this.element, event, ui);
		this.$element.triggerHandler('resizestop', [ui]);
		if (this.$helper) this.$helper.remove();
		this.$helper = null;
		this.$element.removeClass('ui-resizable-resizing');
	};

	Resizable.prototype._updateCache = function (values) {
		if (values.left !== undefined) this.position.left = values.left;
		if (values.top !== undefined) this.position.top = values.top;
		if (values.width !== undefined) this.size.width = values.width;
		if (values.height !== undefined) this.size.height = values.height;
	};

	Resizable.prototype.setOptions = function (options) {
		$.extend(this.options, options);
		this.refresh();
	};

	Resizable.prototype.destroy = function () {
		this.interactable.resizable(false);
		$(this.generatedHandles).remove();
		this.$element.removeClass('ui-resizable ui-resizable-disabled ui-resizable-resizing');
		this.$element.removeData(DATA_KEY).removeData('ui-resizable');
	};

	$.fn.resizable = function (command, key, value) {
		if (typeof command === 'string') {
			return this.each(function () {
				var instance = $(this).data(DATA_KEY), options;
				if (!instance) return;
				if (command === 'destroy') instance.destroy();
				else if (command === 'enable') instance.setOptions({disabled: false});
				else if (command === 'disable') instance.setOptions({disabled: true});
				else if (command === 'option' && typeof key === 'object') instance.setOptions(key);
				else if (command === 'option' && value !== undefined) {
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
				instance = new Resizable(this, command || {});
				$element.data(DATA_KEY, instance).data('ui-resizable', instance);
			}
		});
	};

	return Resizable;
});