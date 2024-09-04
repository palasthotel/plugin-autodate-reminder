<?php

namespace Palasthotel\WordPress\PluginUpdateCheck;

use Palasthotel\WordPress\PluginUpdateCheck\Components\Component;

class PublicApi extends Component {

	public function onCreate() {
		parent::onCreate();
		add_action('parse_request',[$this, 'handler']);
	}

	public function handler() {
		if($_SERVER['REQUEST_URI'] == '/plugin_update_check') {
			$ip_allow_list = [
				// START -> #101 status cake ips https://www.statuscake.com/kb/knowledge-base/what-are-your-ips/
				"162.243.141.135",
				"107.170.235.240",
				"104.236.163.90",
				// <- END status cake ips
			];
			$requestIp = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR']: "";
			error_log("HTTP_X_FORWARDED_FOR ".$requestIp);
			header("Content-Type: text/plain; charset=utf8");
			if(
				//!isset($_SERVER['HTTP_X_FORWARDED_FOR']) || !in_array($_SERVER['HTTP_X_FORWARDED_FOR'], $ip_allow_list) ||
				!isset($_SERVER['HTTP_X_SECURITY']) || $_SERVER['HTTP_X_SECURITY'] != 'Janeway Pi 1-1-0'
			) {
				http_response_code(401);
				echo "You are not authorized to use this function.";
				exit();
			} else {
				$this->perform_checks();
				exit();
			}
		}
	}


	function perform_checks() {
		$versionList = $this->plugin->plugins->getVersionList();

		$output=[];
		$output[]="checking ".count($versionList)." plugins...";

		$state=200;
		foreach ($versionList as $plugin){
			$output[]=$plugin->name." (".$plugin->currentVersion."):";
			if($plugin->checkFailed()){
				$output[]="> unable to proceed. Skipping version check for this plugin.";
			} else if($plugin->needsUpdate()){
				$output[]="> Version mismatch. $plugin->latestVersion is available, while ".$plugin->currentVersion." is installed. This is the Reason for HTTP Status Code 503.";
				$state=503;
			} else {
				$output[]="> Plugin up to date.";
			}

		}
		http_response_code($state);
		foreach($output as $line) {
			echo $line."\n";
		}
	}
}
