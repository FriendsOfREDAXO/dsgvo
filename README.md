## Einleitung

### Über das Addon

Dieses Addon bietet Unterstützung bei der DSGVO-konformen Umsetzung von ein oder mehreren REDAXO-Websites. Ein Rechtsanspruch besteht jedoch nicht. Für die DSGVO-konforme Umsetzung ist die ganzheitliche Betrachtung der auf einer Website anfallenden Daten und deren Nutzung unerlässlich.

### Bugmeldungen Hilfe und Links

* Auf Github: https://github.com/alexplusde/dsgvo/issues/
* im Forum: https://www.redaxo.org/forum/
* im Slack-Channel: https://friendsofredaxo.slack.com/

## Client-Plugin

### Features (Client)

Die Client-Funktion ist für die Darstellung und die ggf. automatische Aktualisierung von Datenschutz-Textbausteinen und der Verwaltung von Tracking-Codes gedacht.

* Verwaltung von Textbausteinen für die Datenschutz-Erklärung inkl. Quellen-Nennung
* Umfangreiches Setup, das einen bei einem Teil der DSGVO geforderten Auflagen unterstützt und Teile der REDAXO-Seite prüft 
* Vorlage für die Ausgabe der Datenschutz-Erklärung
* Vorlage für die Ausgabe der Cookie-Einverständnis-Meldung
* Vorlage für die Ausgabe von Tracking-Codes und externen Inhalten, die ein Opt-Out-Cookie des Nutzers berücksichtigen.
* Cronjob zum automatischen Abruf von Texten (experimentell)
* Cronjob zum automatischen Löschen alter Datensätze (experimentell)
* Cronjob zum Löschen alter Backups (in Arbeit)
* Cronjob zum Löschen alter PHP-Mailer-Logs (in Arbeit)

### Installation

Voraussetzung für die aktuelle Version des DSGVO-Addons: REDAXO 5.5
Beim Installieren und Aktivieren des Addons werden die Tabellen für den Client und ggf. den Server angelegt.
Nach erfolgreicher Installation gibt es im Backend unter AddOns einen Eintrag "DSGVO".

### Setup

Unter dem Reiter **Setup** wird in mehreren Schritten die aktuelle Installation sowie deren Konfiguration überprüft. Alle Schritte sind optional und erfordern ggf. ein händisches eingreifen. Sie erheben nicht den Anspruch auf Vollständigkeit.

* Cronjob zur Abfrage der Datenschutz-Texte (optional)
* Modul zur Datenschutz-Erklärung
* Cronjob zum Löschen alter Datensätze
* Analyse bereits vorhandener Tracking-Codes
* Cronjob für alte REDAXO-Datenbank-Backups
* Cronjob für alte PHP-Mailer-Logs

### Texte

Unter dem Reiter **Texte** werden Text-Bausteine der Datenschutz-Erklärung sowie deren Codes verwaltet. Diese können 

* manuell hinzugefügt werden, oder
* via Cronjob seitens des Clients von einem Server abgerufen werden, oder
* via Cronjob seitens des Servers am Client aktualisiert werden.

Die einzelnen Felder sind:

* Website (Domain aus dem System oder Domain des YRewrite-Projekts, z.B. `domain.de`)
* Sprache (ISO-Sprachcode, z.B. `de`)
* Schlüssel (ein eindeutiger Schlüssel für den Dienst, der ggf. auch im Opt-Out als Cookie hinterlegt wird, z.B. `google_analytics` oder `facebook_pixel`)
* Überschrift
* Status (anzeigen / verbergen)
* Datenschutz-Text (ggf. vom Server abgerufen)
* Eigener Datenschutz-Text (kann eigenen Server-Text überschreiben)
* Quelle (ggf. notwendig für Nutzungsrechte externer Dienste, bspw. dem Datenschutz-Generator von E-Recht 24)
* Link zur Quelle (URL zur Quelle)

### Einrichtung der Datenschutz-Erklärung (Modul)

Dem Addon liegt ein generisches Modul bei, das automatisch in Abhängigkeit der gewählten REDAXO-Sprache und der gewählten Domain einen Code erzeugt. Es erzeugt ebenfalls bei den passenden Diensten einen Opt-Out-Code.

```php
$lang = rex_clang::getCurrent()->getCode();
$dsgvo_pool = rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_client WHERE status = 1 AND lang = :lang ORDER by prio',[':lang'=>$lang]);

$output = new rex_fragment();
$output->setVar("dsgvo_pool", $dsgvo_pool);
$output->setVar("lang", $lang);
$output->setVar("domain", $domain);

echo $output->parse('dsgvo-page.fragment.inc.php');
```

> **Achtung:** Um die Funktionalität zu überprüfen, bitte auf der Live-Seite in den Entwickler-Einstellungen des Browsers das korrekte Setzen des Cookies überprüfen. Der Cookie lautet: `dsgvo_[schlüssel] = -1` - der entsprechende Dienst darf dann nicht im Tracking-Code auftauchen.

### Einrichtung der Tracking-Codes

Dem Addon liegt eine generische Klasse bei, die die hinterlegten Tracking-Codes in Abhängigkeit des vom Nutzer gewählten Opt-Outs ausgibt oder die Ausgabe verhindert.

```php
$lang = rex_clang::getCurrent()->getCode();
$dsgvo_pool = rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_client WHERE status = 1 AND lang = :lang ORDER by prio',[':lang'=>$lang]);

$output = new rex_fragment();
$output->setVar("dsgvo_pool", $dsgvo_pool);
$output->setVar("lang", $lang);
$output->setVar("domain", $domain);

echo $output->parse('dsgvo-tracking.fragment.inc.php');
```

> **Achtung:** Um die Funktionalität zu überprüfen, bitte auf der Live-Seite in den Entwickler-Einstellungen des Browsers das korrekte Setzen des Cookies überprüfen. Der Cookie lautet: `dsgvo_[schlüssel] = -1` - der entsprechende Dienst darf dann nicht im Tracking-Code auftauchen.


### Einrichtung des Cookie-Einverständnis-Banners

Dem Addon liegt eine generischer Code bei, der ein minimalistisches Cookie-Einverständnis-Banner ("Cookie Consent") erzeugt. Das CSS kann angepasst werden.

```php
$lang = rex_clang::getCurrent()->getCode();
$dsgvo_pool = rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM rex_dsgvo_client WHERE status = 1 AND lang = :lang ORDER by prio',[':lang'=>$lang]);

$output = new rex_fragment();
$output->setVar("dsgvo_pool", $dsgvo_pool);
$output->setVar("lang", $lang);
$output->setVar("domain", $domain);

echo $output->parse('dsgvo-consent.fragment.inc.php');
```

> **Achtung:** Der Banner darf wichtige Elemente wie bspw. den Link zum Impressum, zur Datenschutz-Seite oder Bildquellen nicht verdecken! Für die korrekte Einbindung ist der Entwickler bzw. der Betreiber verantwortlich.

## Server-Plugin

### Features

Die Server-Funktion ist für das zentrale Verwalten und Bereitstellen der Datenschutz-Erklärung und Tracking-Codes für ein oder mehrere Projekte gedacht.

* Verwaltung von Textbausteinen für die Datenschutz-Erklärung inkl. Quellen-Nennung für mehrere Projekte
* Standalone-Client zum Einfügen in ältere Projekte (REDAXO 4, andere CMS)
* Projekte-Verwaltung für die unterschiedlich
* Cronjob zum manuellen Anstoßen der Clients (in Arbeit)
* Logfiles zur Nachverfolgung, welche Projekte wann Texte abgerufen haben (in Arbeit) 

### Texte-Server

Unter dem Reiter **Texte-Server** werden Text-Bausteine der Datenschutz-Erklärung sowie deren Codes verwaltet. Diese können 

* via Cronjob seitens des Clients von einem Server abgerufen werden, oder
* via Cronjob seitens des Servers am Client aktualisiert werden.

Die einzelnen Felder sind:

* Website (Domain aus dem System oder Domain des YRewrite-Projekts, z.B. `domain.de`)
* Sprache (ISO-Sprachcode, z.B. `de`)
* Schlüssel (ein eindeutiger Schlüssel für den Dienst, der ggf. auch im Opt-Out als Cookie hinterlegt wird, z.B. `google_analytics` oder `facebook_pixel`)
* Überschrift
* Status (anzeigen / verbergen)
* Datenschutz-Text
* Quelle (ggf. notwendig für Nutzungsrechte externer Dienste, bspw. dem Datenschutz-Generator von E-Recht 24)
* Link zur Quelle (URL zur Quelle)

### Projekte-Server

Unter dem Reiter **Projekte-Server** werden Domains verwaltet.

Die einzelnen Felder sind:

* Website (Domain aus dem System oder Domain des YRewrite-Projekts, z.B. `domain.de`)
* Sprache (ISO-Sprachcode, z.B. `de`)

Außerdem werden in Logs die Verbindungen festgehalten.

### Sync-Cronjob

Der Sync-Cronjob kann sich mit externen REDAXO-Installationen verbinden und deren Datenschutz-Texte aktualisieren. Der Abruf der Texte vom Client wird über einen API-Call umgesetzt.

### Standalone-Version

Dem DSGVO-Server-Plugin liegt ein Standalone-Client bei, um beim Server verwaltete Texte auch am Client zu aktualisieren. Es befindet sich unter `/redaxo/src/addons/dsgvo/plugins/server/standalone`