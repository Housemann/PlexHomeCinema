<?php

declare(strict_types=1);
	class PlexRecieveWebhookData extends IPSModule
	{
		private $hook = 'plex';

		public function Create()
		{
			//Never delete this line!
			parent::Create();

			// WebHook generieren // http://user:pass@192.168.11.111:3777/hook/Plex?Test=1
			$this->RegisterHook('/hook/'.$this->hook);						

      // Propertys
			$this->RegisterPropertyString('Username', '');
			$this->RegisterPropertyString('Password', '');			

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

		public function ForwardData($JSONString)
		{
			$data = json_decode($JSONString);
			IPS_LogMessage('IO FRWD', utf8_decode($data->Buffer));
		}

		public function Send(string $Text)
		{
			$this->SendDataToChildren(json_encode(['DataID' => '{9CB87C13-D56D-3118-B827-BBF1B254FD33}', 'Buffer' => $Text]));
		}

		protected function ProcessHookData()
		{
			if ((IPS_GetProperty($this->InstanceID, 'Username') != '') || (IPS_GetProperty($this->InstanceID, 'Password') != '')) {
				if (!isset($_SERVER['PHP_AUTH_USER'])) {
						$_SERVER['PHP_AUTH_USER'] = '';
				}
				if (!isset($_SERVER['PHP_AUTH_PW'])) {
						$_SERVER['PHP_AUTH_PW'] = '';
				}

				if (($_SERVER['PHP_AUTH_USER'] != IPS_GetProperty($this->InstanceID, 'Username')) || ($_SERVER['PHP_AUTH_PW'] != IPS_GetProperty($this->InstanceID, 'Password'))) {
						header('WWW-Authenticate: Basic Realm="NotificationBoard WebHook"');
						header('HTTP/1.0 401 Unauthorized');
						echo 'Authorization required';
						$this->SendDebug('Unauthorized', print_r($_GET, true), 0);
						return;
				}							

				$this->SendDebug('DataWebHook', print_r($_POST["payload"], true), 0);
				$this->Send($_POST["payload"]);
			}
		}

	  private function RegisterHook($WebHook)
	  {
      $ids = IPS_GetInstanceListByModuleID('{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}');
      if (count($ids) > 0) {
          $hooks = json_decode(IPS_GetProperty($ids[0], 'Hooks'), true);
          $found = false;
          foreach ($hooks as $index => $hook) {
              if ($hook['Hook'] == $WebHook) {
                  if ($hook['TargetID'] == $this->InstanceID) {
                      return;
                  }
                  $hooks[$index]['TargetID'] = $this->InstanceID;
                  $found = true;
              }
          }
          if (!$found) {
              $hooks[] = ['Hook' => $WebHook, 'TargetID' => $this->InstanceID];
          }
          IPS_SetProperty($ids[0], 'Hooks', json_encode($hooks));
          IPS_ApplyChanges($ids[0]);
      }
	  } 		
	}