<?php
declare(strict_types=1);

require_once __DIR__ . '/../libs/helper_variables.php';
	
	class PlexPlayerStats extends IPSModule
	{
		use PLEX_HelperVariables;

		public function Create()
		{
			//Never delete this line!
			parent::Create();
			
			$this->ConnectParent('{CD98E05C-AF3F-91A2-B064-49694D01215F}');

			// Propertys
			$this->RegisterPropertyString('playerUUID', '');
			$this->RegisterPropertyString('player', '');
			$this->RegisterPropertyString('playerPlatform', '');
			
			$this->RegisterPropertyString('ServerIPAddress','');
			$this->RegisterPropertyString('ServerPort', '');
			$this->RegisterPropertyString('ServerToken', '');


			#############################################################################################
			// Variablen und Profile anlegen
			#############################################################################################
			// Event
			$this->RegisterProfileIntegerEx('PLEX.Event', '', '', '', Array(
				Array(0 , 'Play'		, '', -1),
				Array(1 , 'Pause' 	, '', -1),
				Array(2 , 'Resume' 	, '', -1),
				Array(3 , 'Scrobble', '', -1),
				Array(4 , 'Stop'		, '', -1),
				Array(5 , 'Rate'		, '', -1)
			));
			$this->Variable_Register('event', 'Event', 'PLEX.Event', '', 1, false, 0);
			$this->SetValue('event',4);
			// Titel
			$this->Variable_Register('title', $this->translate('Title'), '', '', 3, false, 1);
			// Titel Staffel/Album
			$this->Variable_Register('titleSeasonAlbum', $this->translate('Season/Album'), '', '', 3, false, 2);
			// Titel Episode/Musik
			$this->Variable_Register('titleEpisodeMusic', $this->translate('Episode/Music'), '', '', 3, false, 3);
			// Mediathekname und MediathekId
			$this->Variable_Register('MediaLibraryName', $this->translate('Media library Name'), '', '', 3, false, 4);
			$this->Variable_Register('MediaLibraryId', $this->translate('Media library ID'), '', '', 3, false, 5);
			// Studio
			$this->Variable_Register('studio', $this->translate('Studio'), '', '', 3, false, 6);
			// Veröffentlichungs-Datum
			$this->Variable_Register('AvailableAt', $this->translate('Available At'), '', '', 3, false, 7);
			// Zusammenfassung
			$this->Variable_Register('summary', $this->translate('Summary'), '~HTMLBox', '', 3, false, 8);
			// Bewertung			
			$this->Variable_Register('rating', $this->translate('Rating'), '', '', 3, false, 9);
			// Altersbeschränkung
			$this->Variable_Register('contentRating', $this->translate('Content Rating'), '', '', 3, false, 10);
			// Cover
			$this->Variable_Register('cover', $this->translate('Cover'), '', '', 3, false, 11);
			// Cover Serie
			$this->Variable_Register('coverSeasonAlbum', $this->translate('Cover Season/Album'), '', '', 3, false, 12);
			// Fanart
			$this->Variable_Register('coverFanart', $this->translate('Fanart'), '', '', 3, false, 13);
			// Movie Format
			$this->Variable_Register('movieFormat', $this->translate('Movie Format'), '', '', 3, false, 14);
			// Sound Format
			$this->Variable_Register('soundFormat', $this->translate('Sound Format'), '', '', 3, false, 15);
			
		





		
		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
		}

		public function Send()
		{
			$this->SendDataToParent(json_encode(['DataID' => '{4C67B307-97FB-B9D3-3600-6305B16EBF9B}']));
		}

		public function ReceiveData($JSONString)
		{
			$data = json_decode($JSONString);
			IPS_LogMessage('Device RECV', utf8_decode($data->Buffer));
			
			$this->ReadAndProcessData(utf8_decode($data->Buffer));
		}

		private function ReadAndProcessData($data) 
		{			
			$data = json_decode(utf8_encode($data));
			
			$playerUUID 					= $data->Player->uuid;
			$event								= $data->event;
			$librarySectionType		= $data->Metadata->librarySectionType;			
			$metadata 						= $data->Metadata;			

			// Player uebereinstimmung
			if($playerUUID === $this->ReadPropertyString('playerUUID')) {
				// ZusatzInfos laden
				$ArraySessionData = $this->getPlexPlayerSessionData ($playerUUID);
				$PlayerSteamData  = json_decode(json_encode($ArraySessionData));			

				#################################################################
				// Event
				if($event === "media.play") {
					$this->SetValue('event',0);
				} elseif($event === "media.pause") {
					$this->SetValue('event',1);
				} elseif($event === "media.resume") {
					$this->SetValue('event',2);
				} elseif($event === "media.scrobble") {
					$this->SetValue('event',3);
				} elseif($event === "media.stop") {
					$this->SetValue('event',4);
				} elseif($event === "media.rate") {
					$this->SetValue('event',5);
				} 

				#################################################################
				// Titel
				if($librarySectionType == "show") {
					$title = @$metadata->grandparentTitle;
				} elseif ($librarySectionType == "movie") {
					$title = @$metadata->title;
				} elseif ($librarySectionType == "artist") {
					$title = @$metadata->grandparentTitle;
				} elseif ($librarySectionType == "photo") {
					$title = @$metadata->parentTitle;
				}
				
				if($event <> "media.stop") {
					$this->SetValue('title',$title);
				} else {
					$this->SetValue('title','');
				}				

				#################################################################
				// Titel Staffel/Album
				if($librarySectionType == "show") {
					$titleSeasonAlbum = @$metadata->parentTitle;
				} elseif ($librarySectionType == "movie") {
					$titleSeasonAlbum = '';
				} elseif ($librarySectionType == "artist") {
					$titleSeasonAlbum = @$metadata->parentTitle;
				} elseif ($librarySectionType == "photo") {
					$titleSeasonAlbum = '';
				}

				if($event <> "media.stop") {
					$this->SetValue('titleSeasonAlbum',$titleSeasonAlbum);
				} else {
					$this->SetValue('titleSeasonAlbum','');
				}			

				#################################################################
				// Titel Episode/Musik
				if($librarySectionType == "show") {
					$titleEpisodeMusic = @$metadata->title;
				} elseif ($librarySectionType == "movie") {
					$titleEpisodeMusic = '';
				} elseif ($librarySectionType == "artist") {
					$titleEpisodeMusic = @$metadata->title;
				} elseif ($librarySectionType == "photo") {
					$titleEpisodeMusic = '';
				}
				
				if($event <> "media.stop") {
					$this->SetValue('titleEpisodeMusic',$titleEpisodeMusic);
				} else {
					$this->SetValue('titleEpisodeMusic','');
				}						

				#################################################################
				// Veröffentlichungs-Datum				
				if($librarySectionType == "show") {
					$AvailableAt = @date( "d.m.Y", strtotime($metadata->originallyAvailableAt));
				} elseif ($librarySectionType == "movie") {
					$AvailableAt = @date( "d.m.Y", strtotime($metadata->originallyAvailableAt));
				} elseif ($librarySectionType == "artist") {
					$AvailableAt = '';
				} elseif ($librarySectionType == "photo") {
					$AvailableAt = '';
				}
				
				if($event <> "media.stop") {
					$this->SetValue('AvailableAt',$AvailableAt);
				} else {
					$this->SetValue('AvailableAt','');
				}	

				#################################################################				
				// Altersbeschränkung
				if($librarySectionType == "show") {
					$contentRating = @$metadata->contentRating;
				} elseif ($librarySectionType == "movie") {
					$contentRating = @$metadata->contentRating;
				} elseif ($librarySectionType == "artist") {
					$contentRating = '';
				} elseif ($librarySectionType == "photo") {
					$contentRating = '';
				}
				
				if($event <> "media.stop") {
					$this->SetValue('contentRating',$contentRating);
				} else {
					$this->SetValue('contentRating', '');
				}					

				#################################################################				
				// Mediathekname und MediathekId
				if($event <> "media.stop") {
					// Mediathekname und MediathekId
					$this->SetValue('MediaLibraryName',$metadata->librarySectionTitle);
					$this->SetValue('MediaLibraryId',$metadata->librarySectionID);									
				} else {					
					// Mediathekname und MediathekId
					$this->SetValue('MediaLibraryName','');
					$this->SetValue('MediaLibraryId','');
				}

				#################################################################				
				// Zusammenfassung
				if($event <> "media.stop") {				
					$this->SetValue('summary',$metadata->summary);					
				} else {					
					$this->SetValue('summary','');
				}

				#################################################################
				// Studio
				if($librarySectionType == "show") {
					$studio = '';
				} elseif ($librarySectionType == "movie") {
					$studio = @$metadata->studio;
				} elseif ($librarySectionType == "artist") {
					$studio = '';
				} elseif ($librarySectionType == "photo") {
					$studio = '';
				}
				
				if($event <> "media.stop") {
					$this->SetValue('studio',$studio);
				} else {
					$this->SetValue('studio','');
				}

				#################################################################
				// Bewertung
				if($event <> "media.stop") {
					if(!empty($metadata->rating)) {
						$this->SetValue('rating',strval($metadata->rating * 10).' %');
					} else {
						$this->SetValue('rating', $this->translate('no Information'));
					}
				} else {
					$this->SetValue('rating','');
				}			

				#################################################################
				// Cover
				if($librarySectionType == "show") {
					$cover = @$metadata->grandparentThumb;
				} elseif ($librarySectionType == "movie") {
					$cover = @$metadata->thumb;
				} elseif ($librarySectionType == "artist") {
					$cover = @$metadata->grandparentThumb;
				} elseif ($librarySectionType == "photo") {
					$cover = '';
				}

				// Wenn Metadaten nicht vorhanden
				if(empty($cover))
					$cover = '';		

				if($event <> "media.stop") {
					$this->SetValue('cover',$cover);
					$this->CreateMediaObject ($this->GetIDForIdent('cover'), 'Cover', $cover, $event, $Fanart=0);
				} else {
					$this->SetValue('cover','');
					$this->CreateMediaObject ($this->GetIDForIdent('cover'), 'Cover', $cover, $event, $Fanart=0);
				}
				
				#################################################################
				// Cover Serie				
				if($librarySectionType == "show") {
					$coverSeasonAlbum = @$metadata->parentThumb;
				} elseif ($librarySectionType == "movie") {
					$coverSeasonAlbum = @$metadata->parentThumb;
				} elseif ($librarySectionType == "artist") {
					$coverSeasonAlbum = @$metadata->parentThumb;
				} elseif ($librarySectionType == "photo") {
					$coverSeasonAlbum = '';
				}
				
				// Wenn Metadaten nicht vorhanden
				if(empty($coverSeasonAlbum))
					$coverSeasonAlbum = '';			

				if($event <> "media.stop") {
					$this->SetValue('coverSeasonAlbum',$coverSeasonAlbum);
					$this->CreateMediaObject ($this->GetIDForIdent('coverSeasonAlbum'), 'SeasonAlbum', $coverSeasonAlbum, $event, $Fanart=0);
				} else {
					$this->SetValue('coverSeasonAlbum','');
					$this->CreateMediaObject ($this->GetIDForIdent('coverSeasonAlbum'), 'SeasonAlbum', $coverSeasonAlbum, $event, $Fanart=0);
				}
				
				#################################################################
				// Fanart
				if($librarySectionType == "show") {
					$coverFanart = @$metadata->art;
				} elseif ($librarySectionType == "movie") {
					$coverFanart = @$metadata->art;
				} elseif ($librarySectionType == "artist") {
					$coverFanart = '';
				} elseif ($librarySectionType == "photo") {
					$coverFanart = '';
				}

				// Wenn Metadaten nicht vorhanden
				if(empty($coverFanart))
					$coverFanart = '';		
				
				if($event <> "media.stop") {
					$this->SetValue('cover',$coverFanart);
					$this->CreateMediaObject ($this->GetIDForIdent('coverFanart'), 'Fanart', $coverFanart, $event, $Fanart=1);
				} else {
					$this->SetValue('cover','');
					$this->CreateMediaObject ($this->GetIDForIdent('coverFanart'), 'Fanart', $coverFanart, $event, $Fanart=1);
				}
				
				#################################################################
				// Movie Format
				if($librarySectionType == "show") {
					$movieFormat = @$PlayerSteamData->movieFormat;
				} elseif ($librarySectionType == "movie") {
					$movieFormat = @$PlayerSteamData->movieFormat;
				} elseif ($librarySectionType == "artist") {
					$movieFormat = '';
				} elseif ($librarySectionType == "photo") {
					$movieFormat = '';
				}

				// Wenn Metadaten nicht vorhanden
				if(empty($movieFormat))
					$movieFormat = '';
				
				if($event <> "media.stop") {
					$this->SetValue('movieFormat',$movieFormat);
				} else {
					$this->SetValue('movieFormat','');
				}

				#################################################################
				// Sound Format
				if($librarySectionType == "show") {
					$soundFormat = @$PlayerSteamData->soundFormat;
				} elseif ($librarySectionType == "movie") {
					$soundFormat = @$PlayerSteamData->soundFormat;
				} elseif ($librarySectionType == "artist") {
					$soundFormat = @$PlayerSteamData->soundFormat;
				} elseif ($librarySectionType == "photo") {
					$soundFormat = '';
				}

				// Wenn Metadaten nicht vorhanden
				if(empty($soundFormat))
					$soundFormat = '';
				
				if($event <> "media.stop") {
					$this->SetValue('soundFormat',$soundFormat);
				} else {
					$this->SetValue('soundFormat','');
				}

				#################################################################

			}
		}








		// Not in Use
		private function GetMetaDataFromKey (string $key) 
		{
			// Plex Server Daten holen
			$ServerIPAddress = $this->ReadPropertyString('ServerIPAddress');
			$ServerPort 		 = $this->ReadPropertyString('ServerPort');
			$ServerToken		 = $this->ReadPropertyString('ServerToken');
			
			if(!empty($ServerToken)) {
				$url = 'http://'.$ServerIPAddress.':'.$ServerPort.$key.'?X-Plex-Token='.$ServerToken;
			} else {
				$url = 'http://'.$ServerIPAddress.':'.$ServerPort.$key;
			}
		
			$metadata = simplexml_load_file($url);
			$return = json_decode(json_encode($metadata));
			return $return;
		}

		private function getPlexPlayerSessionData (string $playerUUID) 
		{
			// Plex Server Daten holen
			$ServerIPAddress = $this->ReadPropertyString('ServerIPAddress');
			$ServerPort 		 = $this->ReadPropertyString('ServerPort');
			$ServerToken		 = $this->ReadPropertyString('ServerToken');

			if(!empty($plexToken)) {
				$url = 'http://'.$ServerIPAddress.':'.$ServerPort.'/status/sessions?X-Plex-Token='.$ServerToken;
			} else {
				$url = 'http://'.$ServerIPAddress.':'.$ServerPort.'/status/sessions';
			}

			$homepage = simplexml_load_file($url);
			$data = json_decode(json_encode($homepage),true);
		
			$librarys = array("Photo","Video","Track");
		
			$arrayPlayerData = [];
			foreach($librarys as $library) {
				if(array_key_exists($library, $data)) {
					if(array_key_exists('0', $data[$library])) {
						for($i=0; $i < count($data[$library]); $i++) {
							$plexPlayer       = $data[$library][$i]['Player']['@attributes']['machineIdentifier'];
							$arrayPlayerData[$plexPlayer] = [];
		
							if($library=='Video') {                        
								$arrayPlayerData[$plexPlayer] = [
									"movieFormat"      => $data[$library][$i]['Media']['Part']['Stream'][0]['@attributes']['displayTitle'],
									"soundFormat"        => $data[$library][$i]['Media']['Part']['Stream'][1]['@attributes']['displayTitle']              
								];
		
								// Serie Episode zusammenbauen und ins Array Pushen
								if(!empty(@$data[$library][$i]['@attributes']['parentIndex']) && !empty(@$data[$library][$i]['@attributes']['index'])) {
									$SeasonEpisode = "S".substr("0".@$data[$library][$i]['@attributes']['parentIndex'],-2,2)."E".substr("0".@$data[$library][$i]['@attributes']['index'],-2,2);            
									$arrayPlayerData[$plexPlayer] += ["SeasonEpisode" => $SeasonEpisode];            
								}    
							} elseif ($library=='Photo') {
								$arrayPlayerData[$plexPlayer] = [
									"container"       => $data[$library][$i]['Media']['Part']['@attributes']['container'],
									"file"            => $data[$library][$i]['Media']['Part']['@attributes']['file']
								];
							} elseif ($library=='Track') {
								$arrayPlayerData[$plexPlayer] = [
									"soundFormat"    => $data[$library][$i]['Media']['Part']['Stream']['@attributes']['displayTitle']
								];
							} 
						}
					} else {
						$plexPlayer = $data[$library]['Player']['@attributes']['machineIdentifier'];
						$arrayPlayerData[$plexPlayer] = [];
		
						if($library=='Video') {          
							$arrayPlayerData[$plexPlayer] = [
								"movieFormat"      => $data[$library]['Media']['Part']['Stream'][0]['@attributes']['displayTitle'],
								"soundFormat"        => $data[$library]['Media']['Part']['Stream'][1]['@attributes']['displayTitle']           
							];
		
							// Serie Episode zusammenbauen und ins Array Pushen
							if(!empty(@$data[$library]['@attributes']['parentIndex']) && !empty(@$data[$library]['@attributes']['index'])) {
								$SeasonEpisode = "S".substr("0".@$data[$library]['@attributes']['parentIndex'],-2,2)."E".substr("0".@$data[$library]['@attributes']['index'],-2,2);            
								$arrayPlayerData[$plexPlayer] += ["SeasonEpisode" => $SeasonEpisode];            
							}
						} elseif ($library=='Photo') {
								$arrayPlayerData[$plexPlayer] = [
									"container"       => $data[$library]['Media']['Part']['@attributes']['container'],
									"file"            => $data[$library]['Media']['Part']['@attributes']['file']
								];
						} elseif ($library=='Track') {
								$arrayPlayerData[$plexPlayer] = [
									"soundFormat"    => $data[$library]['Media']['Part']['Stream']['@attributes']['displayTitle']
								];
						} 
					}
					// Nur werte vom selektierten Player ausgeben
					if(array_key_exists($playerUUID,$arrayPlayerData))
						return $arrayPlayerData[$playerUUID];
				}
			}
		}

		private function CreateMediaObject (int $CatId, string $Name, string $MediaPath, string $Event, int $Fanart) 
		{		
			// Plex Server Daten holen
			$ServerIPAddress = $this->ReadPropertyString('ServerIPAddress');
			$ServerPort 		 = $this->ReadPropertyString('ServerPort');
			$ServerToken		 = $this->ReadPropertyString('ServerToken');

			// Plex URL
			if(!empty($ServerToken)) {
				$url = 'http://'.$ServerIPAddress.':'.$ServerPort.$MediaPath.'?X-Plex-Token='.$ServerToken;
			} else {
				$url = 'http://'.$ServerIPAddress.':'.$ServerPort.$MediaPath;
			}			

			// Check Folder Exists an copy clearing Files into
			if (!file_exists(IPS_GetKernelDir()."webfront/user/PlexCover")) {
				mkdir(IPS_GetKernelDir()."webfront/user/PlexCover", 0777, true);

				// Copy Files
				$TargetPath = IPS_GetKernelDir().'modules/PlexHomeCinema/img/';
				$DestPath   = IPS_GetKernelDir().'webfront/user/PlexCover/';
				$files = 'plex_cover.jpg,plex_fanart.jpg';

				foreach(explode(",",$files) as $file) {
				  if (!copy($TargetPath.$file, $DestPath.$file)) {
				    echo "copy $TargetPath schlug fehl...\n";
				  }
				}
			}
		
			// Content aus Pfad holen
			if(!empty($MediaPath) && $Event <> "media.stop") { 
				$Content = file_get_contents($url);
			} else {
				if($Fanart == 0) {
					#$Content = file_get_contents("http://".$ips."/user//PlexCover/plex_cover.jpg");
					$Content = file_get_contents(IPS_GetKernelDir()."webfront/user/PlexCover/plex_cover.jpg");
				}
				else {
					#$Content = file_get_contents("http://".$ips."/user//PlexCover/plex_fanart.jpg");
					$Content = file_get_contents(IPS_GetKernelDir()."webfront/user/PlexCover/plex_fanart.jpg");
				}				
			}
			
			// Mediaobjekt schreiben
			$MediaID = @IPS_GetMediaIDByName ( $Name, $CatId );
			if( $MediaID == false ) {
				$MediaID = IPS_CreateMedia(1);		// Image im MedienPool anlegen
				IPS_SetParent($MediaID, $CatId);	// Medienobjekt einsortieren unter Kategorie $CatId
				IPS_SetIdent ($MediaID, 'image'.$Name);
				IPS_SetPosition($MediaID, 0);
				IPS_SetMediaCached($MediaID, true);	// Das Cachen für das Mediaobjekt wird aktiviert. // Beim ersten Zugriff wird dieses von der Festplatte ausgelesen und zukünftig nur noch im Arbeitsspeicher verarbeitet.			
				$ImageFile = IPS_GetKernelDir()."webfront/user/PlexCover".DIRECTORY_SEPARATOR.$Name.".jpg";	// Image-Datei
				IPS_SetMediaFile($MediaID, $ImageFile, false);	// Image im MedienPool mit Image-Datei verbinden
				IPS_SetName($MediaID, $Name);	// Medienobjekt benennen
				IPS_SetMediaContent($MediaID, base64_encode($Content));	//Bild Base64 codieren und ablegen
				IPS_SendMediaEvent($MediaID);	//aktualisieren
			} else {
				IPS_SetMediaContent($MediaID, base64_encode($Content));  //Bild Base64 codieren und ablegen
				IPS_SendMediaEvent($MediaID); //aktualisieren
				IPS_SetMediaCached($MediaID, true);
			}
		}			

	}