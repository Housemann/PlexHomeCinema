<?php

declare(strict_types=1);
	require_once __DIR__ . '/../libs/helper_variables.php';

	class PlexPlayerConfigurator extends IPSModule
	{
		use PLEX_HelperVariables;

		public function Create()
		{
			//Never delete this line!
			parent::Create();

			// Propertys
			$this->RegisterPropertyString('IPAddress','');
			$this->RegisterPropertyString('Port', '');
			$this->RegisterPropertyString('Token', '');
			$this->RegisterPropertyString('PlexUrl', '');

			// Attributes
			$this->RegisterAttributeInteger('rc',0);

			// IP-Symcon IP und Port
			$this->RegisterPropertyString('IpsIpAddress','');
			$this->RegisterPropertyString('IpsPort', '');
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

			$plexIp			= $this->ReadPropertyString('IPAddress');
			$plexPort		= $this->ReadPropertyString('Port');
			$plexToken	= $this->ReadPropertyString('Token');

			$rc = $this->CheckIpAdressPortStatus($plexIp, intval($plexPort));
			$this->WriteAttributeInteger('rc',$rc['ErrorCode']);
			
			if($rc['ErrorCode']<0)
				echo $rc['ErrorMsg'];
		}
	
		public function GetConfigurationForm()
		{
			$data = json_decode(file_get_contents(__DIR__ . "/form.json"),true);

			$plexIp			= $this->ReadPropertyString('IPAddress');
			$plexPort		= $this->ReadPropertyString('Port');
			$plexToken	= $this->ReadPropertyString('Token');			

			if(!empty($plexIp)) {
				$Players = $this->plexPlayer($plexIp, $plexPort, $plexToken);
				$Values = [];

				if(is_countable($Players)) {
					if(count($Players)>0) {
						for($i=0; $i < count($Players); $i++) {
							$instanceID = $this->getPlayer($Players[$i]['create']['playerUUID']);
							$AddValue = [
								'player'      		=> $Players[$i]['create']['player'],
								'playerUUID'      => $Players[$i]['create']['playerUUID'],
								'playerPlatform'  => $Players[$i]['create']['playerPlatform'],
								'ipAddress'       => $Players[$i]['create']['ipAddress'],
								'remoteIpAddress' => $Players[$i]['create']['remoteIpAddress'],
								'instanceID'      => $instanceID
							];
							
							$moduleID = '{DF440146-0E00-8B37-3A28-71D56C0A3CFC}';
							$AddValue['create'] = [
								[
										'moduleID'      => $moduleID,
										'configuration' => [
												'playerUUID'  		=> $Players[$i]['create']['playerUUID'],
												'player'  				=> $Players[$i]['create']['player'],
												'playerPlatform' 	=> $Players[$i]['create']['playerPlatform']
										]
								]
							];
							$Values[] = $AddValue;
						}
						$data['actions'][0]['values'] = $Values;
					}
				}
			}
			return json_encode($data);
		}

		private function getCreatedPlayers()
		{
			$player = [];
		
			//PlexPlayer Instances
			$InstanceIDs = IPS_GetInstanceListByModuleID('{DF440146-0E00-8B37-3A28-71D56C0A3CFC}');
		
			foreach ($InstanceIDs as $IDs) {
				$player[] = [
					"instanceID"      => $IDs,
					"playerUUID"      => IPS_GetProperty($IDs, 'playerUUID'),
					"player"      		=> IPS_GetProperty($IDs, 'player'),
					"playerPlatform"  => IPS_GetProperty($IDs, 'playerPlatform') 
				];
			}
			return $player;
		}

		private function getPlayer(string $playerUUID) 
		{
			$InstanceIDs = [];
		
			//PlexPlayer Instances
			$InstanceIDs[] = IPS_GetInstanceListByModuleID('{DF440146-0E00-8B37-3A28-71D56C0A3CFC}');
		
			foreach ($InstanceIDs as $IDs) {
				foreach ($IDs as $id) {
					if (@IPS_GetProperty($id, 'playerUUID') == $playerUUID) {
						return $id;
					}
				}
			}
			return 0;
		}


		private function plexPlayer (string $plexIp, string $plexPort, string $plexToken) 
		{
			if(!empty($plexToken)) {
				$url = 'http://'.$plexIp.':'.$plexPort.'/status/sessions?X-Plex-Token='.$plexToken;
			} else {
				$url = 'http://'.$plexIp.':'.$plexPort.'/status/sessions';
			}
						
			// Plex XML Metadaten zu Bibliotheken auslesen und in Array konvertieren	
			$homepage = @simplexml_load_file($url);	
			$array_xml = json_decode(json_encode($homepage),true);		
		
			if(is_countable($array_xml)) {

				// List Count Players
				$count_player = $array_xml['@attributes']['size'];
			
				$AddAllPlayer = array();
			
				// List Players
				if(array_key_exists('Video', $array_xml)) {
					if(!array_key_exists('0', $array_xml['Video'])) {
						$Player['create'] = [
							"player"      		=> $array_xml['Video']['Player']['@attributes']['product'],
							"playerUUID"      => $array_xml['Video']['Player']['@attributes']['machineIdentifier'],
							"playerPlatform"  => $array_xml['Video']['Player']['@attributes']['platform'],
							"ipAddress"       => $array_xml['Video']['Player']['@attributes']['address'],
							"remoteIpAddress" => $array_xml['Video']['Player']['@attributes']['remotePublicAddress']
						];
						$AddAllPlayer[] = $Player;
			
					} else {
						for($i=0;$i<$count_player;$i++) {
							$Player['create'] = [
								"player"      		=> $array_xml['Video'][$i]['Player']['@attributes']['product'],
								"playerUUID"      => $array_xml['Video'][$i]['Player']['@attributes']['machineIdentifier'],
								"playerPlatform"  => $array_xml['Video'][$i]['Player']['@attributes']['platform'],
								"ipAddress"       => $array_xml['Video'][$i]['Player']['@attributes']['address'],
								"remoteIpAddress" => $array_xml['Video'][$i]['Player']['@attributes']['remotePublicAddress']
							];
							$AddAllPlayer[] = $Player;
			
						}
					}
				} 
				if(array_key_exists('Track', $array_xml)) {
					if(!array_key_exists('0', $array_xml['Track'])) {
						$Player['create'] = [
							"player" 		  	  => $array_xml['Track']['Player']['@attributes']['product'],
							"playerUUID"      => $array_xml['Track']['Player']['@attributes']['machineIdentifier'],
							"playerPlatform"  => $array_xml['Track']['Player']['@attributes']['platform'],
							"ipAddress"       => $array_xml['Track']['Player']['@attributes']['address'],
							"remoteIpAddress" => $array_xml['Track']['Player']['@attributes']['remotePublicAddress']
						];
						$AddAllPlayer[] = $Player;
			
					} else {
						for($i=0;$i<$count_player;$i++) {
							$Player['create'] = [
								"player" 			    => $array_xml['Track'][$i]['Player']['@attributes']['product'],
								"playerUUID"      => $array_xml['Track'][$i]['Player']['@attributes']['machineIdentifier'],
								"playerPlatform"  => $array_xml['Track'][$i]['Player']['@attributes']['platform'],
								"ipAddress"       => $array_xml['Track'][$i]['Player']['@attributes']['address'],
								"remoteIpAddress" => $array_xml['Track'][$i]['Player']['@attributes']['remotePublicAddress']
							];
							$AddAllPlayer[] = $Player;
						}
					}
				}
				if(array_key_exists('Photo', $array_xml)) {
					if(!array_key_exists('0', $array_xml['Photo'])) {
						$Player['create'] = [
							"player"     			=> $array_xml['Photo']['Player']['@attributes']['product'],
							"playerUUID"      => $array_xml['Photo']['Player']['@attributes']['machineIdentifier'],
							"playerPlatform"  => $array_xml['Photo']['Player']['@attributes']['platform'],
							"ipAddress"       => $array_xml['Photo']['Player']['@attributes']['address'],
							"remoteIpAddress" => $array_xml['Photo']['Player']['@attributes']['remotePublicAddress']
						];
						$AddAllPlayer[] = $Player;
			
					} else {
						for($i=0;$i<$count_player;$i++) {
							$Player['create'] = [
								"player" 			    => $array_xml['Photo'][$i]['Player']['@attributes']['product'],
								"playerUUID"      => $array_xml['Photo'][$i]['Player']['@attributes']['machineIdentifier'],
								"playerPlatform"  => $array_xml['Photo'][$i]['Player']['@attributes']['platform'],
								"ipAddress"       => $array_xml['Photo'][$i]['Player']['@attributes']['address'],
								"remoteIpAddress" => $array_xml['Photo'][$i]['Player']['@attributes']['remotePublicAddress']
							];
							$AddAllPlayer[] = $Player;
						}
					}
				} 
				return $AddAllPlayer;
			}
		}	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	}