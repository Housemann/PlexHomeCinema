<?php

declare(strict_types=1);
	class PlexSplitter extends IPSModule
	{
		public function Create()
		{
			//Never delete this line!
			parent::Create();

			$this->ConnectParent('{A7A1B882-9FA6-3846-3DC3-13EA1F7B318B}');
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
			IPS_LogMessage('Splitter FRWD', utf8_decode($data->Buffer));

			$this->SendDataToParent(json_encode(['DataID' => '{094FB1A6-73A2-9BEB-2E16-1967F4A1A3DB}', 'Buffer' => $data->Buffer]));

			return 'String data for device instance!';
		}

		public function ReceiveData($JSONString)
		{
			$data = json_decode($JSONString);
			IPS_LogMessage('Splitter RECV', utf8_decode($data->Buffer));		

			$this->SendDataToChildren(json_encode(['DataID' => '{4223881F-59F4-2962-AA2F-F58C8B7E1E68}', 'Buffer' => $data->Buffer]));
		}
	}