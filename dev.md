# Entwicklung und Build

Diese Datei beschreibt den aktuellen Build-Workflow fuer das Theme.

## Voraussetzungen

- Node.js und npm installiert
- PHP/ClassicPress-Umgebung fuer lokale Tests

## Einmalige Einrichtung

```bash
npm install
```

Das installiert alle aktuellen Dev-Dependencies aus `package.json`.

## CSS Build (Standard)

```bash
npm run build-css
```

Dieser Befehl erzeugt/aktualisiert:

- `styles/editor-interface.css`
- `styles/editor-interface.css.map`
- `styles/editor-interface.min.css`
- `styles/admin.css`
- `styles/admin.css.map`
- `styles/global.min.css`
- `styles/global-rtl.min.css`
- `elements/upfront-newnavigation/css/unewnavigation-style.min.css`

## CSS Watch-Modus

```bash
npm run watch-css
```

Beobachtet die zentralen SCSS-Entry-Points und baut bei Aenderungen neu.

## Build Alias

```bash
npm run build
```

Alias fuer `npm run build-css`.

## Grunt Status

`Gruntfile.js` wurde auf i18n reduziert.

- `grunt`/default task fuehrt `makepot` aus.
- CSS wird nicht mehr ueber Grunt gebaut.

Hinweis: Die alte POT-Erstellung ueber `grunt-wp-i18n` kann unter modernen PHP-Versionen
(z. B. PHP 8.4) fehlschlagen, da dort veraltete PHP-Funktionen genutzt werden.

## Sprachdateien (POT)

Aktueller, empfohlener Weg:

```bash
wp i18n make-pot . languages/upfront.pot --slug=upfront --exclude=node_modules,vendor,build,.git,test
```

Optionaler Check auf deutsche Quellstrings in der POT:

```bash
rg -n 'msgid "Allgemeine Einstellungen"|msgid "Externe Avatare laden"' languages/upfront.pot
```

Wichtig:

- Die POT uebernimmt die Quelltexte (`msgid`) so, wie sie im Code stehen.
- In diesem Projekt sind viele Quelltexte bereits deutsch, einzelne Eintraege koennen weiterhin englisch sein, wenn sie im Code noch englisch hinterlegt sind.

## Warum diese Umstellung?

Der alte Stack mit `grunt-sass`/`node-sass` war mit modernen Node-Versionen fehleranfaellig (u. a. Python2/node-gyp Altlasten).

Der neue CSS-Workflow nutzt:

- `sass` (Dart Sass)
- `csso-cli` fuer Minifizierung

Damit ist der Build unter aktuellen Node-Versionen stabiler.

## Typische Hinweise

- Sass kann Hinweise zu veralteten Mustern ausgeben, die den Build nicht abbrechen.
- Nach CSS/JS-Aenderungen im Editor immer mit Hard Reload testen (Cache umgehen).

## Schnellstart

```bash
npm install
npm run build-css
```
