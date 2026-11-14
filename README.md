# MGD Slider für Shopware 6

**Release 1.4.0.** Das Plugin läuft in einer Test-Shopware (6.7.2.2, lokal) und in der MGD-Demo-Shopware (6.7.15.1). Bitte trotzdem noch nicht in einem echten Shop einsetzen.

## Was ist das?

Ein Slider (Bilder zum Durchblättern) für die Erlebniswelten in Shopware. Es gibt zwei Arten:

- **Klassisch:** Du legst die Slides selbst an. Jede Slide hat ein Bild. Text und Button sind freiwillig.
- **Prime:** Die Slides entstehen von allein aus den Artikeln deines Shops. Du sagst nur, wie viele (1 bis 12) und welche Quelle: zufällig oder meistverkauft.

In der kostenlosen Version darfst du **5 Slider** anlegen. Beim sechsten kommt die Meldung: "Das Limit von 5 Slidern ist erreicht." Eine Pro-Version mit unbegrenzten Slidern ist geplant. Die Lizenz-Prüfung gibt es in diesem Release noch nicht.

## Das brauchst du

- Shopware 6.7
- Zugang zum Server (Terminal), um das Plugin zu installieren

## Installation, Schritt für Schritt

1. ZIP von der Release-Seite laden (Datei `MgdSlider-1.4.0.zip`).
2. Prüfsumme prüfen (siehe unten). So weißt du, dass die Datei echt ist.
3. ZIP entpacken. Es entsteht der Ordner `MgdSlider`.
4. Ordner nach `custom/plugins/MgdSlider` im Shop kopieren.
5. Im Shop-Ordner diese Befehle nacheinander ausführen:

```
bin/console plugin:refresh
bin/console plugin:install --activate MgdSlider
bin/console assets:install
bin/console theme:compile
bin/console cache:clear
```

6. Bei einem Update später zusätzlich: `bin/console plugin:update MgdSlider`.

### Prüfsumme prüfen

Auf dem Mac oder Linux: `shasum -a 256 MgdSlider-1.4.0.zip`. Die Zahl muss gleich der Zahl in der Datei `MgdSlider-1.4.0.zip.sha256` sein.

## Slider anlegen

### Variante A: Im Admin (Erlebniswelten)

Das gebaute Admin-Paket liegt im ZIP. Du musst nichts bauen.

1. Erlebniswelt öffnen und das Element "MGD Super-Slider" einfügen.
2. Auf das Zahnrad am Element klicken (Element-Einstellungen). Dort steht "Neuen Slider anlegen".
3. Name eintragen, Art wählen (Klassisch oder Prime) und auf "Slider anlegen" klicken.
4. Bei Klassisch: Bild-Adresse (URL) pro Slide eintragen. Das Bild muss im Netz oder im Shop liegen, zum Beispiel `/media/...`.
5. Der neue Slider ist danach schon im Element ausgewählt. Erlebniswelt speichern.

### Variante B: Per Konsole

```
bin/console mgd:slider:create "Mein Prime Slider" --mode=prime --prime-source=bestseller --prime-count=6
bin/console mgd:slider:create "Mein Slider" --config-file=slider.json
bin/console mgd:slider:list
bin/console mgd:slider:delete <ID>
```

Die Datei `slider.json` enthält die Einstellungen. Beispiel:

```
{"autoplay": true, "interval": 4000, "slides": [
  {"image": "/media/bild1.jpg", "alt": "Beschreibung", "title": "Überschrift", "buttonLabel": "Mehr", "buttonUrl": "/"}
]}
```

## Barrierefreiheit

- Bedienung mit der Tastatur: Pfeil links/rechts, Pos1 und Ende. Alle Knöpfe sind mit Tab erreichbar.
- Wechselt der Slider von selbst, gibt es einen Pause-Knopf.
- Wer im System "Bewegung reduzieren" eingestellt hat, bekommt keinen automatischen Wechsel.
- Auf dem Handy gibt es Wischen statt Pfeile.
- Keine externen Schriften, kein CDN. Alles liegt im Plugin.

## Was geprüft wurde und was nicht

Geprüft in der MGD-Demo-Shopware (6.7.15.1, PHP 8.4): Installieren und aktivieren, Slider Klassisch und Prime per Konsole anlegen, Anzeige im Browser breit und schmal, Tastatur und Pause-Knopf beim Klassisch-Slider, Limit 5 (der 6. wird abgelehnt). Das Update eines schon installierten Plugins (`plugin:update`) wurde dort nicht geprüft.

Zusätzlich geprüft (lokal, Shopware 6.7.2.2 in Docker):

- Installieren und aktivieren, Tabelle wird angelegt.
- Slider Klassisch und Prime werden im Shop angezeigt (breit und schmal im Browser).
- Limit 5: der sechste wird abgelehnt (Konsole und API).
- Admin-Formular: Slider (Klassisch) anlegen und die Limit-Meldung beim 6. Slider. Prime anlegen im Formular und Speichern der Erlebniswelt habe ich nicht im Browser durchgeklickt (Prime per Konsole/API geprüft).
- Tastatur, Pause-Knopf, "Bewegung reduzieren" (letzteres mit simulierter Einstellung).
- Automatische Tests: `php tests/run.php` und `node --test tests/`.

**Nicht geprüft / noch offen:**

- Slider im Admin bearbeiten oder löschen (kommt später, bis dahin Konsole).
- Bilder aus der Shopware-Medienverwaltung auswählen (aktuell nur Bild-Adresse).
- Pro-Lizenz und Lizenz-Prüfung.
- Screenreader-Test mit echtem Gerät.

## Deinstallation

`bin/console plugin:uninstall MgdSlider` löscht auch die Slider-Daten. Willst du die Daten behalten: `bin/console plugin:uninstall MgdSlider --keep-user-data`.

## Hilfe und Fehler melden

Bitte als Issue in diesem Repository melden. Das ist keine Rechtsberatung: Die Lizenzfrage wird noch geprüft (siehe LICENSE).
