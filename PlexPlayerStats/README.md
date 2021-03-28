# PlexPlayerStats
Dieses Modul ist immer für einen Player der aus den Plex Sessions über den Konfigurator erstellet wurde. Es enhält Information zum Inhalt, der wiedergegeben wird.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [Benachrichtigung](#6-benachrichtigung)

### 1. Funktionsumfang

* Plex Player für einer Session. Es werden diverse Inhalte aus den Metadaten bei Play, Pause, Resume aktualisiert. Bei Stop werden die Daten geleert.
* Eine HTML Übersichtsbox zeigt Detailiert diese Informationen an.
* Es kann per Skript ein eigene Benachrichtigung pro Player hinterlegt werden.

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


### 6. Benachrichtigung

Man kann ein Script hinzufügen um sich eine Benachrichtigung senden zu lassen.

Benachrichtigung 
![message1](img/message1.png?raw=true)

Folgende Variablen werden ans Skript übergeben...

Plex URL und Token
```php
$_IPS['PLEX_IpPort']	// Hinterlegte IP und Port in Form von 192.168.1.1:32400 was im Konfigurator hinterlegt wurde
$_IPS['PLEX_ExtUrl']	// was im Konfigurator hinterlegt wurde
$_IPS['PLEX_Token']		// was im Konfigurator hinterlegt wurde
```

Plex Event Typ
```php
$_IPS['PLEX_Event']		// media.play / media.pause / media.stop / media.scrobble / media.rate
```

Plex Bibliothekstyp
```php
$_IPS['PLEX_LibrarySectionType'] // movie / show / artist
```

Plex Player und User
```php
$_IPS['PLEX_Player'] 
$_IPS['PLEX_User']
```

Mediathekname
```php
$_IPS['PLEX_MediaLibraryName']
```

Film, Serien oder Künstlername  (bei Musik)
```php
$_IPS['PLEX_Title']
```

Bei Filmen wird folgendes übergeben...
```php
$_IPS['PLEX_Title']				// Filmname
$_IPS['PLEX_MovieFormat'] // Filmformat (z.B. 1080p)
$_IPS['PLEX_SoundFormat'] // Soundformat (z.B. DTS HD-MA)
$_IPS['PLEX_Duration']    // Laufzeit
$_IPS['PLEX_Cover']				// Cover in Form von /library/metadata/55183/thumb/1605439527
```

Bei Serien wird folgendes übergeben...
```php
$_IPS['PLEX_Title']							// Serienname
$_IPS['PLEX_SeasonAlbum']				// Staffelname
$_IPS['PLEX_TitleEpisodeMusic']	// Episodenname
$_IPS['PLEX_SeasonEpisode'] 		// S02E03 
$_IPS['PLEX_MovieFormat'] 			// Serienformat (z.B. 1080p)
$_IPS['PLEX_SoundFormat'] 			// Serienformat (z.B. DTS HD-MA)
$_IPS['PLEX_Duration']    			// Laufzeit
$_IPS['PLEX_CoverSeasonAlbum']	// Cover in Form von /library/metadata/55183/thumb/1605439527
```

Bei Musik wird folgendes übergeben...
```php
$_IPS['PLEX_Title']							// Interpret
$_IPS['PLEX_SeasonAlbum']				// Albumname
$_IPS['PLEX_TitleEpisodeMusic']	// Titel
$_IPS['PLEX_SoundFormat'] 			// Serienformat (z.B. DTS HD-MA)
$_IPS['PLEX_Duration']    			// Laufzeit
$_IPS['PLEX_CoverSeasonAlbum']	// Cover in Form von /library/metadata/55183/thumb/1605439527
```

Für das Cover muss die URL zusammengebaut werden von euren PlexServer in Form von...
```php
http://ip:port/library/metadata/55183/thumb/1605439527
```

#### Beispiel

Hier ist ein Beispiel zum senden der Variablen in z.B. eine HTMLBox in IP-Symcon
```php
<?php
# ------------------------------ Konfiguration ---------------------------------
# Soll Bild gecached werden
$Cached = false;
#
# wo soll Bild abgelegt werden
$TargetDirectory = IPS_GetKernelDir()."media/Plex";
#
################################################################################
# ------------------------- Ab hier nichts mehr ändern -------------------------
if($_IPS['SENDER'] == "RunScript") {
  $text = '';
  if ($_IPS['PLEX_Event'] == "media.play") {
    $PlexUrl = ReturnUrl();  
    if($_IPS['PLEX_LibrarySectionType'] == 'movie') {
      $Content = str_replace('###Cover###',$_IPS['PLEX_Cover'],$PlexUrl);  
      $MediaID = CreateMediaObject($Content,$TargetDirectory,$Cached);
      $text = $text . "\n";
      $text = $text . "<b>Player: </b>".          $_IPS['PLEX_Player']              ." \n";
      $text = $text . "<b>Benutzer: </b>".        $_IPS['PLEX_User']                ." \n";
      $text = $text . "<b>Bibliothek: </b>".      $_IPS['PLEX_MediaLibraryName']    ." \n";
      $text = $text . "<b>Filmtitel: </b>".       $_IPS['PLEX_Title']               ." \n";
      $text = $text . "<b>Video: </b>".           $_IPS['PLEX_MovieFormat']         ." \n";
      $text = $text . "<b>Ton: </b>".             $_IPS['PLEX_SoundFormat']         ." \n";
      $text = $text . "<b>Laufzeit: </b>".        $_IPS['PLEX_Duration']            ." \n";
      $text = $text . "\n";
      $text = $text . "<table><tr><td>"."<img src=".$Content." width=\"150\">"."</td></tr></table>";
      $text = $text . "\n";    
    } elseif ($_IPS['PLEX_LibrarySectionType'] == 'show') {
      $Content = str_replace('###Cover###',$_IPS['PLEX_CoverSeasonAlbum'],$PlexUrl);    
      $MediaID = CreateMediaObject($Content,$TargetDirectory,$Cached);
      $text = $text . "\n";
      $text = $text . "<b>Player: </b>".          $_IPS['PLEX_Player']              ." \n";
      $text = $text . "<b>Benutzer: </b>".        $_IPS['PLEX_User']                ." \n";
      $text = $text . "<b>Bibliothek: </b>".      $_IPS['PLEX_MediaLibraryName']    ." \n";
      $text = $text . "<b>Serientitel: </b>".     $_IPS['PLEX_Title']               ." \n";
      $text = $text . "<b>Staffel/Episode: </b>". $_IPS['PLEX_SeasonAlbum']         ." \n";
      $text = $text . "<b>Episodentitel: </b>".   $_IPS['PLEX_TitleEpisodeMusic']   ." \n";
      $text = $text . "<b>Staffel / Folge: </b>". $_IPS['PLEX_SeasonEpisode']       ." \n";
      $text = $text . "<b>Video: </b>".           $_IPS['PLEX_MovieFormat']         ." \n";
      $text = $text . "<b>Ton: </b>".             $_IPS['PLEX_SoundFormat']         ." \n";
      $text = $text . "<b>Laufzeit: </b>".        $_IPS['PLEX_Duration']            ." \n";
      $text = $text . "\n";
      $text = $text . "<table><tr><td>"."<img src=".$Content." width=\"150\">"."</td></tr></table>";
      $text = $text . "\n";
    } elseif ($_IPS['PLEX_LibrarySectionType'] == 'artist') {
      $Content = str_replace('###Cover###',$_IPS['PLEX_CoverSeasonAlbum'],$PlexUrl);
      $MediaID = CreateMediaObject($Content,$TargetDirectory,$Cached);
      $text = $text . "\n";
      $text = $text . "<b>Player: </b>".          $_IPS['PLEX_Player']              ." \n";
      $text = $text . "<b>Benutzer: </b>".        $_IPS['PLEX_User']                ." \n";
      $text = $text . "<b>Bibliothek: </b>".      $_IPS['PLEX_MediaLibraryName']    ." \n";
      $text = $text . "<b>Interpret: </b>".       $_IPS['PLEX_Title']               ." \n";
      $text = $text . "<b>Album: </b>".           $_IPS['PLEX_SeasonAlbum']         ." \n";
      $text = $text . "<b>Titel: </b>".           $_IPS['PLEX_TitleEpisodeMusic']   ." \n";
      $text = $text . "<b>Ton: </b>".             $_IPS['PLEX_SoundFormat']         ." \n";
      $text = $text . "<b>Laufzeit: </b>".        $_IPS['PLEX_Duration']            ." \n";
      $text = $text . "\n";
      $text = $text . "<table><tr><td>"."<img src=".$Content." width=\"150\">"."</td></tr></table>";
      $text = $text . "\n";    
    }
	
	SetValue(12345,$text);
  }
}


// Zusammenbauen URL wenn Token vorhanden
function ReturnUrl() 
{
  if(!empty($_IPS['PLEX_ExtUrl']) && !empty($_IPS['PLEX_Token'])) {
    $PlexUrl = $_IPS['PLEX_ExtUrl'].'###Cover###'.'?X-Plex-Token='.$_IPS['PLEX_Token'];
  } elseif(empty($_IPS['PLEX_ExtUrl']) && !empty($_IPS['PLEX_Token'])) {
    $PlexUrl = 'http://'.$_IPS['PLEX_IpPort'].'###Cover###'.'?X-Plex-Token='.$_IPS['PLEX_Token'];
  } else {
    $PlexUrl = 'http://'.$_IPS['PLEX_IpPort'].'###Cover###';
  }
  return $PlexUrl;
}

// Mediaobjekt anlegen
function CreateMediaObject($ContentUrl,$Directory,$Cached)
{
  if (!file_exists($Directory)) {
	  mkdir($Directory, 0777, true);
  }

  $CatId = $_IPS['SELF'];
  $Name = "Cover";
  $Content = file_get_contents($ContentUrl);
  $MediaID = @IPS_GetMediaIDByName ( $Name, $CatId );
    if( $MediaID == false ) {
      $MediaID = IPS_CreateMedia(1);		// Image im MedienPool anlegen
      IPS_SetParent($MediaID, $CatId);	// Medienobjekt einsortieren unter Kategorie $CatId
      IPS_SetIdent ($MediaID, 'image'.$Name);
      IPS_SetPosition($MediaID, 0);
      IPS_SetMediaCached($MediaID, $Cached);	// Das Cachen für das Mediaobjekt wird aktiviert. // Beim ersten Zugriff wird dieses von der Festplatte ausgelesen und zukünftig nur noch im Arbeitsspeicher verarbeitet.
      $ImageFile = $Directory.DIRECTORY_SEPARATOR.$Name.".jpg";	// Image-Datei
      IPS_SetMediaFile($MediaID, $ImageFile, false);	// Image im MedienPool mit Image-Datei verbinden
      IPS_SetName($MediaID, $Name);	// Medienobjekt benennen
      IPS_SetMediaContent($MediaID, base64_encode($Content));	//Bild Base64 codieren und ablegen
      IPS_SendMediaEvent($MediaID);	//aktualisieren
    } else {      
      IPS_SetMediaContent($MediaID, base64_encode($Content));  //Bild Base64 codieren und ablegen
      IPS_SendMediaEvent($MediaID); //aktualisieren
      IPS_SetMediaCached($MediaID, $Cached);
    }
    return $MediaID;
}  
```