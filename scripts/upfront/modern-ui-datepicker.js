define(['flatpickr'], function (flatpickr) {
	'use strict';

	var $ = jQuery,
		DATA_KEY = 'upfront-datepicker';

	function convertFormat(format) {
		var tokens = {d: 'j', dd: 'd', D: 'D', DD: 'l', o: 'z', oo: 'z', m: 'n', mm: 'm', M: 'M', MM: 'F', y: 'y', yy: 'Y'},
			result = '',
			i = 0,
			literal = false;

		while (i < format.length) {
			if (format.charAt(i) === "'") {
				if (format.charAt(i + 1) === "'") {
					result += "'";
					i += 2;
					continue;
				}
				literal = !literal;
				i++;
				continue;
			}
			var pair = format.substr(i, 2);
			if (!literal && tokens[pair]) {
				result += tokens[pair];
				i += 2;
			} else if (!literal && tokens[format.charAt(i)]) {
				result += tokens[format.charAt(i)];
				i++;
			} else {
				result += literal ? '\\' + format.charAt(i) : format.charAt(i);
				i++;
			}
		}
		return result;
	}

	function Datepicker(element, options) {
		this.element = element;
		this.$element = $(element);
		this.options = $.extend({dateFormat: 'mm/dd/yy'}, options);
		this.inline = !/^(input|textarea)$/i.test(element.tagName);
		this.$input = this.inline ? $('<input type="text" class="upfront-flatpickr-input" aria-hidden="true" tabindex="-1">').appendTo(this.$element) : this.$element;
		this.create();
	}

	Datepicker.prototype.create = function () {
		var me = this,
			lastMonth,
			lastYear;

		this.picker = flatpickr(this.$input[0], {
			inline: this.inline,
			clickOpens: !this.inline,
			allowInput: !this.inline,
			dateFormat: convertFormat(this.options.dateFormat),
			defaultDate: this.options.defaultDate || this.$input.val() || null,
			onReady: function (dates, value, instance) {
				var date = dates[0] || new Date();
				lastMonth = date.getMonth();
				lastYear = date.getFullYear();
				me.$element.addClass('hasDatepicker');
				$(instance.calendarContainer).addClass('ui-datepicker');
			},
			onMonthChange: function (dates, value, instance) {
				me.monthYearChanged(instance, lastMonth, lastYear);
				lastMonth = instance.currentMonth;
				lastYear = instance.currentYear;
			},
			onYearChange: function (dates, value, instance) {
				me.monthYearChanged(instance, lastMonth, lastYear);
				lastMonth = instance.currentMonth;
				lastYear = instance.currentYear;
			},
			onChange: function (dates, value) {
				if ($.isFunction(me.options.onSelect)) me.options.onSelect.call(me.element, value, me);
			}
		});
	};

	Datepicker.prototype.monthYearChanged = function (instance, previousMonth, previousYear) {
		if (!$.isFunction(this.options.onChangeMonthYear)) return;
		if (instance.currentMonth === previousMonth && instance.currentYear === previousYear) return;
		var selected = instance.selectedDates[0] || new Date();
		this.options.onChangeMonthYear.call(this.element, instance.currentYear, instance.currentMonth + 1, {
			selectedDay: selected.getDate(),
			input: this.$input[0],
			instance: instance
		});
	};

	Datepicker.prototype.setDate = function (date) {
		this.picker.setDate(date, false);
	};

	Datepicker.prototype.getDate = function () {
		return this.picker.selectedDates[0] || null;
	};

	Datepicker.prototype.destroy = function () {
		this.picker.destroy();
		if (this.inline) this.$input.remove();
		this.$element.removeClass('hasDatepicker').removeData(DATA_KEY).removeData('datepicker');
	};

	$.fn.datepicker = function (command, value) {
		var first = this.first().data(DATA_KEY);
		if (command === 'getDate') return first ? first.getDate() : null;

		if (typeof command === 'string') {
			return this.each(function () {
				var instance = $(this).data(DATA_KEY);
				if (!instance) return;
				if (command === 'setDate') instance.setDate(value);
				else if (command === 'destroy') instance.destroy();
			});
		}

		return this.each(function () {
			var $element = $(this), instance = $element.data(DATA_KEY);
			if (instance) instance.destroy();
			instance = new Datepicker(this, command || {});
			$element.data(DATA_KEY, instance).data('datepicker', instance);
		});
	};

	$.datepicker = {
		formatDate: function (format, date) {
			return flatpickr.formatDate(date, convertFormat(format));
		}
	};

	return Datepicker;
});