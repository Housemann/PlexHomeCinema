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

			$this->RegisterAttributeString('librarySectionType','');

			// IP-Symcon IP und Port
			$this->RegisterPropertyString('IpsIPAddress','');
			$this->RegisterPropertyString('IpsPort', '');

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
			$this->Variable_Register('titleSeasonAlbum', $this->translate('Season / Album'), '', '', 3, false, 2);
			// Titel Episode/Musik
			$this->Variable_Register('titleEpisodeMusic', $this->translate('Episode / Music'), '', '', 3, false, 3);
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
			$this->Variable_Register('audienceRating', $this->translate('Audience Rating'), '', '', 3, false, 9);
			// Rating and AudienceRating
			$this->Variable_Register('ratingImage', $this->translate('Rating Image'), '', '', 3, false, 9);
			$this->Variable_Register('audienceRatingImage', $this->translate('Audience Rating Image'), '', '', 3, false, 9);			
			// Altersbeschränkung
			$this->Variable_Register('contentRating', $this->translate('Content Rating'), '', '', 3, false, 10);
			// Cover
			$this->Variable_Register('cover', $this->translate('Cover'), '', '', 3, false, 11);
			// Cover Serie
			$this->Variable_Register('coverSeasonAlbum', $this->translate('Cover Season / Album'), '', '', 3, false, 12);
			// Fanart
			$this->Variable_Register('coverFanart', $this->translate('Fanart'), '', '', 3, false, 13);
			// Movie Format
			$this->Variable_Register('movieFormat', $this->translate('Movie Format'), '', '', 3, false, 14);
			// Sound Format
			$this->Variable_Register('soundFormat', $this->translate('Sound Format'), '', '', 3, false, 15);
			// Aspect Ratio
			$this->Variable_Register('aspectRatio', $this->translate('Aspect Ratio'), '', '', 3, false, 16);
			// Total Duration
			$this->Variable_Register('duration', $this->translate('Total Duration'), '', '', 3, false, 18);
			// Director
			$this->Variable_Register('director', $this->translate('Director'), '', '', 3, false, 20);
			// Producer
			$this->Variable_Register('producer', $this->translate('Producer'), '', '', 3, false, 21);
			// Writer
			$this->Variable_Register('writer', $this->translate('Writer'), '', '', 3, false, 22);	
			// SeasonEpisode
			$this->Variable_Register('seasonEpisode', $this->translate('Season Episode'), '', '', 3, false, 23);
			// Year
			$this->Variable_Register('year', $this->translate('Year'), '', '', 3, false, 24);
			// Role
			$this->Variable_Register('role', $this->translate('Role'), '~HTMLBox', '', 3, false, 25);
			
			

			


			// Overview
			$this->Variable_Register('overview', $this->translate('Overview'), '~HTMLBox', '', 3, false, 50);
		
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

			// LibryrySectionTxpe merken
			$this->WriteAttributeString('librarySectionType',$librarySectionType);

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
					$this->SetValue('coverFanart',$coverFanart);
					$this->CreateMediaObject ($this->GetIDForIdent('coverFanart'), 'Fanart', $coverFanart, $event, $Fanart=1);
				} else {
					$this->SetValue('coverFanart','');
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
				// Aspect Ratio
				if($librarySectionType == "show") {
					$aspectRatio = @$PlayerSteamData->aspectRatio;
				} elseif ($librarySectionType == "movie") {
					$aspectRatio = @$PlayerSteamData->aspectRatio;
				} elseif ($librarySectionType == "artist") {
					$aspectRatio = '';
				} elseif ($librarySectionType == "photo") {
					$aspectRatio = '';
				}

				// Wenn Metadaten nicht vorhanden
				if(empty($aspectRatio))
					$aspectRatio = '';
				
				if($event <> "media.stop") {
					$this->SetValue('aspectRatio',$aspectRatio);
				} else {
					$this->SetValue('aspectRatio','');
				}

				#################################################################
				// Duration
				if($librarySectionType == "show") {					
					$returnInt = $this->intervall(@$PlayerSteamData->duration);
					$duration = $returnInt['hms_name'];
				} elseif ($librarySectionType == "movie") {
					$returnInt = $this->intervall(@$PlayerSteamData->duration);
					$duration = $returnInt['hms_name'];
				} elseif ($librarySectionType == "artist") {
					$returnInt = $this->intervall(@$PlayerSteamData->duration);
					$duration = $returnInt['hms_name'];
				} elseif ($librarySectionType == "photo") {
					$duration = '';
				}

				// Wenn Metadaten nicht vorhanden
				if(empty($duration))
					$duration = '';
				
				if($event <> "media.stop") {
					$this->SetValue('duration',$duration);
				} else {
					$this->SetValue('duration','');
				}

				#################################################################
				// SeasonEpisode
				if($librarySectionType == "show") {					
					$seasonEpisode = @$PlayerSteamData->SeasonEpisode;					
				
					if($event <> "media.stop") {
						$this->SetValue('seasonEpisode',$seasonEpisode);
					} else {
						$this->SetValue('seasonEpisode','');
					}
				} else {
					$this->SetValue('seasonEpisode','');
				}

				#################################################################
				// Roles
				if ($librarySectionType == "movie") {
					$roleinfo = @$metadata->Role;
					$roleTable = $this->RoleFillTable ($roleinfo);
					
					if($event <> "media.stop") {
						$this->SetValue('role',$roleTable);
					} else {
						$this->SetValue('role','');
					}							
				} else {
					$this->SetValue('role','');
				}
				
				#################################################################
				// Director
				$comma_separated = '';
				if ($librarySectionType == "movie") {
					$directorinfo = @$metadata->Director;
					if(is_countable($directorinfo)) {
						foreach($directorinfo as $key => $director) {
							$array_directorinfo[] = $director->tag;
						}
						$comma_separated = implode(", ", $array_directorinfo);
					
					}
				} 
				
				if($event <> "media.stop") {
					$this->SetValue('director',$comma_separated);
				} else {
					$this->SetValue('director','');
				}

				#################################################################
				// Producer
				$comma_separated = '';
				if ($librarySectionType == "movie") {
					$producerinfo = @$metadata->Producer;
					if(is_countable($producerinfo)) {
						foreach($producerinfo as $key => $producer) {
							$array_producer[] = $producer->tag;
						}
						$comma_separated = implode(", ", $array_producer);
					}
				} 
				
				if($event <> "media.stop") {
					$this->SetValue('producer',$comma_separated);
				} else {
					$this->SetValue('producer','');
				}				
				
				#################################################################
				// Writer
				$comma_separated = '';
				if ($librarySectionType == "movie") {
					$writerinfo = @$metadata->Writer;
					if(is_countable($writerinfo)) {
						foreach($writerinfo as $key => $writer) {
							$array_writer[] = $writer->tag;
						}
						$comma_separated = implode(", ", $array_writer);
					}
				} 
				
				if($event <> "media.stop") {
					$this->SetValue('writer',$comma_separated);
				} else {
					$this->SetValue('writer','');
				}	

				#################################################################
				// year
				if($librarySectionType == "show") {					
					$year = @$metadata->year;
				} elseif ($librarySectionType == "movie") {
					$year = @$metadata->year;
				} elseif ($librarySectionType == "artist") {
					$year = '';
				} elseif ($librarySectionType == "photo") {
					$year = '';
				}
				
				if($event <> "media.stop") {
					$this->SetValue('year',$year);
				} else {
					$this->SetValue('year','');
				}

				#################################################################
				// Bewertung
				if($event <> "media.stop") {
					if(!empty($metadata->ratingImage)) {
						$ratingImage = $metadata->ratingImage;
						$ratingImage_host = substr($ratingImage,0,strpos($ratingImage,":"));						
						
						if($ratingImage_host=="rottentomatoes") {
							$ratingImage_pic  = substr($ratingImage,strripos($ratingImage,".")+1,10);
							$this->SetValue('rating',strval($metadata->rating * 10).' %');
							$this->SetValue('ratingImage',$ratingImage_host."_".$ratingImage_pic);
						} else {							
							$this->SetValue('rating','');
							$this->SetValue('ratingImage','');
						}					
					} else {
						$this->SetValue('rating','');
						$this->SetValue('ratingImage','');
					}
					if(!empty($metadata->audienceRatingImage)) {					
					$audienceRatingImage = $metadata->audienceRatingImage;
					$audienceRatingImage_host = substr($audienceRatingImage,0,strpos($audienceRatingImage,":"));
					
						if($audienceRatingImage_host=="rottentomatoes") {
							$audienceRatingImage_pic  = substr($audienceRatingImage,strripos($audienceRatingImage,".")+1,10);
							$this->SetValue('audienceRating',strval($metadata->audienceRating * 10).' %');
							$this->SetValue('audienceRatingImage',$audienceRatingImage_host."_".$audienceRatingImage_pic);
						} elseif($audienceRatingImage_host=="imdb") {
							$audienceRatingImage_pic  = substr($audienceRatingImage,strripos($audienceRatingImage,".")+1,10);
							$this->SetValue('audienceRating',strval($metadata->audienceRating));
							$this->SetValue('audienceRatingImage',$audienceRatingImage_host."_".$audienceRatingImage_pic);
						}											
					} else {
						$this->SetValue('audienceRating','');
						$this->SetValue('audienceRatingImage','');
					}
				} else {
					$this->SetValue('rating','');
					$this->SetValue('ratingImage','');
					$this->SetValue('audienceRating','');
					$this->SetValue('audienceRatingImage','');
				}

				#################################################################
				// Html Overview
				$this->GenerateHtmlOverview ();




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
									"movieFormat"      	=> @$data[$library][$i]['Media']['Part']['Stream'][0]['@attributes']['displayTitle'],
									"soundFormat"      	=> @$data[$library][$i]['Media']['Part']['Stream'][1]['@attributes']['displayTitle'],
									"aspectRatio"				=> @$data[$library][$i]['Media']['@attributes']['aspectRatio'],
									"duration"					=> @$data[$library][$i]['@attributes']['duration'] / 1000
								];
		
								// Serie Episode zusammenbauen und ins Array Pushen
								if(!empty(@$data[$library][$i]['@attributes']['parentIndex']) && !empty(@$data[$library][$i]['@attributes']['index'])) {
									$SeasonEpisode = "S".substr("0".@$data[$library][$i]['@attributes']['parentIndex'],-2,2)."E".substr("0".@$data[$library][$i]['@attributes']['index'],-2,2);            
									$arrayPlayerData[$plexPlayer] += ["SeasonEpisode" => $SeasonEpisode];            
								}    
							} elseif ($library=='Photo') {
								$arrayPlayerData[$plexPlayer] = [
									"container"       => @$data[$library][$i]['Media']['Part']['@attributes']['container'],
									"file"            => @$data[$library][$i]['Media']['Part']['@attributes']['file']
								];
							} elseif ($library=='Track') {
								$arrayPlayerData[$plexPlayer] = [
									"soundFormat"    => @$data[$library][$i]['Media']['Part']['Stream']['@attributes']['displayTitle'],
									"duration"			 => @$data[$library][$i]['@attributes']['duration'] / 1000
								];
							} 
						}
					} else {
						$plexPlayer = $data[$library]['Player']['@attributes']['machineIdentifier'];
						$arrayPlayerData[$plexPlayer] = [];
		
						if($library=='Video') {          
							$arrayPlayerData[$plexPlayer] = [
								"movieFormat"      	=> @$data[$library]['Media']['Part']['Stream'][0]['@attributes']['displayTitle'],
								"soundFormat"      	=> @$data[$library]['Media']['Part']['Stream'][1]['@attributes']['displayTitle'],
								"aspectRatio"				=> @$data[$library]['Media']['@attributes']['aspectRatio'],
								"duration"					=> @$data[$library]['@attributes']['duration'] / 1000
							];
		
							// Serie Episode zusammenbauen und ins Array Pushen
							if(!empty(@$data[$library]['@attributes']['parentIndex']) && !empty(@$data[$library]['@attributes']['index'])) {
								$SeasonEpisode = "S".substr("0".@$data[$library]['@attributes']['parentIndex'],-2,2)."E".substr("0".@$data[$library]['@attributes']['index'],-2,2);            
								$arrayPlayerData[$plexPlayer] += ["SeasonEpisode" => $SeasonEpisode];            
							}
						} elseif ($library=='Photo') {
								$arrayPlayerData[$plexPlayer] = [
									"container"       => @$data[$library]['Media']['Part']['@attributes']['container'],
									"file"            => @$data[$library]['Media']['Part']['@attributes']['file']
								];
						} elseif ($library=='Track') {
								$arrayPlayerData[$plexPlayer] = [
									"soundFormat"    => @$data[$library]['Media']['Part']['Stream']['@attributes']['displayTitle'],
									"duration"			 => @$data[$library]['@attributes']['duration'] / 1000
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
				$TargetPath = IPS_GetKernelDir().'modules/PlexHomeCinema/PlexPlayerStats/img/';
				$DestPath   = IPS_GetKernelDir().'webfront/user/PlexCover/';
				$files = 'plex_cover.jpg,plex_fanart.jpg,imdb_rating.png,rottentomatoes_ripe.png,rottentomatoes_rotten.png,rottentomatoes_spilled.png,rottentomatoes_upright.png,FSK_0.png,FSK_6.png,FSK_12.png,FSK_16.png,FSK_18.png';

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

		private function intervall($sek) 
		{
			$hms = sprintf(
				'%d:%d:%d',
				$sek / 3600 % 24,
				$sek / 60 % 60,
				$sek % 60
			);
			$ms = sprintf(			
				'%d:%d',          
				$sek / 60,		
				$sek % 60
			);
			$hms_name = sprintf(
				'%d Stunde%s,'.' %d Minute%s und %d Sekunde%s',
				$sek / 3600 % 24,
				floor($sek / 3600 % 24) != 1 ? 'n':'',
				$sek / 60 % 60,
				floor($sek / 60 % 60) != 1 ? 'n':'',
				$sek % 60,
				floor($sek % 60) != 1 ? 'n':''
			);
			$ms_name = sprintf(
				'%d Minute%s und %d Sekunde%s',
				$sek / 60,
				floor($sek / 60) != 1 ? 'n':'',
				$sek % 60,
				floor($sek % 60) != 1 ? 'n':''
			);			
			return array(
				"hms_name"=>	$hms_name,
				"hms"			=>	$hms,
				"ms_name"	=>	$ms_name,
				"ms"			=>	$ms
			);
		}

		private function RoleFillTable ($roleinfo)
		{			
			$font_size_header = "25";
			$font_size_table = "23";
			
			$s = '';
			$s = $s . "<style type='text/css'>"; 
			$s = $s . "table.test { width: 100%; border-collapse: collapse;}"; 
			$s = $s . "Test { border: 1px outset #444455; border-color:#FF0000;}</style>";
			$s = $s . "<table class='test'>"; 
	
			$s = $s . "<tr>"; 
			$s = $s . "<td style='border-style:outset;border-width: 1px;background: #121212;font-size:$font_size_header;' colspan='1'><B>Schauspieler</td>";
			$s = $s . "<td style='border-style:outset;border-width: 1px;background: #121212;font-size:$font_size_header;' colspan='1'><B>Rolle</td>"; 
			$s = $s . "<td style='border-style:outset;border-width: 1px;background: #121212;font-size:$font_size_header;' colspan='1'><B>Bild</td>";
			$s = $s . "</tr>"; 
			
			if(!empty($roleinfo)) {
				foreach($roleinfo as $key => $cast) {
					if(!empty($cast->thumb)) {
							$pic = "<img src=".$cast->thumb." width=\"150\" height=\"150\" >";
					} else {
							$pic = "";
					}
					
					$s = $s . "<tr>"; 
					$s = $s . "<td style='border-style:outset;border-width: 1px;font-size:$font_size_table;' colspan='1'>$cast->tag</td>"; 
					$s = $s . "<td style='border-style:outset;border-width: 1px;text-align:left;font-size:$font_size_table;' colspan='1'>$cast->role</td>";
					$s = $s . "<td style='border-style:outset;border-width: 1px;text-align:left;font-size:$font_size_table;' colspan='1'>$pic</td>";
					$s = $s . "</tr>"; 
				} 
			}

			return $s;
		}

		private function GenerateHtmlOverview () 
		{
			// Plex Server Daten holen
			$ServerIPAddress 			= $this->ReadPropertyString('ServerIPAddress');
			$ServerPort 		 			= $this->ReadPropertyString('ServerPort');
			$ServerToken		 			= $this->ReadPropertyString('ServerToken');
			
			$IpsIpAdress 					= $this->ReadPropertyString('IpsIPAddress');
			$IpsPort 							= $this->ReadPropertyString('IpsPort');

			// Variablen auslesen
			$librarySectionType 	= $this->ReadAttributeString('librarySectionType');
			$event 								= GetValueFormatted($this->GetIDForIdent('event'));
			$title 								= $this->GetValue('title');
			$summary 							= $this->GetValue('summary');
			$year 								= $this->GetValue('year');
			$seasonEpisode 				= $this->GetValue('seasonEpisode');
			$titleEpisodeMusic 		= $this->GetValue('titleEpisodeMusic');						
			$duration 						= $this->GetValue('duration');
			$movieFormat 					= $this->GetValue('movieFormat');
			$soundFormat 					= $this->GetValue('soundFormat');
			
			$rating 							= $this->GetValue('rating');
			$ratingImage 					= $this->GetValue('ratingImage');
			$audienceRating 			= $this->GetValue('audienceRating');
			$audienceRatingImage 	= $this->GetValue('audienceRatingImage');
			$MyRatingUrl 					= 'http://'.$IpsIpAdress.':'.$IpsPort.'/user/PlexCover/'.$ratingImage.'.png';
			$MyaudienceRatingUrl 	= 'http://'.$IpsIpAdress.':'.$IpsPort.'/user/PlexCover/'.$audienceRatingImage.'.png';
			$MyRating 						= '<img src='.$MyRatingUrl." width=\"40\" hight=\"40\">";
			$MyaudienceRating 		= '<img src='.$MyaudienceRatingUrl." width=\"40\" hight=\"40\">";

			$contentRating 				= $this->GetValue('contentRating');
			$contentRatingNr			= preg_replace('![^0-9]!', '', $contentRating);
			$MycontentRatingUrl		= 'http://'.$IpsIpAdress.':'.$IpsPort.'/user/PlexCover/FSK_'.$contentRatingNr.'.png';

			// Plex URL bauen
			if(!empty($ServerToken)) {				
				$coverURL = 'http://'.$ServerIPAddress.':'.$ServerPort.$this->GetValue('cover').'?X-Plex-Token='.$ServerToken;
				$coverSeasonAlbum = 'http://'.$ServerIPAddress.':'.$ServerPort.$this->GetValue('coverSeasonAlbum').'?X-Plex-Token='.$ServerToken;
			} else {
				$coverURL = 'http://'.$ServerIPAddress.':'.$ServerPort.$this->GetValue('cover');
				$coverSeasonAlbum = 'http://'.$ServerIPAddress.':'.$ServerPort.$this->GetValue('coverSeasonAlbum');
			}		

			// andres Cover holen wenn Artist
			if($librarySectionType !== "artist") {
				$cover = $coverURL;
			} else {
				$cover = $coverSeasonAlbum;
			}

			// Biler zu contentRating holen (nur bei filmen)
			if($librarySectionType == "movie") {
				if(!empty($contentRating)) {
					$contentRatingString = '<img src='.$MycontentRatingUrl." width=\"40\" hight=\"40\">";
				} else {
					$contentRatingString = $contentRating;
				}
			} else {
				$contentRatingString = $contentRating;
			}
			
			// Tabelle aufbauen
			$s = '';
			if($librarySectionType !== "photo") {
				if($event <> "Stop") {					
	        $s = $s . "<table style=\"border-collapse: collapse; width: 100%; \" border=\"0\">";
	        $s = $s . "<tbody>";

	        $s = $s . "<tr style=\"height: 35px;\">";		
	        $s = $s . "<td style=\"width: 20%; height: 18px;text-align: center;padding-right:20px\" rowspan=\"13\">".'<img src='.$cover."\" width=\"350\"></td>";
	        
	        $s = $s . "<td style=\"width: 40%; height: 35px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px \" colspan=\"2\"></td>";
	        $s = $s . "<td style=\"width: 40%; height: 18px; text-align: right;border-bottom: 1px solid white;font-size: 35px;\" colspan=\"2\">".$this->translate(ucfirst($librarySectionType))."</td>";
	        $s = $s . "</tr>";

	        $s = $s . "<td style=\"width: 40%; height: 35px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px \" colspan=\"2\">".$this->translate("Title")."</td>";
	        $s = $s . "<td style=\"width: 40%; height: 18px; text-align: right;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$title."</td>";
	        $s = $s . "</tr>";
	        
					if($librarySectionType !== "artist") {
						$s = $s . "<tr>";
						$s = $s . "<td style=\"text-align: left;height: 35px;font-weight: bold;font-size: 20px; \" colspan=\"3\">".$this->translate("Summary")."</td>";
						$s = $s . "</tr>";
						$s = $s . "<tr>";
		        $s = $s . "<td style=\"text-align: left;vertical-align: top;height: auto;font-size: 16px;\" colspan=\"3\">".$summary."</td>";
		        $s = $s . "</tr>";
					}

					if($librarySectionType !== "artist") {
		        $s = $s . "<tr style=\"height: 40px;\">";
		        $s = $s . "<td style=\"width: 20%; height: 40px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Year")."</td>";
		        $s = $s . "<td style=\"width: 20%; height: 40px; text-align: right;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$year."</td>";
		        $s = $s . "</tr>";
					}

	        if($librarySectionType == 'show') {
	            $s = $s . "<tr style=\"height: 40px;\">";
	            $s = $s . "<td style=\"width: 20%; height: 40px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Season/Episode")."</td>";
	            $s = $s . "<td style=\"width: 20%; height: 40px; text-align: right;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$seasonEpisode."</td>";
	            $s = $s . "</tr>";

	            $s = $s . "<tr style=\"height: 40px;\">";
	            $s = $s . "<td style=\"width: 20%; height: 40px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Episode Name")."</td>";
	            $s = $s . "<td style=\"width: 20%; height: 40px; text-align: right;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$titleEpisodeMusic."</td>";
	            $s = $s . "</tr>";
	        }

					if($librarySectionType !== "artist") {
		        $s = $s . "<tr style=\"height: 40px;\">";
		        $s = $s . "<td style=\"height: 40px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px; \" colspan=\"2\">".$this->translate("Rating")."</td>";		        
						$s = $s . "<td style=\" height: 40px; text-align: right;border-bottom: 1px solid white;font-size: 16px; \" colspan=\"2\">";

						// Nicht immer jede Bewertung vorhanden
						if(!empty($audienceRatingImage) && substr($audienceRatingImage,0,14) == "rottentomatoes" && !empty($ratingImage) && substr($audienceRatingImage,0,14) == "rottentomatoes") {
							// Rating und Audience Rating == true && Rotten
							$s = $s . "<table width=100%; align=left ><tr><td></td><td width=50px;>".$rating."</td><td width=50px>".$MyRating."</td><td width=50px>".$audienceRating."</td><td width=50px>".$MyaudienceRating."</td></tr></table>";
						} elseif(!empty($audienceRatingImage) && substr($audienceRatingImage,0,14) == "rottentomatoes" && empty($ratingImage)) {
							// Rating == false und Audience Rating == true && Rotten
							$s = $s . "<table width=100%; align=left ><tr><td></td><td width=50px>".$audienceRating."</td><td width=50px>".$MyaudienceRating."</td></tr></table>";
						} elseif(!empty($audienceRatingImage) && substr($audienceRatingImage,0,4) == "imdb") {
							// Rating == false und Audience Rating == true && IMDB
							$s = $s . "<table width=100%; align=left ><tr><td></td><td width=50px>".$audienceRating."</td><td width=50px>".$MyaudienceRating."</td></tr></table>";
						}
						$s = $s . "</td>";
						$s = $s . "</tr>";

		        $s = $s . "<tr style=\"height: 40px;\">";
		        $s = $s . "<td style=\"height: 40px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Content Rating")."</td>";
		        $s = $s . "<td style=\"height: 40px; text-align: right;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$contentRatingString."</td>";
		        $s = $s . "</tr>";			
					}

	        $s = $s . "<tr style=\"height: 40px;\">";
	        $s = $s . "<td style=\"text-align: left; height: 40px;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Duration")."</td>";
	        $s = $s . "<td style=\"text-align: right; height: 40px;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$duration."</td>";
	        $s = $s . "</tr>";

					if($librarySectionType !== "artist") {
		        $s = $s . "<tr style=\"height: 40px;\">";
		        $s = $s . "<td style=\"text-align: left; height: 40px;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Video")."</td>";
		        $s = $s . "<td style=\"text-align: right; height: 40px;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$movieFormat."</td>";
		        $s = $s . "</tr>";
					}

	        $s = $s . "<tr style=\"height: 40px;\">";
	        $s = $s . "<td style=\"text-align: left; height: 40px;font-weight: bold;border-bottom: 0px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Sound")."</td>";
	        $s = $s . "<td style=\"text-align: right; height: 40px;border-bottom: 0px solid white;font-size: 16px;\" colspan=\"2\">".$soundFormat."</td>";
	        $s = $s . "</tr>";

	        $s = $s . "</tbody>";
	        $s = $s . "</table>";
				} else {
					$s = "";
				}
				$this->SetValue('overview',$s);
			}
		}

	}