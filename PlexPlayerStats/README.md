# PlexPlayerStats
Dieses Modul ist immer für einen Player der aus den Plex Sessions über den Konfigurator erstellet wurde. Es enhält Information zum Inhalt, der wiedergegeben wird.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)

### 1. Funktionsumfang

* Plex Player für einer Session. Es werden diverse Inhalte aus den Metadaten bei Play, Pause, Resume aktualisiert. Bei Stop werden die Daten geleert.
* Eine HTML Übersichtsbox zeigt Detailiert diese Informationen an.

Überischt Konfigurator
![config1](img/uebersicht.png?raw=true)

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.5
- Plex Server mit angelegtem Webhook
- Modul PlexRecieveWebhookData (wird bei Anlage vom ersten Player automatisch hinzugefügt und muss nur konfiguriert werden)
- Modul PlexSplitter (wird bei Anlage vom ersten Player automatisch hinzugefügt und muss NICHT konfiguriert werden)

### 3. Software-Installation

* Über das Module Control folgende URL hinzufügen `https://github.com/Housemann/PlexHomeCinema`
* Webhook in Plex anlegen

### 4. Einrichten der Instanzen in IP-Symcon

 Über den Konfigurator aktive Plex Sessions auslesen und als Player erstellen lassen.
 Im I/O Modul PlexRecieveWebhookData einmalig User und Password vom im Plex hinterlegten Webhook hinzufügen.
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name                 | Beschreibung
-------------------- | ------------------
Player UUID          | Eindeutiger PlayerId aus dem Plexserver (nicht ändern)
Player               | Playername wie er aus Plex kommt
Player Platform      | Auf welchen System sich der Player vefindet

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

#### Statusvariablen

Name                        | Typ       | Beschreibung
--------------------------- | ----------| ------------
Event                       | Integer   | Play, Pause, Resume, Scrobble, Stop, Rate
Titel                       | String    | Film = Filmtitel / Serien = Serientitel / Musik = Interpret
Staffel/Album               | String    | Film = leer / Serie = Staffel / Musik = Albumname
Folge/Title                 | String    | Film = leer / Serie = Episodenname / Musik = Songname
Bibliotheksname             | String    | Name der Plex-Mediathek
Bibliotheks ID              | String    | ID der Plex-Mediathek
Studio                      | String    | Filmstudio
Verfügbar ab                | String    | Wann verfügbar war
Zusammenfassung             | String    | Zusammenfassung / Beschreibung des Films/Serie
Bewertung                   | String    | Bewertung Rotten oder Imdb
Bewertungsbild              | String    | Bewertungsbild Rotten oder Imdb
Publikumsbewertung          | String    | Bewertung Rotten oder Imdb
Publikumsbewertungbild      | String    | Bewertungsbild Rotten oder Imdb
Altersfreigabe              | String    | Altersfreigabe
Cover                       | String    | Cover Film / Serie (Bild wird gecached) | wenn gestoppt wird ein Ersatz-Cover hinterlegt
Cover Staffel/Album         | String    | Cover Staffel / Album (Bild wird gecached) | wenn gestoppt wird ein Ersatz-Cover hinterlegt
Fanart                      | String    | Fanart Cover (Bild wird gecached) | wenn gestoppt wird ein Ersatz-Cover hinterlegt
Video-Format                | String    | Videoformat
Ton-Format                  | String    | Tonformat
Bildseitenverhältnis        | String    | Seitenverhältnis
Gesamtlaufzeit              | String    | Gesamtlaufzeit
Regisseur                   | String    | Regisseur
Produzent                   | String    | Produzent
Autor                       | String    | Autor
Staffel Folge               | String    | S01E05
Jahr                        | String    | Aus welchen Jahr der Film oder die Serie stammt
Rollen                      | String    | HTML Box mit den Schauspielern
Übersicht                   | String    | HTML Box mit übersicht zum gerade laufendem Inhalt

#### Profile

Name          | Typ
------------- | ------------------------------------------
PLEX.Event    | Plex Event (Play, Pause, Stop, Resume)
