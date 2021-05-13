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
			$this->RegisterPropertyInteger('refreshDurationTime', 60);
			$this->RegisterPropertyInteger('OwnScriptID', 0);
			$this->RegisterPropertyBoolean('OwnScriptAktive', false);

			// Propertys HTML Übersicht
			$this->RegisterPropertyBoolean('CreateSummary', true);
			$this->RegisterPropertyBoolean('CreateYear', true);
			$this->RegisterPropertyBoolean('CreateSeasonEpisode', true);
			$this->RegisterPropertyBoolean('CreateEpisodeName', true);
			$this->RegisterPropertyBoolean('CreateRating', true);
			$this->RegisterPropertyBoolean('CreateContentRating', true);
			$this->RegisterPropertyBoolean('CreateDuration', true);
			$this->RegisterPropertyBoolean('CreateProgressDuration', true);
			$this->RegisterPropertyBoolean('CreateVideo', true);
			$this->RegisterPropertyBoolean('CreateSound', true);

			// Attributes
			$this->RegisterAttributeString('librarySectionType','');
			$this->RegisterAttributeString('PlexIpAdress','');
			$this->RegisterAttributeString('PlexPort','');
			$this->RegisterAttributeString('PlexToken','');
			$this->RegisterAttributeString('PlexExtUrl','');
			$this->RegisterAttributeString('durationPercent','');

			// IP-Symcon IP und Port
			#$this->RegisterAttributeString('IpsIPAddress','');
			#$this->RegisterAttributeString('IpsPort','');

			// Timer anlegen
			$TimerNameRand = rand(1,99);
			$this->RegisterAttributeString('TimerNameRand',"TimerPlexRemain".$TimerNameRand);
			$this->RegisterTimer ("TimerPlexRemain".$TimerNameRand, 0, 'PLEX_GetDuration($_IPS[\'TARGET\'],\'Timer\');');

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
			// User
			$this->Variable_Register('user', $this->translate('User'), '', '', 3, false,3);
			// Titel
			$this->Variable_Register('title', $this->translate('Title'), '', '', 3, false, 6);
			// Titel Staffel/Album
			$this->Variable_Register('titleSeasonAlbum', $this->translate('Season / Album'), '', '', 3, false, 9);
			// Titel Episode/Musik
			$this->Variable_Register('titleEpisodeMusic', $this->translate('Episode / Music'), '', '', 3, false, 12);
			// Mediathekname und MediathekId
			$this->Variable_Register('MediaLibraryName', $this->translate('Media library Name'), '', '', 3, false, 15);
			$this->Variable_Register('MediaLibraryId', $this->translate('Media library ID'), '', '', 3, false, 18);
			// Studio
			$this->Variable_Register('studio', $this->translate('Studio'), '', '', 3, false, 21);
			// Veröffentlichungs-Datum
			$this->Variable_Register('AvailableAt', $this->translate('Available At'), '', '', 3, false, 24);
			// Zusammenfassung
			$this->Variable_Register('summary', $this->translate('Summary'), '~HTMLBox', '', 3, false, 27);
			// Bewertung			
			$this->Variable_Register('rating', $this->translate('Rating'), '', '', 3, false, 30);
			$this->Variable_Register('audienceRating', $this->translate('Audience Rating'), '', '', 3, false, 33);
			// Rating and AudienceRating
			$this->Variable_Register('ratingImage', $this->translate('Rating Image'), '', '', 3, false, 36);
			$this->Variable_Register('audienceRatingImage', $this->translate('Audience Rating Image'), '', '', 3, false, 39);			
			// Altersbeschränkung
			$this->Variable_Register('contentRating', $this->translate('Content Rating'), '', '', 3, false, 41);
			// Cover
			$this->Variable_Register('cover', $this->translate('Cover'), '', '', 3, false, 44);
			// Cover Serie
			$this->Variable_Register('coverSeasonAlbum', $this->translate('Cover Season / Album'), '', '', 3, false, 47);
			// Fanart
			$this->Variable_Register('coverFanart', $this->translate('Fanart'), '', '', 3, false, 50);
			// Movie Format
			$this->Variable_Register('movieFormat', $this->translate('Movie Format'), '', '', 3, false, 53);
			// Sound Format
			$this->Variable_Register('soundFormat', $this->translate('Sound Format'), '', '', 3, false, 56);
			// Aspect Ratio
			$this->Variable_Register('aspectRatio', $this->translate('Aspect Ratio'), '', '', 3, false, 59);
			// Total Duration
			$this->Variable_Register('duration', $this->translate('Total Duration'), '', '', 3, false, 62);
			// Remaining Duration
			$this->Variable_Register('remainingduration', $this->translate('Remaining Duration'), '', '', 3, false, 63);
			// Director
			$this->Variable_Register('director', $this->translate('Director'), '', '', 3, false, 65);
			// Producer
			$this->Variable_Register('producer', $this->translate('Producer'), '', '', 3, false, 68);
			// Writer
			$this->Variable_Register('writer', $this->translate('Writer'), '', '', 3, false, 71);	
			// SeasonEpisode
			$this->Variable_Register('seasonEpisode', $this->translate('Season Episode'), '', '', 3, false, 74);
			// Year
			$this->Variable_Register('year', $this->translate('Year'), '', '', 3, false, 77);
			// Role
			$this->Variable_Register('role', $this->translate('Role'), '~HTMLBox', '', 3, false, 80);
			// Overview
			$this->Variable_Register('overview', $this->translate('Overview'), '~HTMLBox', '', 3, false, 83);
				
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
			#IPS_LogMessage('Device RECV', utf8_decode($data->Buffer));
			$this->SendDebug(__FUNCTION__, json_encode(utf8_decode($data->Buffer)), 0);

			// Plex Server Daten aus Configurator holen
			$InstanceIDConf = IPS_GetInstanceListByModuleID('{01D944DE-8835-F81F-9A3A-DA544E2BB9A1}')[0];
			// Propertys aus Konfigurator in Attribute schreiben
			$this->WriteAttributeString('PlexIpAdress', IPS_GetProperty($InstanceIDConf, 'IPAddress'));
			$this->WriteAttributeString('PlexPort', IPS_GetProperty($InstanceIDConf, 'Port'));
			$this->WriteAttributeString('PlexToken', IPS_GetProperty($InstanceIDConf, 'Token'));
			$this->WriteAttributeString('PlexExtUrl', IPS_GetProperty($InstanceIDConf, 'PlexUrl'));

			#$this->WriteAttributeString('IpsIPAddress', IPS_GetProperty($InstanceIDConf, 'IpsIpAddress'));
			#$this->WriteAttributeString('IpsPort', IPS_GetProperty($InstanceIDConf, 'IpsPort'));

			// Daten verarbeiten
			$this->ReadAndProcessData(utf8_decode($data->Buffer));

		}

		private function ReadAndProcessData($data) 
		{			
			$data = json_decode(utf8_encode($data));
			$this->SendDebug(__FUNCTION__, json_encode($data), 0);
			
			// Pruefung ob Objekt librarySectionType existiert (z.B. wenn Live DVR geschaut wird.)
			$rc = 0;
			if(isset($data->Metadata->librarySectionType)) {
			    $rc = 1;
			} else {
			    $rc = -1;
			}

			if($rc>=0) {

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
					
					// Array fuer Message
					$arrayMessage['PLEX_Player'] = IPS_GetName($this->InstanceID);
					$arrayMessage['PLEX_LibrarySectionType'] = $librarySectionType;

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

					// Array fuer Message
					$arrayMessage['PLEX_Event'] = $event;

					#################################################################
					// Timer zum Restlaufzeits Update
					$TimerNameRand = $this->ReadAttributeString('TimerNameRand');
					if($event <> "media.stop") {
						$this->SetTimerInterval($TimerNameRand, $this->ReadPropertyInteger("refreshDurationTime") * 1000);					
					} else {
						$this->SetTimerInterval($TimerNameRand, 0);
					}

					#################################################################
					// User
					$user = @$data->Account->title;			
					if($event <> "media.stop") {
						$this->SetValue('user',$user);
					} else {
						$this->SetValue('user','');
					}	

					// Array fuer Message
					$arrayMessage['PLEX_User'] = $user;
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

					// Array fuer Message
					if(!empty($title) || $title !== NULL) {
						$arrayMessage['PLEX_Title'] = $title;
					} else {
						$arrayMessage['PLEX_Title'] = '';
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

					// Array fuer Message
					if(!empty($titleSeasonAlbum) || $titleSeasonAlbum !== NULL) {
						$arrayMessage['PLEX_SeasonAlbum'] = $titleSeasonAlbum;
					} else {
						$arrayMessage['PLEX_SeasonAlbum'] = '';
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

					// Array fuer Message
					if(!empty($titleEpisodeMusic) || $titleEpisodeMusic !== NULL) {
						$arrayMessage['PLEX_TitleEpisodeMusic'] = $titleEpisodeMusic;
					} else {
						$arrayMessage['PLEX_TitleEpisodeMusic'] = '';
					}

					#################################################################
					// Veröffentlichungs-Datum
					// prüfen wenn leer
					if(!empty(@$metadata->originallyAvailableAt) || @$metadata->originallyAvailableAt !== NULL) {
						if($librarySectionType == "show") {
							$AvailableAt = @date( "d.m.Y", strtotime($metadata->originallyAvailableAt));							
						} elseif ($librarySectionType == "movie") {
							$AvailableAt = @date( "d.m.Y", strtotime($metadata->originallyAvailableAt));							
						} elseif ($librarySectionType == "artist") {
							$AvailableAt = '';
						} elseif ($librarySectionType == "photo") {
							$AvailableAt = '';
						}
					} else {
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
						$this->SetValue('MediaLibraryName',@$metadata->librarySectionTitle);
						$this->SetValue('MediaLibraryId',@$metadata->librarySectionID);									
					} else {					
						// Mediathekname und MediathekId
						$this->SetValue('MediaLibraryName','');
						$this->SetValue('MediaLibraryId','');
					}

					// Array fuer Message
					if(!empty(@$metadata->librarySectionTitle) || @$metadata->librarySectionTitle !== NULL) {
						$arrayMessage['PLEX_MediaLibraryName'] = $metadata->librarySectionTitle;
					} else {
						$arrayMessage['PLEX_MediaLibraryName'] = '';
					}

					#################################################################				
					// Zusammenfassung
					if($event <> "media.stop") {				
						$this->SetValue('summary',@$metadata->summary);					
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

					// Array fuer Message
					if(!empty($cover) || $cover !== NULL) {
						$arrayMessage['PLEX_Cover'] = $cover;
					} else {
						$arrayMessage['PLEX_Cover'] = '';
					}

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

					// Array fuer Message
					if(!empty($coverSeasonAlbum) || $coverSeasonAlbum !== NULL) {
						$arrayMessage['PLEX_CoverSeasonAlbum'] = $coverSeasonAlbum;
					} else {
						$arrayMessage['PLEX_CoverSeasonAlbum'] = '';
					}

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
					
					// Array fuer Message
					if(!empty($movieFormat) || $movieFormat !== NULL) {
						$arrayMessage['PLEX_MovieFormat'] = $movieFormat;
					} else {
						$arrayMessage['PLEX_MovieFormat'] = '';
					}
						
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

					// Array fuer Message
					if(!empty($soundFormat) || $soundFormat !== NULL) {
						$arrayMessage['PLEX_SoundFormat'] = $soundFormat;
					} else {
						$arrayMessage['PLEX_SoundFormat'] = '';
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
					
					// Array fuer Message
					if(!empty($duration) || $duration !== NULL) {
						$arrayMessage['PLEX_Duration'] = $duration;
					} else {
						$arrayMessage['PLEX_Duration'] = '';
					}

					if($event <> "media.stop") {
						$this->SetValue('duration',$duration);
					} else {
						$this->SetValue('duration','');
					}

					#################################################################
					// Remaining duration
					$this->GetDuration($event);

					#################################################################
					// SeasonEpisode
					if($librarySectionType == "show") {					
						$seasonEpisode = @$PlayerSteamData->SeasonEpisode;					
					
						if($event <> "media.stop") {
							$this->SetValue('seasonEpisode',$seasonEpisode);
						} else {
							$this->SetValue('seasonEpisode','');
						}

						// Array fuer Message					
						if(!empty($seasonEpisode) || $seasonEpisode !== NULL) {
							$arrayMessage['PLEX_SeasonEpisode'] = $seasonEpisode;
						} else {
							$arrayMessage['PLEX_SeasonEpisode'] = '';
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
						if(!empty(@$metadata->ratingImage)) {
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
						$this->SendDebug(__FUNCTION__, "audienceRatingImage: ".$audienceRatingImage, 0);
						$this->SendDebug(__FUNCTION__, "audienceRatingImage_host: ".$audienceRatingImage_host, 0);

							if($audienceRatingImage_host=="rottentomatoes") {
								$audienceRatingImage_pic  = substr($audienceRatingImage,strripos($audienceRatingImage,".")+1,10);
								$this->SetValue('audienceRating',strval($metadata->audienceRating * 10).' %');
								$this->SetValue('audienceRatingImage',$audienceRatingImage_host."_".$audienceRatingImage_pic);
							} elseif($audienceRatingImage_host=="imdb") {
								$audienceRatingImage_pic  = substr($audienceRatingImage,strripos($audienceRatingImage,".")+1,10);
								$this->SetValue('audienceRating',strval($metadata->audienceRating));
								$this->SetValue('audienceRatingImage',$audienceRatingImage_host."_".$audienceRatingImage_pic);
							} elseif($audienceRatingImage_host=="themoviedb") {
								$audienceRatingImage_pic  = substr($audienceRatingImage,strripos($audienceRatingImage,".")+1,10);
								$this->SetValue('audienceRating',strval($metadata->audienceRating * 10).' %');
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
					// Daten-Plex übergeben
					$ServerIpPort			= $this->ReadAttributeString('PlexIpAdress').':'.$this->ReadAttributeString('PlexPort');
					$ServerToken			= $this->ReadAttributeString('PlexToken');	
					$PlexUrl					= $this->ReadAttributeString('PlexExtUrl');	

					// Array fuer Message
					$arrayMessage['PLEX_IpPort'] = $ServerIpPort;
					$arrayMessage['PLEX_Token']  = $ServerToken;
					$arrayMessage['PLEX_ExtUrl'] = $PlexUrl;

					// Message Senden wenn Script hinterlegt
					if($this->ReadPropertyBoolean('OwnScriptAktive') === true) {				
						#if($event == "media.play") {
							$this->SendMessage($arrayMessage);
						#}

					}
					
					#################################################################
					// Html Overview
					$this->GenerateHtmlOverview ();
				}
			}
		}


		public function GetDuration(string $event)
		{
			// Player aus Modul
			$playerUUID = $this->ReadPropertyString('playerUUID');

			// LibrarySectionType holen
			$librarySectionType = $this->ReadAttributeString('librarySectionType');

			// Plex Session Daten vom Player holen
			$ArraySessionData = $this->getPlexPlayerSessionData ($playerUUID);
			$PlayerSteamData  = json_decode(json_encode($ArraySessionData));

			// Laufzeit und Gesamtlaufzeit holen
			$duration 			= @$PlayerSteamData->duration;
			$Runtime				= @$PlayerSteamData->runtime;
			$RemainDuration = intval(ceil($duration-$Runtime));

			if(!empty($duration)) {
				$precent = intval(ceil(intval(ceil($Runtime))/intval(ceil($duration))*100));
			} else {
				$precent = 0;
			}
			
				$this->WriteAttributeString("durationPercent",$precent);

			// Remaining Duration
			if($librarySectionType == "show") {					
				$returnInt = $this->intervall($RemainDuration);
				$remDuration = $returnInt['hms_name'];				
			} elseif ($librarySectionType == "movie") {
				$returnInt = $this->intervall($RemainDuration);
				$remDuration = $returnInt['hms_name'];						
			} elseif ($librarySectionType == "artist") {
				$returnInt = $this->intervall($RemainDuration);
				$remDuration = $returnInt['hms_name'];				
			} elseif ($librarySectionType == "photo") {
				$remDuration = '';
			}

			// Wenn Metadaten nicht vorhanden
			if(empty($remDuration))
				$remDuration = '';
			
			// Array fuer Message
			if(!empty($remDuration) || $remDuration !== NULL) {
				$arrayMessage['PLEX_RemainDuration'] = $remDuration;
			} else {
				$arrayMessage['PLEX_RemainDuration'] = '';
			}

			if($event <> "media.stop") {
				$this->SetValue('remainingduration',$remDuration);
				
				// HTML Box aktualisieren
				$this->GenerateHtmlOverview ();
			} else {
				$this->SetValue('remainingduration','');
			}			

		}


		/*
		// Not in Use
		private function GetMetaDataFromKey (string $key) 
		{
			$ServerIPAddress 	= $this->ReadAttributeString('PlexIpAdress');
			$ServerPort 			= $this->ReadAttributeString('PlexPort');
			$ServerToken			= $this->ReadAttributeString('PlexToken');
			
			if(!empty($ServerToken)) {
				$url = 'http://'.$ServerIPAddress.':'.$ServerPort.$key.'?X-Plex-Token='.$ServerToken;
			} else {
				$url = 'http://'.$ServerIPAddress.':'.$ServerPort.$key;
			}
		
			$metadata = simplexml_load_file($url);
			$return = json_decode(json_encode($metadata));
			return $return;
		}
		*/

		private function getPlexPlayerSessionData (string $playerUUID) 
		{
			$ServerIPAddress 	= $this->ReadAttributeString('PlexIpAdress');
			$ServerPort 			= $this->ReadAttributeString('PlexPort');
			$ServerToken			= $this->ReadAttributeString('PlexToken');	
			#$PlexUrl					= $this->ReadAttributeString('PlexExtUrl');	

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
									"duration"					=> @$data[$library][$i]['@attributes']['duration'] / 1000,
									"runtime"						=> @$data[$library][$i]['@attributes']['viewOffset'] / 1000
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
									"soundFormat"    		=> @$data[$library][$i]['Media']['Part']['Stream']['@attributes']['displayTitle'],
									"duration"			 		=> @$data[$library][$i]['@attributes']['duration'] / 1000,
									"runtime"						=> @$data[$library][$i]['@attributes']['viewOffset'] / 1000
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
								"duration"					=> @$data[$library]['@attributes']['duration'] / 1000,
								"runtime"						=> @$data[$library]['@attributes']['viewOffset'] / 1000
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
									"soundFormat"    		=> @$data[$library]['Media']['Part']['Stream']['@attributes']['displayTitle'],
									"duration"			 		=> @$data[$library]['@attributes']['duration'] / 1000,
									"runtime"						=> @$data[$library]['@attributes']['viewOffset'] / 1000
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
			$ServerIPAddress 	= $this->ReadAttributeString('PlexIpAdress');
			$ServerPort 			= $this->ReadAttributeString('PlexPort');
			$ServerToken			= $this->ReadAttributeString('PlexToken');
			#$PlexUrl					= $this->ReadAttributeString('PlexExtUrl');

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
				$files = 'plex_cover.jpg,plex_fanart.jpg,imdb_rating.png,rottentomatoes_ripe.png,rottentomatoes_rotten.png,rottentomatoes_spilled.png,rottentomatoes_upright.png,FSK_0.png,FSK_6.png,FSK_12.png,FSK_16.png,FSK_18.png,themoviedb_rating.png';

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

		public function GenerateHtmlOverview () 
		{
			$ServerIPAddress 			= $this->ReadAttributeString('PlexIpAdress');
			$ServerPort 					= $this->ReadAttributeString('PlexPort');
			$ServerToken					= $this->ReadAttributeString('PlexToken');
			$PlexUrl							= $this->ReadAttributeString('PlexExtUrl');	
			
			#$IpsIpAdress 				= $this->ReadAttributeString('IpsIPAddress');
			#$IpsPort 						= $this->ReadAttributeString('IpsPort');
			$Sys_GetNetworkInfo		= Sys_GetNetworkInfo();
			$IpsIpAdress 					= $Sys_GetNetworkInfo[0]['IP'];
			$IpsPort 							= '3777';

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

			// Propertys HTML Checkbox auslesen
			$CreateSummary          		= $this->ReadPropertyBoolean('CreateSummary');
			$CreateYear             		= $this->ReadPropertyBoolean('CreateYear');
			$CreateCreateSeasonEpisode	= $this->ReadPropertyBoolean('CreateSeasonEpisode');
			$CreateCreateEpisodeName		= $this->ReadPropertyBoolean('CreateEpisodeName');
			$CreateRating           		= $this->ReadPropertyBoolean('CreateRating');
			$CreateContentRating    		= $this->ReadPropertyBoolean('CreateContentRating');
			$CreateDuration         		= $this->ReadPropertyBoolean('CreateDuration');
			$CreateProgressDuration 		= $this->ReadPropertyBoolean('CreateProgressDuration');
			$CreateVideo            		= $this->ReadPropertyBoolean('CreateVideo');
			$CreateSound            		= $this->ReadPropertyBoolean('CreateSound');

			// Attribute auslesen
			$durationPercent = $this->ReadAttributeString("durationPercent");
			
			// Connect Dienst und URL auslesen
			$connectControlId = @IPS_GetInstanceListByModuleID ("{9486D575-BE8C-4ED8-B5B5-20930E26DE6F}")[0];
			$IsActive = json_decode(IPS_GetConfiguration($connectControlId));
			 
			if($IsActive->Active) {
				$Url = CC_GetUrl($connectControlId);
			} else {
				$Url = 'http://'.$IpsIpAdress.':'.$IpsPort;
			}
			
			// Cover laden
			$MyRatingUrl 					= $Url.'/user/PlexCover/'.$ratingImage.'.png';
			$MyaudienceRatingUrl 	= $Url.'/user/PlexCover/'.$audienceRatingImage.'.png';			

			// Rating bauen
			$MyRating 						= '<img src='.$MyRatingUrl." width=\"40\" hight=\"40\">";
			$MyaudienceRating 		= '<img src='.$MyaudienceRatingUrl." width=\"40\" hight=\"40\">";

			// Altersfreigabe bauen
			$contentRating 				= $this->GetValue('contentRating');
			$contentRatingNr			= preg_replace('![^0-9]!', '', $contentRating);
			$MycontentRatingUrl		= $Url.'/user/PlexCover/FSK_'.$contentRatingNr.'.png';

			// Plex URL bauen
			if(!empty($ServerToken) && !empty($PlexUrl)) {
				$coverURL = $PlexUrl.$this->GetValue('cover').'?X-Plex-Token='.$ServerToken;
				$coverSeasonAlbum = $PlexUrl.$this->GetValue('coverSeasonAlbum').'?X-Plex-Token='.$ServerToken;
			} elseif(!empty($ServerToken) && empty($PlexUrl)) {
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
	        $s = $s . "<td style=\"width: 20%; height: 18px;text-align: center;padding-right:20px\" rowspan=\"13\">".'<img src='.$cover." width=\"350\"></td>";
	        
	        $s = $s . "<td style=\"width: 40%; height: 35px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px \" colspan=\"2\"></td>";
	        $s = $s . "<td style=\"width: 40%; height: 18px; text-align: right;border-bottom: 1px solid white;font-size: 35px;\" colspan=\"2\">".$this->translate(ucfirst($librarySectionType))."</td>";
	        $s = $s . "</tr>";

	        if($librarySectionType == "artist") {
						$s = $s . "<td style=\"width: 40%; height: 35px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px \" colspan=\"2\">".$this->translate("Artist-Name")."</td>";
					} elseif ($librarySectionType == "show") {
						$s = $s . "<td style=\"width: 40%; height: 35px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px \" colspan=\"2\">".$this->translate("Series-Name")."</td>";
					} elseif ($librarySectionType == "movie") {
						$s = $s . "<td style=\"width: 40%; height: 35px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px \" colspan=\"2\">".$this->translate("Movie-Name")."</td>";
					}
	        $s = $s . "<td style=\"width: 40%; height: 18px; text-align: right;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$title."</td>";
	        $s = $s . "</tr>";
	        

					if($CreateSummary===true) {
						if($librarySectionType !== "artist") {
							if(!empty($summary)) {
								$s = $s . "<tr>";
								$s = $s . "<td style=\"text-align: left;height: 35px;font-weight: bold;font-size: 20px; \" colspan=\"3\">".$this->translate("Summary")."</td>";
								$s = $s . "</tr>";
								$s = $s . "<tr>";
								$s = $s . "<td style=\"text-align: left;vertical-align: top;height: auto;font-size: 16px;\" colspan=\"3\">".$summary."</td>";
								$s = $s . "</tr>";
							}
						}
					}

					if($CreateYear===true) {
						if($librarySectionType !== "artist") {
							$s = $s . "<tr style=\"height: 40px;\">";
							$s = $s . "<td style=\"width: 20%; height: 40px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Year")."</td>";
							$s = $s . "<td style=\"width: 20%; height: 40px; text-align: right;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$year."</td>";
							$s = $s . "</tr>";
						}
					}

	        if($librarySectionType == 'show') {
						if($CreateCreateSeasonEpisode===true) {
							$s = $s . "<tr style=\"height: 40px;\">";
							$s = $s . "<td style=\"width: 20%; height: 40px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Season/Episode")."</td>";
							$s = $s . "<td style=\"width: 20%; height: 40px; text-align: right;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$seasonEpisode."</td>";
							$s = $s . "</tr>";
						}

						if($CreateCreateEpisodeName===true) {
							$s = $s . "<tr style=\"height: 40px;\">";
							$s = $s . "<td style=\"width: 20%; height: 40px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Episode Name")."</td>";
							$s = $s . "<td style=\"width: 20%; height: 40px; text-align: right;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$titleEpisodeMusic."</td>";
							$s = $s . "</tr>";
						}
	        }

					if($CreateRating===true) {
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
							} elseif(!empty($audienceRatingImage) && substr($audienceRatingImage,0,10) == "themoviedb") {
								// Rating == false und Audience Rating == true && THEMOVIEDB
								$s = $s . "<table width=100%; align=left ><tr><td></td><td width=50px>".$audienceRating."</td><td width=50px>".$MyaudienceRating."</td></tr></table>";							
							}

							$s = $s . "</td>";
							$s = $s . "</tr>";
							
							/*
							if(!empty($contentRating)) {
								$s = $s . "<tr style=\"height: 40px;\">";
								$s = $s . "<td style=\"height: 40px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Content Rating")."</td>";
								$s = $s . "<td style=\"height: 40px; text-align: right;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$contentRatingString."</td>";
								$s = $s . "</tr>";
							}
							*/
						}
					}

					if($CreateContentRating===true) {
						if(!empty($contentRating)) {
							$s = $s . "<tr style=\"height: 40px;\">";
							$s = $s . "<td style=\"height: 40px; text-align: left;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Content Rating")."</td>";
							$s = $s . "<td style=\"height: 40px; text-align: right;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$contentRatingString."</td>";
							$s = $s . "</tr>";
						}
					}

					if($CreateDuration===true) {
						$s = $s . "<tr style=\"height: 40px;\">";
						$s = $s . "<td style=\"text-align: left; height: 40px;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Duration")."</td>";
						$s = $s . "<td style=\"text-align: right; height: 40px;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$duration."</td>";
						$s = $s . "</tr>";
					}

					if($CreateProgressDuration===true) {
						$s = $s . "<tr style=\"height: 40px;\">";
						$s = $s . "<td style=\"text-align: left; height: 40px;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Progress")."</td>";
						$s = $s . "<td style=\"text-align: right; height: 40px; width: 10px;border-bottom: 1px solid white;font-size: 16px; \" colspan=\"2\">";
						$s = $s . "<progress value=\"".$durationPercent."\" max=\"100\"></progress>"." ".$durationPercent." %"."</td>";
						$s = $s . "</tr>";					
					}

					if($CreateVideo===true) {
						if($librarySectionType !== "artist") {
							$s = $s . "<tr style=\"height: 40px;\">";
							$s = $s . "<td style=\"text-align: left; height: 40px;font-weight: bold;border-bottom: 1px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Video")."</td>";
							$s = $s . "<td style=\"text-align: right; height: 40px;border-bottom: 1px solid white;font-size: 16px;\" colspan=\"2\">".$movieFormat."</td>";
							$s = $s . "</tr>";
						}
					}

					if($CreateVideo===true) {
						$s = $s . "<tr style=\"height: 40px;\">";
						$s = $s . "<td style=\"text-align: left; height: 40px;font-weight: bold;border-bottom: 0px solid white;font-size: 20px;\" colspan=\"2\">".$this->translate("Sound")."</td>";
						$s = $s . "<td style=\"text-align: right; height: 40px;border-bottom: 0px solid white;font-size: 16px;\" colspan=\"2\">".$soundFormat."</td>";
						$s = $s . "</tr>";
					}

	        $s = $s . "</tbody>";
	        $s = $s . "</table>";
				} else {
					$s = "";
				}
				$this->SendDebug(__FUNCTION__, $s, 0);
				$this->SetValue('overview',$s);
			}
		}

		private function SendMessage($dataAR)
		{
			if ($this->ReadPropertyBoolean('OwnScriptAktive') === true) {
				$SkriptID = $this->ReadPropertyInteger('OwnScriptID');
				#IPS_LogMessage("PlexModule",json_encode($dataAR));
				$this->SendDebug(__FUNCTION__, json_encode($dataAR), 0);
				if (($SkriptID > 0) && (@IPS_ScriptExists($SkriptID) === true)) {					
					IPS_RunScriptEx($SkriptID,$dataAR);
				}
			}
		}

	}