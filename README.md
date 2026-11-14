# Upfront Theme Framework

**Deutsch** | [English](README.en.md)

[![Version](https://img.shields.io/badge/Version-1.3.8-2271b1?style=flat-square)](readme.txt)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777bb4?style=flat-square&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-bis%207.1.4-21759b?style=flat-square&logo=wordpress&logoColor=white)
![ClassicPress](https://img.shields.io/badge/ClassicPress-2.7.4-03768e?style=flat-square)
[![Lizenz](https://img.shields.io/badge/Lizenz-GPL--2.0--or--later-2ea44f?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)

Mit Upfront gestaltest Du Deine Website direkt im Browser: Du kannst Bereiche anordnen, Texte und Bilder bearbeiten und das Layout für verschiedene Bildschirmgrößen anpassen.

Upfront besteht aus dem **Framework** und einem dazu passenden **Theme**, das das Design Deiner Website enthält. Der visuelle Editor ist in Upfront enthalten. Der separat erhältliche **Upfront Builder** hilft Dir zusätzlich, eigene Themes zu erstellen und zu exportieren; für die Bearbeitung eines vorhandenen Upfront-Themes brauchst Du ihn nicht.

## Voraussetzungen

- ClassicPress ab 2.7 oder WordPress ab 5.0
- PHP ab 8.0
- Ein mit Upfront kompatibles Theme, beispielsweise ein Upfront-Starter-Theme

## Installation und erster Start

1. Installiere das Upfront Framework unter **Design → Themes → Installieren**. Wenn Du eine ZIP-Datei hast, kannst Du sie dort über **Theme hochladen** auswählen.
2. Installiere auf dieselbe Weise ein kompatibles Upfront-Theme und **aktiviere dieses Theme**. Das Framework muss installiert bleiben.
3. Öffne **Einstellungen → Permalinks** und wähle eine Einstellung außer **Einfach**, zum Beispiel **Beitragsname**. Speichere die Änderungen.
4. Rufe Deine Website auf, während Du angemeldet bist, und klicke in der oberen Admin-Leiste auf **Upfront**. Der visuelle Editor öffnet sich.

Upfront und passende Themes kannst Du auch über den PSOURCE Manager installieren. Eine Übersicht findest Du auf der [Upfront-Projektseite](https://psource.eimen.net/ps-upfront-theme-framework/).

## Deine Website bearbeiten

Der Editor zeigt Deine Website zusammen mit einer Seitenleiste. Du arbeitest direkt am Layout und siehst Deine Änderungen während der Bearbeitung.

### Texte, Bilder und andere Elemente

1. Ziehe ein Element aus der Elementliste in der Seitenleiste an die gewünschte Stelle auf der Seite.
2. Klicke ein Textelement doppelt an, um den Text zu bearbeiten. Markiere Text, um ihn beispielsweise fett zu formatieren oder zu verlinken.
3. Wähle bei einem Bildelement ein Bild aus der Mediathek oder lade ein neues hoch. Über die Bedienelemente am Bild kannst Du es unter anderem zuschneiden und einen Alternativtext hinterlegen.
4. Ziehe vorhandene Elemente an eine andere Position oder ändere ihre Größe mit dem angezeigten Griff. Die Einstellungen eines Elements erreichst Du über das Zahnrad.
5. Über das rote **×** am Element kannst Du es entfernen.

Neben Text und Bildern stehen unter anderem Galerien, Slider, Menüs, Buttons, Beiträge und Kontaktformulare zur Verfügung. Welche Einstellungen Du siehst, hängt vom jeweiligen Element ab.

### Bereiche und Hintergründe

**Bereiche** oder **Regionen** gliedern die Seite, beispielsweise in Kopfbereich, Inhalt und Fußbereich. In ihnen liegen die Elemente.

1. Fahre im Editor über einen Bereich und klicke auf das **Stiftsymbol**, um ihn zu bearbeiten.
2. Über die gelben **+**-Symbole kannst Du einen weiteren Bereich einfügen. Das Zahnrad öffnet die Bereichseinstellungen, zum Beispiel für Hintergrundfarbe oder Hintergrundbild.
3. Über das rote **×** löschst Du einen Bereich. Dabei werden auch die darin enthaltenen Elemente entfernt.
4. Schließe die Bereichsbearbeitung über die dafür angezeigte Schaltfläche ab.

Kopf- und Fußbereiche können als **globale Bereiche** verwendet werden. Änderungen daran erscheinen auf allen Layouts, die denselben globalen Bereich nutzen.

### Farben und Schriften

Öffne in der Seitenleiste die **Designeinstellungen**:

- Unter **Farben** kannst Du die Theme-Farbpalette ändern. Elemente, die eine dieser Theme-Farben verwenden, übernehmen die Änderung.
- Unter **Typografie** legst Du unter anderem Schriftart, Größe und Farbe für Überschriften und Text fest. Über den Schriftarten-Manager kannst Du weitere Schriften auswählen.

Einzelne Elemente können eigene Einstellungen haben und daher von den globalen Vorgaben abweichen.

### Für Handy und Tablet anpassen

1. Öffne in der Seitenleiste den **Responsive-Modus** und aktiviere die Ansichten, die Du bearbeiten möchtest.
2. Wähle oben die gewünschte Bildschirmgröße. Prüfe, ob Texte lesbar sind und Bilder, Buttons und Menüs genügend Platz haben.
3. Passe Größe und Anordnung der Elemente an. Mit dem **Augensymbol** kannst Du Elemente oder Bereiche in der ausgewählten Ansicht ausblenden und später wieder einblenden.
4. Wenn Du ein neues Element hinzufügen oder eines löschen möchtest, wechsle zurück zur normalen Layoutansicht.
5. Speichere Deine Änderungen und prüfe das Ergebnis zusätzlich auf einem echten Smartphone oder Tablet.

### Änderungen speichern

Speichere das Layout im Editor, bevor Du ihn verlässt. Bei manchen Beitragslayouts fragt Upfront, ob eine Änderung nur für den aktuellen Beitrag oder für alle Beiträge gelten soll. Wähle bewusst die gewünschte Möglichkeit.

Seiten und Beiträge haben zusätzlich eigene Veröffentlichungsoptionen: Einen Entwurf speichern und einen Beitrag veröffentlichen ist nicht dasselbe wie das Layout zu speichern.

## Seiten und Beiträge

Über die Seitenleiste des Editors kannst Du neue Seiten und Beiträge erstellen oder vorhandene Inhalte aufrufen. Alternativ verwaltest Du sie wie gewohnt im Dashboard unter **Seiten** und **Beiträge**.

Eine Seitenvorlage bestimmt das Layout der Seite. Prüfe vor dem Ändern einer vorhandenen Vorlage, welche Inhalte und Layoutänderungen betroffen sind, und sichere Deine Website vorher. Gemeinsame globale Bereiche können auf mehreren Seiten erscheinen.

## Häufige Fragen

**Ich sehe „Upfront“ nicht in der Admin-Leiste.**

Prüfe, ob ein kompatibles Upfront-Theme aktiviert ist, Du angemeldet bist und Dein Konto die nötigen Bearbeitungsrechte hat. Prüfe außerdem unter **Einstellungen → Permalinks**, ob eine andere Einstellung als **Einfach** gewählt und gespeichert wurde.

**Meine Änderung erscheint auf mehreren Seiten. Warum?**

Möglicherweise hast Du einen globalen Bereich oder ein gemeinsames Layout bearbeitet. Änderungen an einem globalen Bereich wirken sich auf alle Layouts aus, die ihn verwenden.

**Meine Änderungen sind auf der Website nicht sichtbar.**

Prüfe zuerst, ob Du das Layout gespeichert und gegebenenfalls den Beitrag oder die Seite veröffentlicht hast. Unter **Upfront → Allgemein** gibt es die Schaltfläche **Upfront Cache leeren**. Andere Caches, etwa vom Server, einem Plugin oder einem CDN, werden dadurch nicht unbedingt geleert und müssen gegebenenfalls separat geprüft werden.

**Ein Video startet auf dem Handy nicht automatisch.**

Browser können automatisches Abspielen mit Ton blockieren. Teste das Video auf dem betroffenen Gerät und prüfe, ob es sich über die Wiedergabesteuerung starten lässt. Nicht jeder Browser unterstützt jedes Videoformat.

## Ausführliche Anleitungen

- [Grundlagen, Farben und Schriftarten](https://psource.eimen.net/wiki/upfront-dokumentation/upfront-part-1-die-basics-themefarben-und-schriftarten/)
- [Bereiche und Hintergründe](https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-2-strukturierung-deiner-webseite-mit-bereichen/)
- [Elemente und ihre Einstellungen](https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-3-layout-deiner-webseite-mit-elementen/)
- [Responsive Gestaltung](https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-6-erstellung-von-responsiven-webseiten/)
- [Seiten und Beiträge](https://psource.eimen.net/wiki/upfront-dokumentation/upfront-teil-7-arbeiten-mit-seiten-und-beitraegen/)
- [Eigene Themes mit dem Upfront Builder](https://psource.eimen.net/wiki/upfront-dokumentation/upfront-builder-dokumentation/)

## Lizenz

Upfront steht unter der [GNU General Public License v2 oder neuer](https://www.gnu.org/licenses/gpl-2.0.html).
