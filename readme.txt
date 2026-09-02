=== UpFront Framework ===
Contributors: PSOURCE
Theme URI: https://github.com/Power-Source/upfront
Tags: classicpress-theme, classicpress, theme, framework, awesome
Requires at least: 4.9
Tested up to: 7.1
ClassicPress: 2.7.2
Stable tag: 1.2.4
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Eine benutzerfreundliche, extrem vielseitige und unendlich anpassbare Drag-and-Drop-ClassicPress-Designplattform.

== Description ==

Grenzenlose Theme-Möglichkeiten
Stelle Dir vor, Du öffnest Photoshop, entwirfst Dein Layout, klickst auf Speichern und das war's – es ist online und funktioniert.

= Das ist UpFront! / This is UpFront! =

Installiere ein Upfront-Starter-Design, passe es nach Deinen Wünschen an oder erstelle es komplett neu und sieh Dir Deine Änderungen in Echtzeit an. 
Es ist nicht nur ein Page Builder, der durch Boxen, Schieberegler und Einstellräder begrenzt ist, sondern jedes Element in beliebigem Maße mit vollständiger Designkontrolle modifiziert.

= Die Tools von Upfront erleichtern die Anpassung =

Upfront verwendet Elemente und Regionen, um Dir maximale Flexibilität beim Erstellen Deiner Webseite zu bieten. 
Es ist anders als jedes ClassicPress-Theme, das Du jemals verwendet hast. Jedes Element ist für feinkörnige Anpassungen ausgelegt, von integrierten Optionen bis hin zu benutzerdefinierten Stilen.

= Flexibles Styling =

Steuere einzelne Elemente UND verwalte den Stil global auf Deiner gesamten Webseite. 
Füge Designfarben hinzu, pass Typografie an und bearbeite globale Einstellungen ohne eine einzige Codezeile.

== POWERMODUS: UpFront Builder ==



== ChangeLog ==

= 1.2.4 =
* Fix: Virtuelle Builder-Seiten laden WP_Screen im Frontend wieder versionskompatibel und verursachen keinen PHP-Fatal-Error mehr
* Fix: WordPress-Backbone kollidiert bei verzögerter Skriptausführung nicht mehr mit RequireJS und vertauscht keine Editor-Module mehr

= 1.2.3 =
* Neu: CodePen-Styleguides enthalten einen versionierten Upfront-Datensatz und lassen sich über ihre öffentliche Pen-URL prüfen
* Neu: Aktiver Theme-Style und zu importierender CodePen-Style werden mit Farben, Schriften und Typografie direkt verglichen
* Neu: Geprüfte Farben, Schriften und Typografie lassen sich einzeln auswählen und bewusst in das aktive Upfront-Theme übernehmen
* Neu: CodePens werden vor dem Import als gerenderte HTML-, CSS- und JavaScript-Vorschau angezeigt
* Neu: Eine Galerie für hilfreiche CodePens ergänzt Links zur externen Collection und zur Einreichung eigener Pens

= 1.2.2 =
* Incomplete string escaping or encoding an mehreren Stellen behoben
* Fix: Von Plugins in Archiv-Queries injizierte virtuelle Seiten werden wieder als Einzelseiten erkannt und mit ihrem tatsächlichen Inhalt gerendert
* Fix: PS-Jobboard-Expertenseiten verwenden wieder das passende Single-Layout statt der generischen Beitragsliste
* Fix: URL-Felder in Bild-Linkmasken behalten den Eingabefokus und die Zielauswahl zeigt ihre Beschriftungen vollständig an
* Fix: Fußleisten, Suchfelder und Seitennavigationen in Auswahl- und Medien-Popups werden wieder vollständig dargestellt
* Fix: Aktive Untertabs werden beim Öffnen von Sidebar-Akkordeons sofort mit ihrem Inhalt dargestellt
* Verbesserung: Farbwähler bleiben vollständig geöffnet und globale Regionen sowie Lightboxes lassen sich im Manager umbenennen oder löschen
* Modernisierung: Suche und Social verwenden die zentrale Element-Settings-Sidebar; Social nutzt lokale moderne SVG-Icons ohne externe Widgets oder Zähler-APIs

= 1.2.1 =
* Fix: Editor-Skripte werden im Frontend nur noch bei aktivem Upfront-Builder geladen
* Fix: Unnötige upfront_data-AJAX-Aufrufe und dadurch verursachte 500-Fehler auf normalen Liveseiten verhindert
* Fix: Virtuelle Editorseiten können die No-Build-Core-Abhängigkeiten ohne editmode-Parameter laden und im gewünschten Modus starten
* Fix: Der veraltete WPMUDEV-Dashboard-Fallback für Snapshot verursacht keinen PHP-Fatal-Error mehr
* Fix: Eingabefelder im Dialog zum Hinzufügen von Regionen lassen sich wieder zuverlässig fokussieren und bedienen
* Fix: Der Region-Dialog liegt über konkurrierenden Overlays und übernimmt Pointer-Ereignisse korrekt

= 1.2.0 =
* Virtuelle Seiten (zB BuddyPress Seiten) werden wieder korrekt ausgegeben

= 1.1.9 =
* Fix: Neue Menünamen lassen sich im Navigationselement wieder zuverlässig eingeben
* Fix: Eingabefelder und interaktive Steuerelemente lösen beim Bearbeiten kein Modul-Dragging mehr aus
* Fix: Textcursor und Fokusrahmen im Feld für neue Menünamen sind wieder deutlich sichtbar

= 1.1.8 =
* Fix: Add-Region-Modal im Builder ist wieder vollständig anklickbar (kein blockierendes Overlay mehr)
* Fix: Drag-/Mousedown-Konflikt im Inline-Modal behoben, sodass Inputs, Radios und Selects zuverlässig fokussiert werden
* Fix: Eingabe-Cursor (Caret) in Textfeldern des Add-Region-Dialogs blinkt wieder sichtbar an der korrekten Position
* Fix: Textauswahl im Add-Region-Dialog bleibt trotz upfront-no-select-Regeln wieder möglich

= 1.1.7 =
* Fix: Breakpoint-Preset-Daten akzeptieren Arrays und Objekte und verhindern PHP-Fatal-Errors
* Fix: Navigation verarbeitet fehlende Breakpoint- und Preset-Daten ohne PHP-Deprecation
* Fix: RequireJS-URL-Pfade werden auch bei Root-Installationen ohne preg_quote-Deprecation verarbeitet
* Fix: Virtuelle Builder-Seiten erzeugen ohne globalen Beitrag keine Kommentar-Feed-Warnung mehr
* Modernisierung: jQuery Effects Core und Slide durch performante Vanilla-JavaScript-Animationen mit Reduced-Motion-Unterstützung ersetzt

= 1.1.6 =
* Fix: Textauswahl im Editor bleibt beim Markieren/Bearbeiten stabiler erhalten
* Fix: Textfarben-/Teilselektionsverhalten im Editor ueberarbeitet
* Fix: Google-Maps-Skripte laden ohne API-Key nicht mehr und erzeugen keine NoApiKeys-Warnung
* Fix: Themebild-Importdialog merkt ignorierte fehlende Bilder pro Layout und zeigt sie nicht erneut an
* Neu: Ace-Editor wird lokal aus dem Theme geladen (keine CDN-Abhaengigkeit mehr)
* Neu: Externe Avatare sind im Upfront-Dashboard unter Allgemein als Option schaltbar
* Fix: Avatar-Darstellung nutzt standardmaessig lokale Platzhalterbilder statt externer Gravatar-Requests
* Modernisierung: Weitere jQuery- und Backbone-Altlasten im Editor-/Element-Code bereinigt
* Modernisierung: Event-Handling in mehreren Views/Commands auf listenTo/on/off umgestellt

= 1.1.5 =
* Fix: Upfront AJAX 500 beim Verlinkungsdialog (Walker-Signatur aktualisiert)
* Fix: Floating-Region-Settings ($el-Guard in Sticky-Option)
* Neu: Social-Element wieder im Editor sichtbar und draggable
* Modernisierung: Google+ im Social-Element vollständig durch Instagram ersetzt

= 1.1.4 =
* Verbesserte Doppelklick-Interaktion für die Textbearbeitung
* Aktualisierte Titelattribute auf Deutsch


= 1.1.3 =
* Fix: Gruppierungseditor
* Fix: Speichern von neuen Regionen im Builder
* Jquery und PhP Modernisierungen
* Editor UI Anpassungen

= 1.1.2 =
* Fix: Bilder werden korrekt gespeichert
* Ladeproblem mit einigen Widgets behoben
* Mehr und besseres Deutsch

= 1.1.1 =
* Fix: Colorpicker zeigt die Picker wieder korrekt
* Mehr und besseres Deutsch

= 1.1.0 =

* Veröffentlicht unter PSOURCE