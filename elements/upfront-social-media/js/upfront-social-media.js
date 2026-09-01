(function ($) {
define([
	'require',
	'scripts/upfront/element-settings/settings',
	'scripts/upfront/element-settings/root-settings-panel'
], function (localRequire, ElementSettings, RootSettingsPanel) {
	var l10n = Upfront.Settings.l10n.social_element,
		fallbackRegistry = {
			facebook: {id: 'facebook', name: 'Facebook', modes: ['share', 'profile']},
			twitter: {id: 'twitter', name: 'X', modes: ['share', 'profile']},
			instagram: {id: 'instagram', name: 'Instagram', modes: ['profile']},
			linkedin: {id: 'linkedin', name: 'LinkedIn', modes: ['share', 'profile']},
			youtube: {id: 'youtube', name: 'YouTube', modes: ['profile']},
			pinterest: {id: 'pinterest', name: 'Pinterest', modes: ['profile']},
			tiktok: {id: 'tiktok', name: 'TikTok', modes: ['profile']},
			threads: {id: 'threads', name: 'Threads', modes: ['profile']},
			bluesky: {id: 'bluesky', name: 'Bluesky', modes: ['share', 'profile']},
			mastodon: {id: 'mastodon', name: 'Mastodon', modes: ['profile']},
			reddit: {id: 'reddit', name: 'Reddit', modes: ['share', 'profile']},
			whatsapp: {id: 'whatsapp', name: 'WhatsApp', modes: ['share', 'profile']},
			telegram: {id: 'telegram', name: 'Telegram', modes: ['share', 'profile']},
			github: {id: 'github', name: 'GitHub', modes: ['profile']},
			email: {id: 'email', name: 'E-Mail', modes: ['share', 'profile']}
		},
		registry = (Upfront.data.usocial && Upfront.data.usocial.services) || fallbackRegistry,
		iconUrl = (Upfront.data.usocial && Upfront.data.usocial.icon_url) || localRequire.toUrl('elements/upfront-social-media/images/social-icons.svg');

	function canonicalId(id) {
		if (id === 'x') return 'twitter';
		if (id === 'linked-in') return 'linkedin';
		return id;
	}

	function mergeServices(current, mode) {
		var saved = {};
		_.each(current || [], function (service) {
			service = _.extend({}, service, {id: canonicalId(service.id), meta: []});
			saved[service.id] = service;
		});
		return _.chain(registry)
			.filter(function (service) { return _.contains(service.modes, mode); })
			.map(function (service) {
				return _.extend({active: false, url: '', meta: []}, service, saved[service.id] || {});
			})
			.value();
	}

	var SocialMediaModel = Upfront.Models.ObjectModel.extend({
		init: function () {
			var socialData = Upfront.data.usocial || {},
				defaults = _.clone(socialData.defaults || {}),
				globals = socialData.globals || {},
				globalServices = _.isArray(globals.services) ? globals.services : [],
				defaultServices = _.isArray(defaults.services) ? defaults.services : [],
				defaultButtonServices = _.isArray(defaults.button_services) ? defaults.button_services : [];
			defaults.services = mergeServices(defaultServices.length ? defaultServices : globalServices, 'share');
			defaults.button_services = mergeServices(defaultButtonServices.length ? defaultButtonServices : globalServices, 'profile');
			defaults.id_slug = defaults.id_slug || 'SocialMedia';
			defaults.social_type = defaults.social_type || 'likes';
			defaults.button_size = defaults.button_size || 'medium';
			defaults.button_style = defaults.button_style || 'button-style-3';
			defaults.element_id = Upfront.Util.get_unique_id(defaults.id_slug + '-object');
			this.init_properties(defaults);
			if (this.get_property_value_by_name('social_type') === 'fans') this.set_property('social_type', 'buttons', true);
			this.set_property('services', mergeServices(this.get_property_value_by_name('services'), 'share'), true);
			this.set_property('button_services', mergeServices(this.get_property_value_by_name('button_services'), 'profile'), true);
		}
	});

	var SocialMediaView = Upfront.Views.ObjectView.extend({
		initialize: function () {
			if (!(this.model instanceof SocialMediaModel)) this.model = new SocialMediaModel({properties: this.model.get('properties')});
			this.constructor.__super__.initialize.apply(this, arguments);
		},
		property: function (name) {
			return this.model.get_property_value_by_name(name);
		},
		shareUrl: function (id) {
			var url = encodeURIComponent(window.location.href),
				title = encodeURIComponent(document.title || ''),
				text = encodeURIComponent((document.title || '') + ' ' + window.location.href),
				urls = {
					facebook: 'https://www.facebook.com/sharer/sharer.php?u=' + url,
					twitter: 'https://twitter.com/intent/tweet?text=' + title + '&url=' + url,
					linkedin: 'https://www.linkedin.com/sharing/share-offsite/?url=' + url,
					bluesky: 'https://bsky.app/intent/compose?text=' + text,
					reddit: 'https://www.reddit.com/submit?url=' + url + '&title=' + title,
					whatsapp: 'https://wa.me/?text=' + text,
					telegram: 'https://t.me/share/url?url=' + url + '&text=' + title,
					email: 'mailto:?subject=' + title + '&body=' + text
				};
			return urls[canonicalId(id)] || '';
		},
		icon: function (id) {
			id = canonicalId(id);
			var icon = _.escape(iconUrl) + '#social-' + _.escape(id);
			return '<svg class="usocial-icon" aria-hidden="true" focusable="false"><use href="' + icon + '" xlink:href="' + icon + '"></use></svg>';
		},
		renderServices: function (services, mode) {
			var me = this, markup = '';
			_.each(services || [], function (service) {
				if (!service.active) return;
				var id = canonicalId(service.id),
					href = mode === 'share' ? me.shareUrl(id) : service.url,
					name = service.name || (registry[id] ? registry[id].name : id);
				if (!href) {
					markup += '<span class="upfront-social-icon usocial-missing-url usocial-' + _.escape(id) + '" title="' + _.escape(l10n.url_missing) + '">' + me.icon(id) + '<span class="alert-url">!</span></span>';
					return;
				}
				markup += '<a class="upfront-social-icon usocial-' + _.escape(id) + '" href="' + _.escape(href) + '" target="_blank" rel="noopener noreferrer" aria-label="' + _.escape(name) + '">' + me.icon(id) + '</a>';
			});
			return markup;
		},
		get_content_markup: function () {
			var mode = this.property('social_type') === 'likes' ? 'share' : 'profile',
				services = mode === 'share' ? this.property('services') : this.property('button_services'),
				markup = this.renderServices(services, mode),
				size = this.property('button_size') || 'medium',
				style = this.property('button_style') || 'button-style-3';
			return markup ? '<div class="upfront-social usocial-button-' + _.escape(size) + ' upfront-' + _.escape(style) + '">' + markup + '</div>' : '<span class="upfront-general-notice">' + l10n.select_some + '</span>';
		}
	});

	var SocialMediaElement = Upfront.Views.Editor.Sidebar.Element.extend({
		priority: 60,
		draggable: true,
		render: function () {
			this.$el.addClass('upfront-icon-element upfront-icon-element-social').html(l10n.element_name);
		},
		add_element: function () {
			this.add_module(new Upfront.Models.Module({
				name: '',
				properties: [
					{name: 'element_id', value: Upfront.Util.get_unique_id('module')},
					{name: 'class', value: 'c8 upfront-social-media_module'},
					{name: 'has_settings', value: 0},
					{name: 'row', value: Upfront.Util.height_to_row(60)}
				],
				objects: [new SocialMediaModel()]
			}));
		}
	});

	var SocialServicesField = Upfront.Views.Editor.Field.Field.extend({
		className: 'usocial-services-field',
		events: {'change input[type=checkbox]': 'toggleService', 'change input[type=url]': 'updateUrl'},
		mode: function () { return this.model.get_property_value_by_name('social_type') === 'likes' ? 'share' : 'profile'; },
		propertyName: function () { return this.mode() === 'share' ? 'services' : 'button_services'; },
		services: function () { return mergeServices(this.model.get_property_value_by_name(this.propertyName()), this.mode()); },
		render: function () {
			var mode = this.mode(), markup = '<div class="usocial-service-list">';
			_.each(this.services(), function (service) {
				markup += '<div class="usocial-service-row"><label><input type="checkbox" value="' + _.escape(service.id) + '"' + (service.active ? ' checked="checked"' : '') + '> <span>' + _.escape(service.name) + '</span></label>';
				if (mode === 'profile') markup += '<input type="url" data-service="' + _.escape(service.id) + '" value="' + _.escape(service.url || '') + '" placeholder="https://">';
				markup += '</div>';
			});
			this.$el.html(markup + '</div>');
			this.trigger('rendered');
			return this;
		},
		saveService: function (id, callback) {
			var services = this.services();
			_.each(services, function (service) { if (service.id === id) callback(service); });
			this.model.set_property(this.propertyName(), services, false);
			this.model.trigger('change');
		},
		toggleService: function (event) {
			var input = $(event.currentTarget);
			this.saveService(input.val(), function (service) { service.active = input.is(':checked'); });
		},
		updateUrl: function (event) {
			var input = $(event.currentTarget);
			this.saveService(input.data('service'), function (service) { service.url = input.val(); });
		}
	});

	var SocialSettingsPanel = RootSettingsPanel.extend({
		className: 'usocial-settings-panel',
		title: l10n.networks || 'Soziale Netzwerke',
		initialize: function (opts) {
			this.options = opts;
			var Fields = Upfront.Views.Editor.Field,
				SettingsItem = Upfront.Views.Editor.Settings.Item,
				servicesField = new SocialServicesField({model: this.model}),
				updateAppearance = function (property, value) {
					this.model.set_property(property, value, true);
					this.model.trigger('change');
				};
			this.settings = _([
				new SettingsItem({model: this.model, title: l10n.mode, fields: [new Fields.Radios_Inline({
					model: this.model, property: 'social_type', label: '',
					values: [{label: l10n.share, value: 'likes'}, {label: l10n.profiles, value: 'buttons'}],
					change: function (value) { this.model.set_property('social_type', value); servicesField.render(); }
				})]}),
				new SettingsItem({model: this.model, title: l10n.services, fields: [servicesField]}),
				new SettingsItem({model: this.model, title: l10n.appearance, fields: [
					new Fields.Radios_Inline({model: this.model, property: 'button_size', label: l10n.size, values: [
						{label: l10n.small, value: 'small'}, {label: l10n.medium, value: 'medium'}, {label: l10n.large, value: 'large'}
					], change: function (value) { updateAppearance.call(this, 'button_size', value); }}),
					new Fields.Radios_Inline({model: this.model, property: 'button_style', label: l10n.shape, values: [
						{label: l10n.square, value: 'button-style-1'}, {label: l10n.rounded, value: 'button-style-2'}, {label: l10n.circle, value: 'button-style-3'}
					], change: function (value) { updateAppearance.call(this, 'button_style', value); }})
				]}),
				new Upfront.Views.Editor.Settings.Settings_CSS({model: this.model})
			]);
		},
		get_label: function () { return l10n.settings; },
		get_title: function () { return l10n.settings; }
	});

	var SocialSettings = ElementSettings.extend({panels: {Settings: SocialSettingsPanel}, title: l10n.settings});
	Upfront.Application.LayoutEditor.add_object('SocialMedia', {
		Model: SocialMediaModel, View: SocialMediaView, Element: SocialMediaElement, Settings: SocialSettings,
		cssSelectors: {
			'.upfront-social': {label: l10n.css.container_label, info: l10n.css.container_info},
			'.upfront-social-icon': {label: l10n.css.box_label, info: l10n.css.box_info}
		},
		cssSelectorsId: Upfront.data.usocial.defaults.type
	});
	Upfront.Models.SocialMediaModel = SocialMediaModel;
	Upfront.Views.SocialMediaView = SocialMediaView;
});
})(jQuery);
