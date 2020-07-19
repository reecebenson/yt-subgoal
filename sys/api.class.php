<?php
	class YT_API
	{
		////////////////////////////////////////
		// 			   VARIABLES  			  //
		////////////////////////////////////////
		private $api_link 		= "https://www.googleapis.com/youtube/v3/";
		private $api_key 		= "[redacted]";
		private $api_keys_data	= array("[redacted]", "[redacted]", "[redacted]", "[redacted]", "[redacted]", "[redacted]");
		private $channel_id 	= "";
		private $channel_name 	= "";
		private $channel_init 	= false;
		private $channel_live 	= "";
		private $channel_subs 	= 0;
		// OAuth2.0
		private $client_id		= "[redacted]";
		private $client_secret	= "[redacted]";

		////////////////////////////////////////
		// 			 INITIALISATION  		  //
		////////////////////////////////////////
		public function init()
		{
			$this->channel_init = true;
			$this->channel_subs = 0;
			$this->channel_id 	= "";

			// > Random API Key
			$rand_apikey = array_rand($this->api_keys_data);
			$this->api_key = $this->api_keys_data[$rand_apikey];
		}

		public function live_init()
		{
			$this->init();
		}

		public function printDetails()
		{
			echo "API Link: " . $this->api_link . "<br/>";
			echo "API Key: " . substr($this->api_key, 0, 15) . "...<br/>";
			echo "Channel Initialised: " . ($this->channel_init ? "Yes" : "No") . "<br/>";
			echo "Channel ID: " . $this->channel_id . "<br/>";
			echo "Channel Name: " . $this->channel_name . "<br/>";
			echo "Channel Subscriptions: " . $this->channel_subs . "<br/>";
		}

		////////////////////////////////////////
		// 			 	 LINKS   		  	  //
		////////////////////////////////////////
		public function getLink($linkType, $extraparams = "")
		{
			global $www;
			return $www . "/plugins/ytcounter/" . $linkType . "/" . $this->channel_id . (!empty($extraparams) ? "&" . $extraparams : "");
		}

		////////////////////////////////////////
		// 			 SUBSCRIPTIONS  		  //
		////////////////////////////////////////
		public function updateSubscriberCount()
		{
			// > Ensure that the channel has been initialised
			if(!$this->channel_init)
				return null;

			// > Retrieve channel data
			$req = array('part' => 'statistics', 'id' => $this->channel_id, 'fields' => 'items/statistics/subscriberCount');
			$data = $this->getResp($req);
			$stats = $data['items'][0]['statistics'];

			// > Update channel subscriber count
			$this->channel_subs = $stats['subscriberCount'];
		}

		public function getSubscriberCount()
		{
			// > Ensure that the channel has been initialised
			if(!$this->channel_init)
				return null;

			if($this->channel_subs == 0 || $this->channel_subs == null || is_null($this->channel_subs))
				return 0;

			return $this->channel_subs;
		}

		////////////////////////////////////////
		// 			 	CHANNELS  		  	  //
		////////////////////////////////////////
		public function getChannelDetails($channel_name)
		{
			// > Ensure that the channel has been initialised
			if(!$this->channel_init)
				return null;

			if(strlen($channel_name) == 24 && substr($channel_name, 0, 2) == "UC")
			{
				// https://www.googleapis.com/youtube/v3/channels?part=snippet&id=[channel-id]&type=channel&key=[redacted]
				$req = array('part' => 'snippet', 'id' => $channel_name, 'type' => 'channel', 'fields' => 'items/snippet/title');
				$data = $this->getResp($req);

				$this->channel_id = $channel_name;
				$this->channel_name = $data['items'][0]['snippet']['title'];
				$this->channel_live = "Unknown";
			}
			else
			{
				$req = array('part' => 'snippet', 'q' => $channel_name, 'type' => 'channel', 'maxResults' => '1', 'fields' => 'items/snippet');
				$data = $this->getResp($req, "search");

				// > Update channel ID
				$this->channel_id = $data['items'][0]['snippet']['channelId'];
				$this->channel_name = $data['items'][0]['snippet']['channelTitle'];
				switch($data['items'][0]['snippet']['liveBroadcastContent'])
				{
					case "live": $this->channel_live = "Currently Live"; break;
					default: $this->channel_live = "Not Live"; break;

				}
			}
			return $this->channel_id;
		}

		public function getChannelID()
		{
			if(!$this->channel_init)
				return null;

			return $this->channel_id;
		}

		public function getChannelName()
		{
			if(!$this->channel_init)
				return null;

			return $this->channel_name;
		}

		public function getChannelLivestate()
		{
			if(!$this->channel_init)
				return null;

			return $this->channel_live;
		}

		public function getResp($api_request, $api_type = "channels")
		{
			// > Fix parameters
			$api_request = http_build_query($api_request);
			$api_request = str_replace('%2F', '/', $api_request);

			$url = htmlspecialchars_decode($this->api_link . $api_type . "?" . $api_request . "&key=" . $this->api_key);
			$url = str_replace("&amp;", "&", $url);
			$data = file_get_contents($url);
			$json_data = json_decode($data, true);

			return $json_data;
		}
	}
?>
