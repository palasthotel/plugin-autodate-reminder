<?php

namespace Palasthotel\WordPress\PluginUpdateCheck\Model;

class PluginVersion {
	var string $slug;
	var string $name;
	var string $currentVersion;
	var string $latestVersion = "???";

	public function checkFailed(): bool {
		return $this->latestVersion === "???";
	}

	public function needsUpdate(){
		return $this->latestVersion != "???" && $this->currentVersion != $this->latestVersion;
	}
}
