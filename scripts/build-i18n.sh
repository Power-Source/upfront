#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

wp i18n make-pot . languages/upfront.pot \
	--domain=upfront \
	--exclude=node_modules,vendor \
	--skip-js \
	--path=.
wp i18n update-po languages/upfront.pot languages/upfront-en_US.po

php -r '
require_once dirname(getcwd(), 3) . "/wp-includes/pomo/po.php";
$po = new PO();
if (!$po->import_from_file("languages/upfront-en_US.po")) {
	fwrite(STDERR, "Unable to read languages/upfront-en_US.po\n");
	exit(1);
}
foreach ($po->entries as $entry) {
	if (empty($entry->translations) || $entry->translations[0] === "") {
		fwrite(STDERR, "Missing en_US translation: {$entry->singular}\n");
		exit(1);
	}
}
'

wp i18n make-mo languages/upfront-en_US.po languages/upfront-en_US.mo
