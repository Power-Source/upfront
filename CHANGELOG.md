Change Log
============

1.2.6 - 2026-09-03
-------------------------------------------------------------------------------
- Modernisierung: Die verbliebenen jQuery-UI-Skripte wurden aus dem Editor-Boot entfernt und durch explizit geladene moderne Adapter ersetzt
- Fix: Datepicker-, Drag-, Sortier- und Größenänderungsfunktionen stehen durch direkte RequireJS-Abhängigkeiten zuverlässig vor ihrer ersten Verwendung bereit
- Fix: Beim Ziehen aus der Element-Sidebar folgt wieder das Elementicon der Maus, während der Zielumriss im Layout erscheint; die modernen UI-Adapter verwenden keine veraltete jQuery.isFunction-API mehr
- Fix: Hilfe & Support im Editormenü verlinkt direkt auf die Upfront-Dokumentation von PSOURCE statt auf die nicht mehr verfügbare WPMUDEV-Dokumentation
- Fix: Die jeweils letzte Region erhält an ihrer Oberkante einen erreichbaren Griff zur vertikalen Größenänderung; der Griff folgt automatisch neu hinzugefügten oder entfernten Regionen
- Neu: Das YouTube-Element bietet optional eine eigene Zwei-Klick-Freigabe und ordnet sich vorrangigen YouTube-Entscheidungen von PS-DSGVO unter
- Datenschutz: YouTube-Player verwenden den No-Cookie-Endpunkt; bei aktiver Sperre werden auch Vorschaubilder erst nach Zustimmung geladen und ein Noscript-Link angeboten

1.2.5 - 2026-09-03
-------------------------------------------------------------------------------
- Fix: RequireJS ordnet anonyme AMD-Module auch bei parallelem Laden zuverlässig dem aufrufenden Skript zu; Pako und direkt verwendete Editor-Abhängigkeiten verunreinigen die Modulwarteschlange nicht mehr
- Fix: Globale Theme-Farbreferenzen wie ufc0 und ufc1 bleiben gespeichert und werden für Regionshintergründe erst bei der CSS-Ausgabe in ihre aktuellen Farbwerte aufgelöst
- Neu: Regionsinhalte können unabhängig vom Regionshintergrund explizit auf 100 Prozent Grid-Breite gestellt werden
- Neu: Webfonts werden serverseitig geladen, unter wp-content/uploads/upfront-fonts zwischengespeichert und anschließend ausschließlich same-origin mit font-display: swap ausgeliefert
- Fix: Der CSS-Editor verarbeitet ACE-Inhalte nullsicher als Text und stürzt beim Einfügen von Selektoren nicht mehr an innerText.split ab
- Fix: UI/Sprites funktioniert auch mit einem leeren ui-Ordner des aktiven Themes; erste Uploads, lokale Bildmetadaten und kontrollierte Speicherfehler werden korrekt verarbeitet
- Fix: Der en_US-Sprachkatalog ist mit den aktuellen deutschen Quellstrings synchronisiert und stellt den Builder bei englischer Website-Sprache vollständig auf Englisch dar
- Modernisierung: Slider, Größenänderung und Dateiupload verwenden Array.isArray statt des entfernten jQuery-Helfers $.isArray
- Verbesserung: Schalter in den Regionseinstellungen stehen sauber untereinander und ihre Beschriftungen werden einheitlich rechts daneben dargestellt
- Fix: Ungültige Bildeditor-CSS-Werte, fehlende Deklarationstrennung und die text-size-adjust-Kompatibilitätswarnung wurden behoben; veraltete IE-CSS-Hacks und tote Regeln wurden entfernt

1.2.4 - 2026-09-02
-------------------------------------------------------------------------------
- Fix: Virtuelle Builder-Seiten laden WP_Screen im Frontend wieder versionskompatibel und verursachen keinen PHP-Fatal-Error mehr
- Fix: WordPress-Backbone kollidiert bei verzögerter Skriptausführung nicht mehr mit RequireJS und vertauscht keine Editor-Module mehr

1.2.3 - 2026-09-02
-------------------------------------------------------------------------------
- Neu: CodePen-Styleguides enthalten einen versionierten Upfront-Datensatz und lassen sich über ihre öffentliche Pen-URL prüfen
- Neu: Aktiver Theme-Style und zu importierender CodePen-Style werden mit Farben, Schriften und Typografie direkt verglichen
- Neu: Geprüfte Farben, Schriften und Typografie lassen sich einzeln auswählen und bewusst in das aktive Upfront-Theme übernehmen
- Neu: CodePens werden vor dem Import als gerenderte HTML-, CSS- und JavaScript-Vorschau angezeigt
- Neu: Eine Galerie für hilfreiche CodePens ergänzt Links zur externen Collection und zur Einreichung eigener Pens

1.2.2 - 2026-09-01
-------------------------------------------------------------------------------
- Incomplete string escaping or encoding an mehreren Stellen behoben
- Fix: Von Plugins in Archiv-Queries injizierte virtuelle Seiten werden wieder als Einzelseiten erkannt und mit ihrem tatsächlichen Inhalt gerendert
- Fix: PS-Jobboard-Expertenseiten verwenden wieder das passende Single-Layout statt der generischen Beitragsliste
- Fix: URL-Felder in Bild-Linkmasken behalten den Eingabefokus und die Zielauswahl zeigt ihre Beschriftungen vollständig an
- Fix: Fußleisten, Suchfelder und Seitennavigationen in Auswahl- und Medien-Popups werden wieder vollständig dargestellt
- Fix: Aktive Untertabs werden beim Öffnen von Sidebar-Akkordeons sofort mit ihrem Inhalt dargestellt
- Verbesserung: Farbwähler bleiben vollständig geöffnet und globale Regionen sowie Lightboxes lassen sich im Manager umbenennen oder löschen
- Modernisierung: Suche und Social verwenden die zentrale Element-Settings-Sidebar; Social nutzt lokale moderne SVG-Icons ohne externe Widgets oder Zähler-APIs


1.2.1 - 2026-08-16
-------------------------------------------------------------------------------
- Fix: Editor-Skripte werden im Frontend nur noch bei aktivem Upfront-Builder geladen
- Fix: Unnötige upfront_data-AJAX-Aufrufe und dadurch verursachte 500-Fehler auf normalen Liveseiten verhindert
- Fix: Virtuelle Editorseiten können die No-Build-Core-Abhängigkeiten ohne editmode-Parameter laden und im gewünschten Modus starten
- Fix: Der veraltete WPMUDEV-Dashboard-Fallback für Snapshot verursacht keinen PHP-Fatal-Error mehr
- Fix: Eingabefelder im Dialog zum Hinzufügen von Regionen lassen sich wieder zuverlässig fokussieren und bedienen
- Fix: Der Region-Dialog liegt über konkurrierenden Overlays und übernimmt Pointer-Ereignisse korrekt

1.2.0 - 2026-08-14
-------------------------------------------------------------------------------
- Virtuelle Seiten (zB BuddyPress Seiten) werden wieder korrekt ausgegeben

1.1.9 - 2026-08-11
-------------------------------------------------------------------------------
- Fix: Neue Menünamen lassen sich im Navigationselement wieder zuverlässig eingeben
- Fix: Eingabefelder und interaktive Steuerelemente lösen beim Bearbeiten kein Modul-Dragging mehr aus
- Fix: Textcursor und Fokusrahmen im Feld für neue Menünamen sind wieder deutlich sichtbar

1.1.8 - 2026-08-09
-------------------------------------------------------------------------------
- Fix: Add-Region-Modal im Builder ist wieder vollständig anklickbar (kein blockierendes Overlay mehr)
- Fix: Drag-/Mousedown-Konflikt im Inline-Modal behoben, sodass Inputs, Radios und Selects zuverlässig fokussiert werden
- Fix: Eingabe-Cursor (Caret) in Textfeldern des Add-Region-Dialogs blinkt wieder sichtbar an der korrekten Position
- Fix: Textauswahl im Add-Region-Dialog bleibt trotz upfront-no-select-Regeln wieder möglich

1.1.7 - 2026-08-08
-------------------------------------------------------------------------------
- Fix: Breakpoint-Preset-Daten akzeptieren Arrays und Objekte und verhindern PHP-Fatal-Errors
- Fix: Navigation verarbeitet fehlende Breakpoint- und Preset-Daten ohne PHP-Deprecation
- Fix: RequireJS-URL-Pfade werden auch bei Root-Installationen ohne preg_quote-Deprecation verarbeitet
- Fix: Virtuelle Builder-Seiten erzeugen ohne globalen Beitrag keine Kommentar-Feed-Warnung mehr
- Modernisierung: jQuery Effects Core und Slide durch performante Vanilla-JavaScript-Animationen mit Reduced-Motion-Unterstützung ersetzt

1.1.6 - 2026-08-07
-------------------------------------------------------------------------------
- Fix: Textauswahl im Editor bleibt beim Markieren/Bearbeiten stabiler erhalten
- Fix: Textfarben-/Teilselektionsverhalten im Editor ueberarbeitet
- Fix: Google-Maps-Skripte laden ohne API-Key nicht mehr und erzeugen keine NoApiKeys-Warnung
- Fix: Themebild-Importdialog merkt ignorierte fehlende Bilder pro Layout und zeigt sie nicht erneut an
- Modernisierung: Weitere jQuery- und Backbone-Altlasten im Editor-/Element-Code bereinigt
- Modernisierung: Event-Handling in mehreren Views/Commands auf listenTo/on/off umgestellt
- Neu: Ace-Editor wird lokal aus dem Theme geladen (keine CDN-Abhaengigkeit mehr)
- Neu: Externe Avatare sind im Upfront-Dashboard unter Allgemein als Option schaltbar
- Fix: Avatar-Darstellung nutzt standardmaessig lokale Platzhalterbilder statt externer Gravatar-Requests

1.1.5 - 2026-08-07
-------------------------------------------------------------------------------

- Fix: Upfront AJAX 500 beim Verlinkungsdialog (Walker-Signatur aktualisiert)
- Fix: Floating-Region-Settings ($el-Guard in Sticky-Option)
- Neu: Social-Element wieder im Editor sichtbar und draggable
- Modernisierung: Google+ im Social-Element vollständig durch Instagram ersetzt

1.1.4 - 2026-08-06
-------------------------------------------------------------------------------
- Verbesserte Doppelklick-Interaktion für die Textbearbeitung
- Aktualisierte Titelattribute auf Deutsch

1.1.3 - 2026-08-06
-------------------------------------------------------------------------------
- Fix: Gruppierungseditor
- Fix: Speichern von neuen Regionen im Builder
- Jquery und PhP Modernisierungen
- Editor UI Anpassungen

1.1.2 - 2026-08-05
-------------------------------------------------------------------------------
- Fix: Bilder werden korrekt gespeichert
- Ladeproblem mit einigen Widgets behoben
- Mehr und besseres Deutsch

1.1.1 - 2026-08-05
-------------------------------------------------------------------------------
- Fix: Colorpicker zeigt die Picker wieder korrekt
- Mehr und besseres Deutsch

1.1.0 - 2026-08-02
-------------------------------------------------------------------------------
- Erste Veröffentlichung.
