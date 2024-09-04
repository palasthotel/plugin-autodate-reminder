<?php


namespace Palasthotel\WordPress\PluginUpdateCheck\Components;

/**
 * Class Component
 *
 * @package Palasthotel\WordPress
 * @version 0.1.2
 */
abstract class Component {
    public \Palasthotel\WordPress\PluginUpdateCheck\Plugin $plugin;

	public function __construct(\Palasthotel\WordPress\PluginUpdateCheck\Plugin $plugin) {
		$this->plugin = $plugin;
		$this->onCreate();
	}

	/**
	 * overwrite this method in component implementations
	 */
	public function onCreate(){
		// init your hooks and stuff
	}
}
