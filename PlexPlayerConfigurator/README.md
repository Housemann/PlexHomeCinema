# PlexPlayerConfigurator
Mit dem Modul können laufende Plex Sessions ausgelesen werden, die man dann als Player in IP-Symcon erstellen kann.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Plex Sessions auslesen um diese dann als Player in IP-Symcon erstellen zu lassen. Damit man sehen kann, was auf den einzelnen Playern gerade läuft mit einigen Details zum Inhalt. Die Player werden nur im Konfigurator aufgeführt, wenn aktiv in Plex ein Film/Serie etc. abgespielt wird.

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.5
- Plex Server mit angelegtem Webhook
- Modul PlexRecieveWebhookData (wird bei Anlage vom ersten Player automatisch hinzugefügt und muss nur konfiguriert werden)
- Modul PlexSplitter (wird bei Anlage vom ersten Player automatisch hinzugefügt und muss NICHT konfiguriert werden)

### 3. Software-Installation

* Über das Module Control folgende URL hinzufügen `https://github.com/Housemann/PlexHomeCinema`
* Webhook in Plex anlegen
* Im Modul PlexRecieveWebhookData User und Password vom in Plex hinterlegten Webhook hinzufügen

### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann das 'PlexPlayerConfigurator'-Modul mithilfe des Schnellfilters gefunden werden.  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

Überischt Konfigurator
![config1](img/config1.png?raw=true)

__Konfigurationsseite__:

Name                 | Beschreibung
-------------------- | ------------------------------------------------------------------------------------------------------------------
Plex IP-Adresse      | Ip vom Plex Server
Plex Port            | Port vom Plex Server
Plex Token           | Token vom Plex Server (nur benötigt wenn unter Einstellungen->Netzwerk->"Liste der IP-Adressen und Netzwerke, die keine Authentifizierung benötigen" nicht euer Netzwerk (z.B. 192.168.11.0/24) hinterlegt ist)
Plex Externe URL     | Eine eigene URL über die ihr euren Plex Server erreicht. Dient für die Anzeige der Bilder in der HTML Box von Extern in IP-Symcon
IP-Symcon IP-Adresse | IP Symcon IP zur Abfrage von hinerlegten Bildern
IP-Symcon Port       | IP Symcon Port