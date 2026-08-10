Change Log
============


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
