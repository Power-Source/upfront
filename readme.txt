=== UpFront Framework ===
Contributors: PSOURCE
Theme URI: https://github.com/Power-Source/upfront
Tags: classicpress-theme, classicpress, theme, framework, awesome
Requires at least: 5.0
Requires CP: 1.4
Tested up to: 7.1
ClassicPress: 2.7.2
Stable tag: 1.2.9
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

= 1.2.9 =
* Neu: Regionen und ihre Grid-Inhalte starten standardmäßig mit 100 Prozent Breite; jede Region kann Regionstyp und Grid-Breite weiterhin unabhängig überschreiben
* Fix: Fullwidth-Grids berechnen ihre Spalten aus der tatsächlich gerenderten Breite und werden nicht mehr durch feste Spalten- oder Maximalbreiten aus den Theme-Einstellungen begrenzt
* Fix: Auto-Snap, responsive Wrapper und Spacer verwenden im Fullwidth-Modus dieselbe dynamische Spaltenbreite
* Verbesserung: In den Element-Einstellungen bleibt immer nur ein Akkordeon-Panel geöffnet; ungespeicherte Feldwerte bleiben beim Panelwechsel erhalten
* Fix: Die Ausrichtung Links, Mitte oder Rechts positioniert den Burger-Button korrekt und wird nicht mehr durch den Editor-Hover überschrieben
* Verbesserung: Navigationstext verwendet standardmäßig ein gut lesbares Dunkelgrau; das Burger-Menü erhält dazu einen hellen Standardhintergrund
* Fix: Button-Hover-Animationen verwenden im Editor und im ausgelieferten Theme konsistente Standardwerte; ältere Easing-Werte und Dezimalkommas werden kompatibel normalisiert
* Fix: Link-, Farb-, Icon- und Format-Panels bewahren vollständige Textmarkierungen; bestätigte Aktionen werden auf den gesamten ausgewählten Bereich angewendet
* Fix: Fehlerhaftes älteres Text-HTML wird ohne DOMDocument-Warnungen tolerant verarbeitet
* Fix: Bildabmessungen mit nichtnumerischen Werten wie auto verursachen unter PHP 8 keinen TypeError mehr
* Tests: Die dynamische Fullwidth-Spaltenberechnung und ihre Verwendung im Grid-Editor sind durch einen fokussierten Regressionstest abgedeckt

= 1.2.8 =
* Neu: CodePen-Styles werden wieder allein über ihre öffentliche Pen-URL erkannt; ein browserseitiger Result-Transport umgeht blockierte Direktabrufe und die validierte Vorschau wird ohne fremde Warnbanner lokal dargestellt
* Fix: CodePen-Importe schreiben Farben, Schriften und Typografie in die settings.php des aktiven Child-Themes und synchronisieren nur bereits vorhandene Datenbank-Overrides
* Fix: Die Theme-Palette bleibt indexstabil auf genau zehn Slots ufc0 bis ufc9 begrenzt, bewahrt doppelte Farbwerte und zeigt keine zusätzlichen zuletzt verwendeten Farben mehr an
* Fix: Der Redactor-Linkdialog sichert markierten Text vor dem Fokuswechsel, erzeugt den Anchor direkt und bewahrt dessen href beim Schließen sowie beim Ausblenden temporärer Besuchs-Icons
* Fix: Der Textfarbwähler formatiert eine Auswahl nur einmal und reagiert dadurch im Textbearbeitungsmodus wieder zuverlässig
* Fix: Die Dashboard-Werkzeuge zum Leeren des Upfront-Caches und Zurücksetzen auf Theme-Defaults verwenden wieder die konsistente Administratorberechtigung
* Fix: Pako registriert sich als benanntes AMD-Modul und verursacht bei parallelem RequireJS-Laden keinen Mismatched-anonymous-define-Fehler mehr
* Tests: CodePen-Import, Child-Theme-Persistenz, zehn Themefarbslots, Pako-AMD und Redactor-Links sind durch fokussierte Regressionstests abgedeckt

= 1.2.7 =
* Sicherheit: HTTPS- und Same-Origin-URLs werden robust normalisiert; lokale Font-Caches ersetzen auch veraltete absolute HTTP-Upload-URLs zuverlässig
* Datenschutz: Das YouTube-Element validiert freizugebende Embed- und Vorschaubild-URLs strikt und lädt ausschließlich erlaubte HTTPS-Ressourcen von YouTube No-Cookie
* Tests: URL-Härtung und verzögertes Laden des YouTube-Elements sind durch PHP- und JavaScript-Tests abgedeckt
* Modernisierung: Die modernen Drag-, Resize-, Sortier-, Slider- und Datepicker-Adapter verwenden aktuelle jQuery-APIs und stellen Drag-Vorschau sowie Zielumriss zuverlässig dar
* Fix: Hilfe & Support bleibt direkt im Editormenü verfügbar; ältere Upfront-Builder-Versionen können das weiterhin erwartete command-help-Modul wieder laden
* Fix: Navigations- und Redactor-Editoren lösen Blur-Ereignisse jQuery-3-kompatibel aus und verarbeiten leere Editorinhalte ohne Typfehler; die Linkbox bewahrt markierten Text, zeigt keine undefined-URL mehr und berechnet ihre Breite vollständig
* Fix: Visuelle Hinweise für den unteren Elementabstand akzeptieren Backbone-Eventoptionen, ohne den Upfront-Editor mit "$el.find is not a function" abzubrechen
* Verbesserung: Tabs und Accordion-Panels besitzen wieder direkt erreichbare Plus-Schaltflächen; die Tab-Schaltfläche liegt ARIA-konform außerhalb der Tablist

= 1.2.6 =
* Modernisierung: Die verbliebenen jQuery-UI-Skripte wurden aus dem Editor-Boot entfernt und durch explizit geladene moderne Adapter ersetzt
* Fix: Datepicker-, Drag-, Sortier- und Größenänderungsfunktionen stehen durch direkte RequireJS-Abhängigkeiten zuverlässig vor ihrer ersten Verwendung bereit
* Fix: Die jeweils letzte Region erhält an ihrer Oberkante einen erreichbaren Griff zur vertikalen Größenänderung; der Griff folgt automatisch neu hinzugefügten oder entfernten Regionen
* Neu: Das YouTube-Element bietet optional eine eigene Zwei-Klick-Freigabe und ordnet sich vorrangigen YouTube-Entscheidungen von PS-DSGVO unter
* Datenschutz: YouTube-Player verwenden den No-Cookie-Endpunkt; bei aktiver Sperre werden auch Vorschaubilder erst nach Zustimmung geladen und ein Noscript-Link angeboten

= 1.2.5 =
* Fix: RequireJS ordnet anonyme AMD-Module auch bei parallelem Laden zuverlässig dem aufrufenden Skript zu; Pako und direkt verwendete Editor-Abhängigkeiten verunreinigen die Modulwarteschlange nicht mehr
* Fix: Globale Theme-Farbreferenzen wie ufc0 und ufc1 bleiben gespeichert und werden für Regionshintergründe erst bei der CSS-Ausgabe in ihre aktuellen Farbwerte aufgelöst
* Neu: Regionsinhalte können unabhängig vom Regionshintergrund explizit auf 100 Prozent Grid-Breite gestellt werden
* Neu: Webfonts werden serverseitig geladen, unter wp-content/uploads/upfront-fonts zwischengespeichert und anschließend ausschließlich same-origin mit font-display: swap ausgeliefert
* Fix: Der CSS-Editor verarbeitet ACE-Inhalte nullsicher als Text und stürzt beim Einfügen von Selektoren nicht mehr an innerText.split ab
* Fix: UI/Sprites funktioniert auch mit einem leeren ui-Ordner des aktiven Themes; erste Uploads, lokale Bildmetadaten und kontrollierte Speicherfehler werden korrekt verarbeitet
* Fix: Der en_US-Sprachkatalog ist mit den aktuellen deutschen Quellstrings synchronisiert und stellt den Builder bei englischer Website-Sprache vollständig auf Englisch dar
* Modernisierung: Slider, Größenänderung und Dateiupload verwenden Array.isArray statt des entfernten jQuery-Helfers $.isArray
* Verbesserung: Schalter in den Regionseinstellungen stehen sauber untereinander und ihre Beschriftungen werden einheitlich rechts daneben dargestellt
* Fix: Ungültige Bildeditor-CSS-Werte, fehlende Deklarationstrennung und die text-size-adjust-Kompatibilitätswarnung wurden behoben; veraltete IE-CSS-Hacks und tote Regeln wurden entfernt

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