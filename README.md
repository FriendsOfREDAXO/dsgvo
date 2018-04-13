## Einleitung

### Über das Addon

Dieses Addon bietet Unterstützung bei der DSGVO-konformen Umsetzung von ein oder mehreren REDAXO-Websites. Ein Rechtsanspruch besteht jedoch nicht. Für die DSGVO-konforme Umsetzung ist die ganzheitliche Betrachtung der auf einer Website anfallenden Daten und deren Nutzung unerlässlich.

### Features (Client)

Die Client-Funktion ist für die Darstellung und die ggf. automatische Aktualisierung von Datenschutz-Textbausteinen und der Verwaltung von Tracking-Codes gedacht.

* Verwaltung von Textbausteinen für die Datenschutz-Erklärung inkl. Quellen-Nennung
* Umfangreiches Setup, das einen bei einem Teil der DSGVO geforderten Auflagen unterstützt und Teile der REDAXO-Seite prüft 
* Vorlage für die Ausgabe der Datenschutz-Erklärung
* Vorlage für die Ausgabe der Cookie-Einverständnis-Meldung
* Vorlage für die Ausgabe von Tracking-Codes und externen Inhalten, die ein Opt-Out-Cookie des Nutzers berücksichtigen.
* Cronjob zum automatischen Abruf von Texten
* Cronjob zum automatischen Löschen alter Datensätze 
* Cronjob zum Löschen alter Backups (in Arbeit)
* Cronjob zum Löschen alter PHP-Mailer-Logs (in Arbeit)

### Features (Server-Plugin)

Die Server-Funktion ist für das zentrale Verwalten und Bereitstellen der Datenschutz-Erklärung und Tracking-Codes für ein oder mehrere Projekte gedacht.

* Verwaltung von Textbausteinen für die Datenschutz-Erklärung inkl. Quellen-Nennung für mehrere Projekte
* Standalone-Client zum Einfügen in ältere Projekte (REDAXO 4, andere CMS)
* Projekte-Verwaltung für die unterschiedlich
* Cronjob zum manuellen Anstoßen der Clients


## Nutzung

### Installation

Voraussetzung für die aktuelle Version des DSGVO-Addons: REDAXO 5.5
Beim installieren und aktivieren des Addons werden die Tabellen für den Client angelegt.

### Setup

```php
<?php
class translate_url_with_sprog extends rex_yrewrite_scheme
{
    public function appendCategory($path, rex_category $cat, rex_yrewrite_domain $domain)
    {
        return $path;
    }
    public function appendArticle($path, rex_article $art, rex_yrewrite_domain $domain)
    {
        return $path . \'/\' . $this->normalize(sprogdown($art->getName(), $art->getClang()), $art->getClang()) . \'/\';
    }
}
```
Multilevel, Kategoriename-Ersetzung durch Sprog.
```php
<?php
class translate_url_with_sprog extends rex_yrewrite_scheme
{
    public function appendCategory($path, rex_category $cat, rex_yrewrite_domain $domain)
    {
        return $path . \'/\' . $this->normalize(sprogdown($cat->getName(), $cat->getClang()), $cat->getClang());
    }
}
```

## Standalone-Version

### Übersicht

Dem DSGVO-Server-Plugin liegt ein Standalone-Client bei, um beim Server verwaltete Texte auch in  

## Links und Hilfe

### Bugmeldungen Hilfe und Links

* Auf Github: https://github.com/yakamara/redaxo_yrewrite/issues/
* im Forum: https://www.redaxo.org/forum/
* im Slack-Channel: https://friendsofredaxo.slack.com/