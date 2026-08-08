define([], function () {
	'use strict';

	var durations = {
		fast: 200,
		slow: 600
	};

	function getDuration(duration) {
		if (typeof duration === 'number') return duration;
		return durations[duration] || 400;
	}

	function showElement(element) {
		element.hidden = false;
		element.style.display = '';
		if (window.getComputedStyle(element).display === 'none') {
			element.style.display = 'block';
		}
	}

	function slideIn(element, direction, duration, complete) {
		if (!element) return false;

		showElement(element);

		if (!element.animate || (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
			if (typeof complete === 'function') complete.call(element);
			return false;
		}

		var previousWillChange = element.style.willChange,
			offset = direction === 'right' ? '100%' : '-100%',
			animation = element.animate([
				{ transform: 'translateX(' + offset + ')', opacity: 0 },
				{ transform: 'translateX(0)', opacity: 1 }
			], {
				duration: getDuration(duration),
				easing: 'cubic-bezier(0.22, 1, 0.36, 1)'
			})
		;

		element.style.willChange = 'transform, opacity';

		animation.onfinish = function () {
			element.style.willChange = previousWillChange;
			if (typeof complete === 'function') complete.call(element);
		};
		animation.oncancel = function () {
			element.style.willChange = previousWillChange;
		};

		return animation;
	}

	return {
		slideIn: slideIn
	};
});
