<?php

namespace Palasthotel\WordPress\PluginUpdateCheck;

use Palasthotel\WordPress\PluginUpdateCheck\Components\Component;
use Palasthotel\WordPress\PluginUpdateCheck\Source\InternalStore;

class AdminNotice extends Component {

	public function onCreate() {
		parent::onCreate();
        
        if(!InternalStore::isGitlabConnectionOk()){
		    add_action('admin_notices', [$this, 'admin_notices']);
        }
	}

	public function admin_notices(){
		?>
		<div class="notice notice-warning">
			<p><strong>Plugin Update Check</strong>: Verbindung zum Gitlab fehlgeschlagen.</p>
		</div>
		<?php
	}


}
