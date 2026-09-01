# Upfront Theme Framework

**Deutsch** | [English](README.en.md)

[![Version](https://img.shields.io/badge/Version-1.2.2-2271b1?style=flat-square)](readme.txt)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb4?style=flat-square&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-bis%207.1.0-21759b?style=flat-square&logo=wordpress&logoColor=white)
![ClassicPress](https://img.shields.io/badge/ClassicPress-2.7.1-03768e?style=flat-square)
[![Lizenz](https://img.shields.io/badge/Lizenz-GPL--2.0--or--later-2ea44f?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)

**Das Theme-Framework für individuelle ClassicPress- und WordPress-Themes.**

Upfront stellt die technische Grundlage bereit, auf der Upfront-Themes laufen: Layoutauflösung, responsive Raster, Regionen, globale Designwerte, wiederverwendbare Elemente und die Schnittstellen für Child-Themes und Plugins.

Der **Upfront Builder ist ein separates Plugin**. Mit ihm werden Themes visuell erstellt, bearbeitet und als Child-Themes exportiert. Dieses Repository enthält das Framework, das diese Themes anschließend lädt und rendert.

> **Projektseite, Dokumentation und Showcase für Upfront-Themes:**
>
> [PS UpFront Theme Framework](https://psource.eimen.net/ps-upfront-theme-framework/)

[Quellcode auf GitHub](https://github.com/Power-Source/upfront) · [Upfront-Dokumentation](https://psource.eimen.net/wiki/upfront-dokumentation/) · [PSOURCE](https://psource.eimen.net/)

## Die Upfront-Architektur

```
    Builder[Upfront Builder Plugin] -->|erstellt und exportiert| Child[Upfront Child-Theme]
    Framework[Upfront Theme Framework] -->|lädt und rendert| Child
    Extension[Eigenes Plugin] -->|registriert Elemente| Framework
    Child --> Site[Website]
    Framework --> Site
```

### Upfront Theme Framework

Das Framework in diesem Repository ist Parent-Theme und Runtime. Es übernimmt:

- die Auswahl des passenden Layouts für Seiten, Beiträge, Archive und Systemansichten,
- das responsive Raster und die Breakpoint-Logik,
- Regionen, globale Regionen, Module und Elemente,
- Theme-Farben, Typografie, Presets und Layout-Eigenschaften,
- die Ausgabe und Verwaltung lokaler Element-Assets,
- PHP-, JavaScript- und AJAX-Schnittstellen für Erweiterungen,
- die Kompatibilität zwischen Upfront-Child-Themes und der Laufzeitumgebung.

Das Framework sollte nicht projektspezifisch umgebaut werden. Eigene Gestaltung gehört in ein Child-Theme, zusätzliche Fachfunktionen gehören in Plugins.

### Upfront Child-Themes

Ein Child-Theme enthält das konkrete Design einer Website. Dazu können Layoutdefinitionen, Einstellungen, Theme-Assets und individuelle Templates gehören.

```text
mein-upfront-theme/
├── style.css
├── functions.php
├── settings.php
├── layouts/
├── styles/
└── scripts/
```

Das Framework bleibt dabei die gemeinsame technische Basis. Ein Child-Theme kann von Hand entwickelt oder mit dem separaten Upfront Builder erzeugt und exportiert werden.

### Upfront Builder Plugin

Der Builder ist das Autorenwerkzeug für Upfront-Themes. Er ergänzt das Framework um die visuelle Arbeitsoberfläche und den Theme-Export.

Mit dem Builder lassen sich unter anderem:

- Layouts und responsive Breakpoints visuell gestalten,
- Regionen und globale Regionen aufbauen,
- Elemente platzieren und konfigurieren,
- Farben, Typografie und Presets definieren,
- Seiten-, Beitrags- und Archivlayouts vorbereiten,
- fertige Designs als Upfront-Child-Theme exportieren.

Das exportierte Theme läuft anschließend auf dem Upfront Theme Framework. Der Builder muss deshalb nicht als Bestandteil des Frameworks verstanden werden.

Weitere Informationen zum Zusammenspiel, verfügbare Themes und Beispiele befinden sich auf der [Upfront-Infoseite](https://psource.eimen.net/ps-upfront-theme-framework/).

## Wie das Framework rendert

1. Upfront bestimmt aus dem Request den Inhaltstyp und die passende Layout-Spezifität.
2. Das Framework sucht das Layout im aktiven Child-Theme und in den gespeicherten Layoutdaten.
3. Das Layout wird aus Regionen, Modulen und Elementen zusammengesetzt.
4. PHP-Views erzeugen das öffentliche Markup der Elemente.
5. Das Framework lädt nur die für das Layout registrierten Styles und Skripte.
6. Theme-Einstellungen und Breakpoint-Regeln werden in die Ausgabe übernommen.

Dadurch können verschiedene Upfront-Themes dieselbe Runtime verwenden, ohne die Frameworklogik zu duplizieren.

## Mitgelieferte Elemente

Upfront bringt eine umfangreiche Elementbibliothek mit, darunter:

- Text, Bild, Galerie, Slider und YouTube,
- Navigation, Suche, Tabs und Accordion,
- Buttons, Spacer und eigener Code,
- Beiträge, Beitragsdaten und Kommentare,
- Kontaktformular, Login, Karte und Widgets,
- Social-Media- und weitere Inhaltsbausteine.

Die Implementierungen unter [`elements/`](elements/) sind gleichzeitig Referenzen für eigene Erweiterungen.

## Eigene Elemente aus Plugins anbinden

Plugins können eigene Elemente an das Upfront Framework und, sofern installiert, an das Builder-Plugin anbinden. Ein Element besteht üblicherweise aus:

- einem Loader im Plugin,
- einer PHP-View für Frontend-Markup und Standardwerte,
- einer JavaScript-Entity für Modell und Darstellung im Builder,
- optionalen lokalen Styles, Skripten, Einstellungen und AJAX-Endpunkten.

Eine mögliche Plugin-Struktur:

```text
acme-upfront-note/
├── acme-upfront-note.php
├── lib/
│   └── class-acme-upfront-note-view.php
└── assets/
    ├── css/note.css
    └── js/note.js
```

### Plugin-Loader

Das Plugin registriert sich am Framework-Hook `upfront-core-initialized`:

```php
<?php
/**
 * Plugin Name: ACME Upfront Note
 */

if (!defined('ABSPATH')) exit;

add_action('upfront-core-initialized', 'acme_upfront_note_register');

function acme_upfront_note_register() {
	if (!function_exists('upfront_add_layout_editor_entity')) return;

	require_once plugin_dir_path(__FILE__) . 'lib/class-acme-upfront-note-view.php';

	upfront_add_layout_editor_entity(
		'acme-note',
		plugins_url('assets/js/note', __FILE__)
	);

	add_action(
		'wp_enqueue_scripts',
		array('Acme_Upfront_Note_View', 'add_assets')
	);
}
```

Die JavaScript-URL wird ohne `.js` registriert, da die Entity über RequireJS geladen wird. Der Slug sollte innerhalb der gesamten Installation eindeutig sein.

### PHP-View

Die PHP-View beschreibt das Element für die Framework-Runtime:

```php
<?php

class Acme_Upfront_Note_View extends Upfront_Object {

	public static function default_properties() {
		return array(
			'type' => 'AcmeNoteModel',
			'view_class' => 'Acme_Upfront_Note_View',
			'class' => 'c24 acme-upfront-note',
			'has_settings' => 0,
			'id_slug' => 'acme-note',
			'content' => __('Hinweistext', 'acme-upfront-note'),
		);
	}

	public function get_markup() {
		$properties = array();
		foreach ($this->_data['properties'] as $property) {
			if (!isset($property['name'], $property['value'])) continue;
			$properties[$property['name']] = $property['value'];
		}

		$content = isset($properties['content']) ? $properties['content'] : '';
		return '<aside class="acme-note">' . esc_html($content) . '</aside>';
	}

	public static function add_assets() {
		wp_enqueue_style(
			'acme-upfront-note',
			plugins_url('../assets/css/note.css', __FILE__),
			array(),
			'1.0.0'
		);
	}
}
```

Elementdaten müssen bei der Ausgabe passend zum Kontext escaped werden. Assets sollten lokal im Plugin liegen; eine CDN-Abhängigkeit ist nicht erforderlich.

### JavaScript-Entity für den Builder

Die Builder-Entity verwendet das Upfront-Modell und registriert den gleichen Typ wie die PHP-View:

```javascript
(function($) {
	define([], function() {
		var AcmeNoteModel = Upfront.Models.ObjectModel.extend({
			init: function() {
				this.init_property('type', 'AcmeNoteModel');
				this.init_property('view_class', 'Acme_Upfront_Note_View');
				this.init_property('element_id', Upfront.Util.get_unique_id('acme-note'));
				this.init_property('class', 'c24 acme-upfront-note');
				this.init_property('has_settings', 0);
				this.init_property('id_slug', 'acme-note');
				this.init_property('content', 'Hinweistext');
			}
		});

		var AcmeNoteView = Upfront.Views.ObjectView.extend({
			model: AcmeNoteModel,
			get_content_markup: function() {
				return $('<div>').text(
					this.model.get_property_value_by_name('content') || ''
				).prop('outerHTML');
			}
		});

		Upfront.Application.LayoutEditor.add_object('AcmeNoteModel', {
			Model: AcmeNoteModel,
			View: AcmeNoteView
		});
	});
})(jQuery);
```

Ohne Builder bleibt die PHP-View für die Ausgabe bereits gespeicherter oder exportierter Layouts zuständig. Die JavaScript-Entity wird für die visuelle Bearbeitung und das Einfügen des Elements benötigt.

Komplexere Plugin-Elemente können zusätzlich:

- lokalisierte Texte über `upfront_l10n` bereitstellen,
- eigene Settings-Panels und Felder registrieren,
- AJAX-Aktionen mit `upfront_add_ajax()` hinzufügen,
- Presets und responsive Eigenschaften unterstützen,
- Ereignisse über `Upfront.Events` abonnieren oder auslösen,
- Frontend-Assets abhängig von der tatsächlichen Verwendung laden.

Als Referenz eignen sich kleine Core-Elemente wie Spacer und Button. Galerie, Posts und Navigation zeigen umfangreichere Daten-, Settings- und Renderingpfade.

## Installation und Verwendung

1. Das Verzeichnis `upfront` unter `wp-content/themes/` installieren.
2. Upfront als Parent-Theme für ein kompatibles Child-Theme verwenden.
3. Ein vorhandenes Upfront-Theme aktivieren oder mit dem separaten Builder-Plugin ein eigenes Theme erstellen und exportieren.
4. Zusätzliche Upfront-Elemente bei Bedarf als Plugins installieren.

Themes und Anwendungsbeispiele sind im [Upfront-Showcase](https://psource.eimen.net/ps-upfront-theme-framework/) verlinkt.

## Entwicklung am Framework

Voraussetzungen:

- Node.js 20 oder neuer
- npm
- PHP und eine lokale ClassicPress-/WordPress-Installation
- WP-CLI mit `wp i18n make-pot`

```bash
npm install
npm run build-css
npm run lint
npm run test-js
npm run test-php
npm run pot
```

| Befehl | Aufgabe |
| --- | --- |
| `npm run build-css` | Sass kompilieren und CSS-Artefakte minimieren |
| `npm run lint` | JavaScript-Syntax mit ESLint prüfen |
| `npm run test-js` | JavaScript-Tests ausführen |
| `npm run test-php` | PHP-Tests ausführen |
| `npm run pot` | `languages/upfront.pot` neu generieren |
| `npm test` | PHP- und JavaScript-Tests gemeinsam ausführen |

Produktive Framework- und Element-Assets werden lokal ausgeliefert. Änderungen an SCSS-Quellen sollten zusammen mit den erzeugten CSS-Dateien eingecheckt werden.

## Links

- [Framework-Infoseite und Theme-Showcase](https://psource.eimen.net/ps-upfront-theme-framework/)
- [Upfront-Dokumentation](https://psource.eimen.net/wiki/upfront-dokumentation/)
- [Dokumentation zum Builder-Plugin](https://psource.eimen.net/wiki/upfront-dokumentation/upfront-builder-dokumentation/)
- [GitHub-Repository](https://github.com/Power-Source/upfront)
- [PSOURCE](https://psource.eimen.net/)

## Lizenz

Upfront wird unter der [GNU General Public License v2 oder neuer](https://www.gnu.org/licenses/gpl-2.0.html) veröffentlicht.
