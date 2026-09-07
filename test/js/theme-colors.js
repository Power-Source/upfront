var assert = require('assert');
var fs = require('fs'),
	path = require('path');

describe('Theme colors', function () {
	it('resolves a transparent theme color as transparent rather than inherited text color', function () {
		var source = fs.readFileSync(path.join(__dirname, '../../scripts/upfront/upfront-util.js'), 'utf8'),
			transparentMatches = source.match(/theme_colors\[[^\]]+\] === '#000000' && theme_alphas\[[^\]]+\] === 0 \? 'transparent' : theme_colors\[[^\]]+\]/g) || [];

		assert.equal(transparentMatches.length, 2);
	});

	it('limits the editor palette to ten slots without removing duplicate values', function () {
		var sourceColors = [],
			collectionColors;

		for (var index = 0; index < 12; index++) {
			sourceColors.push({ color: index === 1 ? '#000000' : '#' + index });
		}
		sourceColors[0].color = '#000000';

		global.Upfront = {
			mainData: {
				themeColors: {
					colors: sourceColors,
					range: 20
				}
			}
		};
		global.define = function (dependencies, callback) {
			function Collection(colors) {
				collectionColors = colors;
			}

			callback(Collection);
		};

		delete require.cache[require.resolve('../../scripts/upfront/upfront-views-editor/theme-colors')];
		require('../../scripts/upfront/upfront-views-editor/theme-colors');

		assert.equal(collectionColors.length, 10);
		assert.equal(collectionColors[0].color, '#000000');
		assert.equal(collectionColors[1].color, '#000000');
		assert.equal(sourceColors.length, 12);
	});
});