(function($){
	var l10n = Upfront.Settings && Upfront.Settings.l10n
			? Upfront.Settings.l10n.global.views
			: Upfront.mainData.l10n.global.views
		;
	define([
		'scripts/upfront/upfront-views-editor/commands/command'
	], function ( Command ) {

		return Command.extend({
			render: function () {
				this.$el.html('<a href="' + Upfront.Settings.admin_url + '">' + l10n.wp_admin + '</a>');
			},
			on_click: function () {
				window.location.assign(Upfront.Settings.admin_url);
			}
		});

	});
}(jQuery));