(function($){
	var l10n = Upfront.Settings && Upfront.Settings.l10n
			? Upfront.Settings.l10n.global.views
			: Upfront.mainData.l10n.global.views
		;
	define([
		'scripts/upfront/upfront-views-editor/commands/command'
	], function ( Command ) {
		var supportUrl = 'https://psource.eimen.net/wiki/upfront-dokumentation/';

		return Command.extend({
			initialize: function () {
				this.el.addEventListener('click', function (event) {
					event.preventDefault();
					event.stopImmediatePropagation();
					window.open(supportUrl, '_blank', 'noopener,noreferrer');
				}, true);
			},
			render: function () {
				this.$el.html(l10n.help_and_support);
			},
			on_click: function () {}
		});

	});
}(jQuery));