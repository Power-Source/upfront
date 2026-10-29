# Developer guide

This guide covers the two catalogs that are intentionally generated or updated
only while preparing a release. Neither catalog performs a discovery request
in the editor, frontend, or dashboard.

## A. Updating the font catalog

### Purpose

The bundled font catalog is stored in
[`library/models/class_upfront_model_google_fonts.php`](library/models/class_upfront_model_google_fonts.php).
It controls the families offered by the editor. Font files themselves are still
imported into the active Child Theme when a font is used.

### Prerequisites

- Node.js 20 or newer.
- Project dependencies installed with `npm ci`.
- The PSOURCE font service must expose its manifest at
  `https://eimen.net/fonts/css.php?manifest=1`.
- Internet access is required only for this update: the command also obtains
  Google Fonts metadata for readable labels, categories, and variants.

### Procedure

1. Run the update from the repository root:

   ```sh
   npm run update-font-catalog
   ```

2. Verify that the command reports the expected number of font families.
3. Review the generated change in
   [`library/models/class_upfront_model_google_fonts.php`](library/models/class_upfront_model_google_fonts.php).
   Confirm that the technical `family` value remains the font-server folder ID
   and that `label`, `category`, and `variants` are populated.
4. Run the focused checks:

   ```sh
   npx eslint tools/update-google-font-catalog.js scripts/upfront/upfront-views-editor/fonts.js
   npm run test-js -- --grep 'Font catalog'
   ```

### Alternate sources

Use these environment variables only for an intentional release-time override:

```sh
UPFRONT_FONT_MANIFEST_URL='https://fonts.example.test/css.php?manifest=1' \
UPFRONT_FONT_METADATA_URL='https://fonts.example.test/metadata/fonts' \
npm run update-font-catalog
```

Do not add these requests to runtime code. The static catalog is the source
used by deployed sites.

## B. Updating the theme catalog

### Purpose

The dashboard catalog is static so normal dashboard visits do not call the
GitHub API or download screenshots. Its data is in
[`library/data/upfront-theme-catalog.php`](library/data/upfront-theme-catalog.php);
the bundled preview images are in [`img/theme-catalog/`](img/theme-catalog/).

The catalog deliberately pins each entry to a full Git commit. GitHub is
contacted only when an administrator explicitly installs a selected theme.

### Procedure

1. Identify the approved `uf-*` repository and its release commit. Use a
   complete 40-character commit SHA, not a branch name.
2. Verify that the selected commit is a valid Upfront Child Theme:

   ```sh
   curl -fsSL "https://raw.githubusercontent.com/Power-Source/uf-example/COMMIT/style.css"
   ```

   Its stylesheet must declare `Template: upfront`.
3. Download the matching `screenshot.png` into
   `img/theme-catalog/uf-example.png`. The image is a release asset and must
   be committed with the catalog entry:

   ```sh
   curl -fsSL \
     "https://raw.githubusercontent.com/Power-Source/uf-example/COMMIT/screenshot.png" \
     -o img/theme-catalog/uf-example.png
   ```

4. Add or update the entry in
   [`library/data/upfront-theme-catalog.php`](library/data/upfront-theme-catalog.php).
   Every entry needs:

   - `slug` — the `uf-*` repository name,
   - `commit` — full, immutable 40-character SHA,
   - `name`, `version`, `author`, and `theme_uri`,
   - the local `screenshot` path,
   - `descriptions.de` and `descriptions.en`.

   Keep both descriptions concise and semantically equivalent. The dashboard
   selects English for `en_*` user locales and German as the fallback.
5. Validate the PHP syntax and that all referenced screenshots exist:

   ```sh
   php -l library/data/upfront-theme-catalog.php
   php -r '$catalog=require "library/data/upfront-theme-catalog.php"; foreach ($catalog as $theme) { if (!is_readable($theme["screenshot"])) { fwrite(STDERR, $theme["slug"] . " has no screenshot\n"); exit(1); } } echo count($catalog) . " catalog themes verified\n";'
   ```
6. On a local ClassicPress installation, open **Upfront → General** with a
   German and an English user profile. Check descriptions, screenshots, and
   the install action before release.

## Release checklist

- Update a catalog only when intentionally preparing a new version.
- Do not run catalog-generation commands on production.
- Commit generated catalog data and preview images with the release.
- Update both `CHANGELOG.md` and `CHANGELOG.en.md` when catalog behavior
  changes.
- Keep `tools/`, `test/`, Node dependencies, and package manifests out of
  production archives. `.gitattributes` already excludes them from
  `git archive`.
