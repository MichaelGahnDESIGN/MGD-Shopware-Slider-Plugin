# Änderungen

## 1.4.0

- Neue Versionsnummer 1.4.0: Alle MGD-Produkte haben jetzt die gleiche Version.
- Slider-Kern abgeglichen: Button-Höhe korrigiert, Beschriftungen der Knöpfe lassen sich übersetzen (sonst Deutsch).
- Pro-Lizenz und Update-Kanal: gibt es noch nicht.

## 0.1.2 (Pre-Release)

- Wartungs-Release. Am Slider selbst hat sich nichts geändert.
- Mit `plugin:update` in der MGD-Demo-Shopware eingespielt. Damit ist der Update-Weg in der Demo jetzt geprüft.
- Tests: `tests/run.php` und `tests/slider.test.js` grün.

## 0.1.1 (Pre-Release)

- Das gebaute Admin-Paket ist jetzt im ZIP (0.1.0 hatte es nicht). Gebaut in einer sauberen Umgebung (Shopware 6.7.2.2).
- Admin-Formular lokal getestet: Klassisch-Slider anlegen geht, beim 6. Slider kommt die deutsche Limit-Meldung.
- Zähler "Angelegt: x / 5" im Formular korrigiert.
- Hinweistexte ohne Versionsnummer.
- In der MGD-Demo-Shopware (6.7.15.1) installiert und geprüft: Klassisch und Prime im Browser (breit und schmal), Tastatur, Pause-Knopf, Limit 5.
- Weiter offen: Bearbeiten im Admin, Pro-Lizenz, plugin:update in der Demo.

## 0.1.0 (Pre-Release)

- Läuft in Shopware 6.7.2.2 (lokal getestet): installieren, Slider Klassisch und Prime im Shop anzeigen.
- Neu: Konsolen-Befehle `mgd:slider:create`, `mgd:slider:list`, `mgd:slider:delete`.
- Neu: Admin-Formular im Erlebniswelten-Element zum Anlegen (Klassisch mit Slides, Prime mit Quelle und Anzahl). In 0.1.0 ohne gebautes Admin-Paket, ab 0.1.1 enthalten.
- Neu: Admin-API zum Löschen, Rechteprüfung für die Admin-API.
- Limit von 5 Slidern (Free) wird auf dem Server geprüft, mit deutscher Meldung. Sperre gegen gleichzeitiges Anlegen.
- CSS und JS werden pro Seite nur einmal geladen.
- Handy: feste Höhe, Text wird nicht mehr abgeschnitten. Stärkerer Verlauf unter dem Text (Lesbarkeit).
- Barrierefrei: Tastatur, Pause-Knopf, reduzierte Bewegung, ohne JS bleibt Slide 1 stehen.
- Tests: `tests/run.php` (PHP) und `tests/slider.test.js` (Node).
- Nicht getestet: Admin-Formular, MGD-Demo-Shopware, Pro-Lizenz, Bearbeiten im Admin (kommt 0.2.0).
