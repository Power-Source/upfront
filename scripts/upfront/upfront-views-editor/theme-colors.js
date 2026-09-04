(function(){

    define([
        'scripts/upfront/upfront-views-editor/theme-colors/collection'
    ], function (Collection) {
		var MAX_THEME_COLORS = 10,
			colors = Upfront.mainData.themeColors.colors || [];

        return {
            colors : new Collection(colors.slice(0, MAX_THEME_COLORS)),
            range  : Upfront.mainData.themeColors.range || 0
        };

    });
}());