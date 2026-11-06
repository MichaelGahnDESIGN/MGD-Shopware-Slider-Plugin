# Änderungen

## 0.1.0 (Pre-Release)

- Läuft in Shopware 6.7.2.2 (lokal getestet): installieren, Slider Klassisch und Prime im Shop anzeigen.
- Neu: Konsolen-Befehle `mgd:slider:create`, `mgd:slider:list`, `mgd:slider:delete`.
- Neu: Admin-Formular im Erlebniswelten-Element zum Anlegen (Klassisch mit Slides, Prime mit Quelle und Anzahl). UNGETESTET, das gebaute Admin-Paket liegt nicht im ZIP (siehe README).
- Neu: Admin-API zum Löschen, Rechteprüfung für die Admin-API.
- Limit von 5 Slidern (Free) wird auf dem Server geprüft, mit deutscher Meldung. Sperre gegen gleichzeitiges Anlegen.
- CSS und JS werden pro Seite nur einmal geladen.
- Handy: feste Höhe, Text wird nicht mehr abgeschnitten. Stärkerer Verlauf unter dem Text (Lesbarkeit).
- Barrierefrei: Tastatur, Pause-Knopf, reduzierte Bewegung, ohne JS bleibt Slide 1 stehen.
- Tests: `tests/run.php` (PHP) und `tests/slider.test.js` (Node).
- Nicht getestet: Admin-Formular, MGD-Demo-Shopware, Pro-Lizenz, Bearbeiten im Admin (kommt 0.2.0).
