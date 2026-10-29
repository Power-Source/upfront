#!/usr/bin/env node

var fs = require('fs');
var https = require('https');
var path = require('path');

var manifestUrl = process.env.UPFRONT_FONT_MANIFEST_URL || 'https://eimen.net/fonts/css.php?manifest=1';
var metadataUrl = process.env.UPFRONT_FONT_METADATA_URL || 'https://fonts.google.com/metadata/fonts';
var modelPath = path.resolve(__dirname, '../library/models/class_upfront_model_google_fonts.php');

function requestJson(url) {
	return new Promise(function(resolve, reject) {
		https.get(url, function(response) {
			var body = '';

			if (response.statusCode !== 200) {
				response.resume();
				reject(new Error('Font manifest request failed with HTTP ' + response.statusCode + '.'));
				return;
			}

			response.setEncoding('utf8');
			response.on('data', function(chunk) {
				body += chunk;
			});
			response.on('end', function() {
				try {
					resolve(JSON.parse(body));
				} catch (error) {
					reject(new Error('Font manifest did not contain valid JSON: ' + error.message));
				}
			});
		}).on('error', reject);
	});
}

function fontSlug(value) {
	return value.toLowerCase().replace(/[^a-z0-9]/g, '');
}

function getMetadataBySlug(metadata) {
	if (!metadata || !Array.isArray(metadata.familyMetadataList)) {
		throw new Error('Google Fonts metadata must contain a familyMetadataList array.');
	}

	return metadata.familyMetadataList.reduce(function(result, font) {
		if (!font || typeof font.family !== 'string' || !font.family) {
			return result;
		}

		result[fontSlug(font.family)] = {
			label: typeof font.displayName === 'string' && font.displayName ? font.displayName : font.family,
			category: typeof font.category === 'string' ? font.category.toLowerCase().replace(/\s+/g, '-') : '',
			variants: Object.keys(font.fonts || {}).map(function(variant) {
				if (variant === '400') return 'regular';
				if (variant === '400i') return 'italic';
				return variant.replace(/i$/, 'italic');
			})
		};
		return result;
	}, {});
}

function normalizeCatalog(fonts, metadata) {
	if (!Array.isArray(fonts)) {
		throw new Error('Font manifest must contain an array.');
	}

	var seen = {};
	var metadataBySlug = getMetadataBySlug(metadata);
	var catalog = fonts.reduce(function(result, font) {
		var details;
		if (!font || typeof font.family !== 'string' || !font.family) {
			throw new Error('Font manifest contains an entry without a family.');
		}

		if (seen[font.family]) {
			return result;
		}
		seen[font.family] = true;
		details = metadataBySlug[fontSlug(font.family)];

		result.push({
			family: font.family,
			label: details ? details.label : font.family,
			category: details ? details.category : (typeof font.category === 'string' ? font.category : ''),
			variants: details && details.variants.length
				? details.variants
				: (Array.isArray(font.variants) && font.variants.length ? font.variants : ['regular'])
		});

		return result;
	}, []);

	return catalog.sort(function(first, second) {
		return first.family.localeCompare(second.family);
	});
}

function updateModel(catalog) {
	var source = fs.readFileSync(modelPath, 'utf8');
	var declaration = 'private static $_cached_list = \'';
	var start = source.indexOf(declaration);
	var end;
	var serialized;

	if (start === -1) {
		throw new Error('Unable to find the bundled font catalog declaration.');
	}

	start += declaration.length;
	end = source.indexOf('\';', start);
	if (end === -1) {
		throw new Error('Unable to find the end of the bundled font catalog declaration.');
	}

	serialized = JSON.stringify(catalog).replace(/\\/g, '\\\\').replace(/'/g, '\\\'');
	source = source.slice(0, start) + serialized + source.slice(end);
	fs.writeFileSync(modelPath, source);
}

Promise.all([requestJson(manifestUrl), requestJson(metadataUrl)])
	.then(function(responses) {
		return normalizeCatalog(responses[0], responses[1]);
	})
	.then(function(catalog) {
		updateModel(catalog);
		console.log('Updated the bundled font catalog with ' + catalog.length + ' families.');
	})
	.catch(function(error) {
		console.error('Unable to update the bundled font catalog: ' + error.message);
		process.exitCode = 1;
	});
