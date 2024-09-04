<?php

namespace Palasthotel\WordPress\PluginUpdateCheck\Model;

class UpdatesIdByCalenderWeek implements UpdatesId {

	private string $id;

	public function __construct($datetime = null) {
		$this->id = ($datetime instanceof \DateTime ? $datetime: new \DateTime())->format('Y/W');
	}

	function asString(): string {
		return $this->id;
	}
}
