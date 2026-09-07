(function ($, undefined) {
define(function () {

var l10n = Upfront.Settings.l10n.code_element;

var Checker = {

	_promise: false,
	_value: false,
	_message: false,

	/**
	 * Sets new value
	 *
	 * @param {String} value Value to set
	 *
	 * @return {Object} Promise
	 */
	set: function (value) {
		this._promise = new $.Deferred();
		this._value = value;
		this._message = false;

		return this._promise.promise();
	},

	/**
	 * Reset current value to default
	 */
	reset: function () {
		this.set(false);
	},

	/**
	 * Test current value for validity and notify promise listeners
	 */
	test: function () {
		this._message = false;
		this._promise.notify();
		if (this.validate()) {
			this._promise.resolve();
		} else {
			this._promise.reject(this._message);
		}
	},

	/**
	 * Value checking method, without actually setting the value
	 *
	 * @param {String} value Value to check
	 *
	 * @return {Boolean} Whether the new value is valid
	 */
	check: function (value) {
		this.set(value);
		var ret = this.validate();
		this.reset();
		return ret;
	},

	/**
	 * Placeholder for actual validation routine
	 *
	 * @return {Boolean} Whether we're valid or not
	 */
	validate: function () {
		return true;
	},

	/**
	 * Raw entry wrapping placeholder
	 *
	 * @param {String} what What to wrap
	 *
	 * @return {String} Wrapped entry
	 */
	wrap: function (what) {
		return what;
	},

	get_annotations: function () {
		return [];
	}
};

var Checker_Js = _.extend({}, Checker, {
	validate: function () {
		var ret = true;
		try {
			var f = new Function(this.wrap(this._value));
			f.apply();
		} catch (e) {
			this._message = e.message;
			ret = false;
		}

		return ret;
	},
	wrap: function (what) {
		return ';(function ($) { {' + what + ';}\n })(jQuery);'; // Do not use try/catch block for in-flight testing
	}
});

var Checker_Html = _.extend({}, Checker, {
	validate: function () {
		var annotations = this.get_annotations(this._value);
		if (annotations.length) {
			this._message = annotations[0].text;
			return false;
		}
		return true;
	},
	get_annotations: function (value) {
		var source = String(value || ''),
			matcher = /<!--[\s\S]*?-->|<![^>]*>|<\/?([a-z][\w:.-]*)(?:\s+(?:"[^"]*"|'[^']*'|[^'">])*)?\s*\/?>/gi,
			void_tags = {
				area: true, base: true, br: true, col: true, embed: true, hr: true,
				img: true, input: true, link: true, meta: true, param: true, source: true,
				track: true, wbr: true
			},
			stack = [],
			annotations = [],
			match;

		while ((match = matcher.exec(source))) {
			var tag = match[1].toLowerCase(),
				line = source.slice(0, match.index).split('\n').length - 1,
				is_closing = /^<\//.test(match[0]),
				is_self_closing = /\/\s*>$/.test(match[0]) || void_tags[tag],
				open;

			if (is_self_closing) continue;
			if (!is_closing) {
				stack.push({tag: tag, line: line});
				continue;
			}

			open = stack.pop();
			if (!open || open.tag !== tag) {
				annotations.push({row: line, column: 0, text: 'Unerwarteter schließender Tag </' + tag + '>', type: 'error'});
				if (open) stack.push(open);
			}
		}

		_.each(stack, function (open) {
			annotations.push({row: open.line, column: 0, text: 'Nicht geschlossener Tag <' + open.tag + '>', type: 'error'});
		});

		return annotations;
	}
});

var Syntax = function () {
	var _types = {
		"markup": "html",
		"style": "css",
		"script": "javascript"
	};
	var _checkers = {
		"markup": _.extend(Checker_Html, {}),
		"script": _.extend(Checker_Js, {})
	};

	var _get_checker = function (syntax) {
		var checker = syntax in _checkers
			? _checkers[syntax]
			: _.extend(Checker, {})
		;
		checker.reset();
		return checker;
	};

	return {
		TYPES: _types,
		checker: _get_checker
	};
};

return new Syntax();

});
})(jQuery);
