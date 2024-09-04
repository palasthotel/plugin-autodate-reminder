<?php

namespace Palasthotel\WordPress\PluginUpdateCheck\Model;

class GitlabProjectConfiguration {
	var string $url;
	var string $namespace;
	var string $name;
	var string $token;
    var string $apiBaseUrl;

    public function __construct($url, $namespace, $project, $privateToken) {
		$this->url       = $url;
		$this->namespace = $namespace;
		$this->name      = $project;
		$this->token = $privateToken;
		$this->apiBaseUrl = $this->url."/api/v4";
	}


	public function getUserByUserNameUrl(string $username){
		return untrailingslashit($this->apiBaseUrl)."/users?username=".urlencode($username);
	}

	public function getIssuesUrl(){
		$project = urlencode("$this->namespace/$this->name");
		return untrailingslashit($this->apiBaseUrl) . "/projects/$project/issues";
	}

}
