(function ($) {
define([
], function () {
	var l10n = Upfront.Settings && Upfront.Settings.l10n
		? Upfront.Settings.l10n.global.views
		: Upfront.mainData.l10n.global.views;

	var RootPanelMixin = {
		className: 'uf-settings-panel upfront-settings_panel',

		events: {
			'click .uf-settings-panel__title': 'toggleBody'
		},

		getTitle: function () {
			var title = this.options.title ? this.options.title : this.title;
			title = title ? title : l10n.settings;
			return title;
		},

		/**
		 * Child classes need to override this method to return div with markup.
		 */
		getBody: function() {
			var $body = $('<div />');
			$body.append('<p>Implement getBody() in child class</p>');
			return $body;
		},

		toggleBody: function () {
			if ( this.$el.hasClass('uf-settings-panel--expanded') ) {
				this.hideBody();
				return;
			}

			this.$el
				.siblings('.uf-settings-panel--expanded')
				.removeClass('uf-settings-panel--expanded')
				.children('.uf-settings-panel__body').hide();
			this.showBody();
		},

		/**
		 * Hides panel body
		 */
		hideBody: function () {
			this.$el.children('.uf-settings-panel__body').hide();
			this.$el.removeClass('uf-settings-panel--expanded');
		},

		/**
		 * Shows panel body
		 */
		showBody: function () {
			this.$el.children('.uf-settings-panel__body').show();
			this.$el.addClass('uf-settings-panel--expanded');
		},

		render: function () {
			var body;

			this.$el.html('<div class="uf-settings-panel__title">' + this.getTitle() + '</div>');

			body = this.getBody();
			body.addClass('uf-settings-panel__body');
			this.$el.append(body);
			this.$el.addClass('uf-settings-panel--expanded');
		}
	};

	return RootPanelMixin;
});
})(jQuery);
