# PlexRecieveWebhookData
Das I/O Modul empfängt die Daten des WebHooks die vom Plex-Server kommen und leitet diese an den Splitter weiter.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)

### 1. Funktionsumfang

* Datenempfang vom Plex-Server und weitergabe an den Plex-Splitter

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.5
- Plex-Server mit Webhook

### 3. Software-Installation

* Über das Module Control folgende URL hinzufügen `https://github.com/Housemann/PlexHomeCinema`
* Plex Webhook hinterlegen (Kontoeinstellungen->Webhooks->Webhook hinzufügen->Webhook Adresse (http://user:pass@IP-AdresseIpSymcon:PortIpSymcon/hook/plex))

### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann das 'PlexRecieveWebhookData'-Modul mithilfe des Schnellfilters gefunden werden.  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)


Überischt Konfigurator

![config1](img/config1.png?raw=true)


__Konfigurationsseite__:

Name     | Beschreibung
-------- | ------------------
Username | Benutzername aus dem Webhook
Password | Passwort aus dem Webhook