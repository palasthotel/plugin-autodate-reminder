<?php

namespace Palasthotel\WordPress\PluginUpdateCheck\Model;

class GitlabIssue {
	var int $iid;
	var string $state = "?";
	var string $title = "";
	var string $description = "";
	/**
	 * @var string[]
	 */
	var array $labels;
	/**
	 * @var object
	 */
	var $assignee;

	public function __construct(int $iid) {
		$this->iid = $iid;
	}
}
