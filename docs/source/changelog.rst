Changelog
=========

v1.3.9
------

* Änderungen zum Markenrelaunch von easyCredit-Ratenkauf

v1.3.8
------

* Änderungen zur Kompatibilität mit PHP 8.1 / Magento 2.4.4

v1.3.7
------

* das Ratenrechner-Modal wird bei mehrmaligem Öffnen nicht mehr dupliziert

v1.3.6
-------

* der Bestellvorgang behält seine Daten auch bei Rücksprung von der Payment Page (Browser Back-Button)
* die Darstellung der ausgewählten Zahlungsart auf der Review-Seite, in der E-Mail und im PDF wurde optimiert
* die ratenkauf by easycredit PHP-Library wurde aktualisiert auf v1.6.0
* textuelle Anpassungen

v1.3.5
------

* die Bestellnummer wird bei der Zahlungsbestätigung an die API übergeben
* eine Versandart kann für "Click & Collect" definiert werden
* es wurden fehlende Übersetzungen hinzugefügt

v1.3.4
------

* die Zinsen werden standardmäßig aus der Bestellung entfernt
* es wurde eine Einstellungsoption hinzugefügt, um die Zinsen beizubehalten
* kleinere Anpassungen im Code-Style & zur Erhöhung der Zuverlässigkeit
* die Extension hat nun eine Abhängigkeit zum Composer-Package "ratenkaufbyeasycredit/php-sdk" (wie Marktplatz-Version)

v1.3.3
------

* explizite Prüfung auf abweichende Lieferadresse in Checkout
* kleinere graphische Anpassung
* Kompatibilität mit Magento 2.4.0

v1.3.2
------

* Anpassung der API Struktur an Magento Standard zur korrekten Anzeige in Swagger
* Überarbeitung der Zahlungintegration im Checkout mit dem Ziel der Reduktion von Abhängigkeiten
* Content Security Policy wurde hinzugefügt (Magento >= v2.3.5)
* die Reihenfolge der Zahlungsmethode ist nun beeinflussbar
* Anpassungen in Übersetzungen, Angleichung an Dokumentation
* Referenzen und Abhängigkeiten zu/von Magento_PayPal wurden entfernt
* die Gestaltung des Review-Schrittes wurde leicht überarbeitet

v1.3.1
------

* die Anrede wird vor Absenden im Checkout nochmal geprüft (verhindert "es ist ein technischer Fehler bei der Integration aufgetreten")

v1.3.0
------

* Verwendung von ratenkauf by easyCredit v2
* Integration des neuen Merchant-Interfaces
* Integration von Backend-Prozessen (Rechnung, Lieferschein)
* die Zahlung wird nun von Magento als "Authorisiert" betrachtet, erst die Lieferung stellt das "Capture" dar.
* statische Code Analyse & Anpassung an Magento 2 Coding Standard
* Bugfix: die Zahlungsart beeinflusst andere Zahlungsarten nicht mehr (additional_information konditional mit easyCredit verknüpft)

v1.2.4
------

* die Anrede wird nun konditional in der Zahlartenauswahl abgefragt, wenn nicht vorhanden oder nicht valide für die Initialisierung
* der Tilgungsplan & die vorvertraglichen Informationen wurden aus der Review-Seite entfernt (bereits im Payment Terminal vorhanden)

v1.2.3
------

* Fehlerbehebung in der PDF-Rechnungsgenerierung
* der Bestellabschluss ist bei aktivierten Bestellbedingungen möglich
* das Widget ist aktualisiert auf die neueste Version (Responsive)

v1.2.2
------

* Anpassung der Betragsgrenze im Widget auf 10.000 EUR
* Kompatibilität mit PHP 7.3

v1.2.1
------
* Kompatibilität für Magento 2.3.x
* Kompatibilität mit PHP 7.2
* textuelle Anpassungen
* Integration der API Library (kein zusätzliches Package notwendig)

v1.2.0
------
* Upgrade der API auf Version 1.0
* verbessertes Fehlerhandling für Entwickler
* verbessertes Fehlerhandling für Benutzer
* Anpassung der Betragsgrenze auf einen Maximalbetrag von 5000 EUR
* API-Integration über gemeinsame PHP Library für alle Plugins
* Verbesserung der Kompatibilität
* Kompatibilität mit Magento 2.0, 2.1 und 2.2
* Kompatibilität mit Magento Marketplace
* das Ändern der Versandadresse im Backend ist nicht möglich (Fehlermeldung)
* Bootstrap Styles werden bei Widget nun zuverlässig nachgeladen, falls nicht vorhanden
* die Betragsgrenze des Widgets wurde korrigiert
* der Adresszusatz wird bei der Packstationserkennung mit einbezogen