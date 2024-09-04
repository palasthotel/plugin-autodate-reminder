<?php

namespace Palasthotel\WordPress\PluginUpdateCheck;

/**
 * Plugin Name:       Plugin Update Check - DEV
 * Description:       Dev inc file
 * Version:           X.X.X
 * Requires at least: X.X
 * Tested up to:      X.X.X
 * Domain Path:       /plugin/languages
 */

include dirname(__FILE__) . "/plugin/plugin-update-check.php";

register_activation_hook(__FILE__, function($multisite){
	Plugin::instance()->onActivation($multisite);
});

register_deactivation_hook(__FILE__, function($multisite){
	Plugin::instance()->onDeactivation($multisite);
});
