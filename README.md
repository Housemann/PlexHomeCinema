# PlexHomeCinema

Folgende Module beinhaltet das PlexHomeCinema Repository:

- __PlexPlayerConfigurator__ ([Dokumentation](PlexPlayerConfigurator))  
	Zum auslesen der Plex Sessions und zum erstellen der Player. Wenn der erste Player erstellt wird, wird automatisch PlexRecieveWebhookData und PlexSplitter mit installiert.

- __PlexRecieveWebhookData__ ([Dokumentation](PlexRecieveWebhookData))  
	Empfang der Webhook Daten aus dem Plex Server.

- __PlexSplitter__ ([Dokumentation](PlexSplitter))  
	Zum weiterleiten der Webhhok Daten an die Player.

- __PlexPlayerStats__ ([Dokumentation](PlexPlayerStats))  
	Player mit Inhalten der Plex Metadaten die aus dem Webhook und den Sessions kommen.