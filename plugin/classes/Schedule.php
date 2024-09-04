<?php

namespace Palasthotel\WordPress\PluginUpdateCheck;

use Palasthotel\WordPress\PluginUpdateCheck\Components\Component;
use Palasthotel\WordPress\PluginUpdateCheck\Model\UpdatesIdByCalenderWeek;
use Palasthotel\WordPress\PluginUpdateCheck\Source\InternalStore;

class Schedule extends Component {

	public function onCreate() {
		parent::onCreate();

		add_action( 'admin_init', [ $this, 'init' ] );
		add_action( Plugin::SCHEDULE_CHECK_CONNECTION, [ $this, 'check_connection' ] );
		add_action( Plugin::SCHEDULE_CHECK_UPDATES, [ $this, 'check_updates' ] );
	}

	public function init() {
		if ( ! wp_next_scheduled( Plugin::SCHEDULE_CHECK_CONNECTION ) ) {
			wp_schedule_event( time(), 'hourly', Plugin::SCHEDULE_CHECK_CONNECTION );
		}
		if ( ! wp_next_scheduled( Plugin::SCHEDULE_CHECK_UPDATES ) ) {
			wp_schedule_event( time(), 'twicedaily', Plugin::SCHEDULE_CHECK_UPDATES );
		}
	}

	public function check_connection() {
		$plugin = $this->plugin;
		InternalStore::setGitlabConnectionOk(
			$plugin->gitlab->validate( $plugin->gitlabProject )
		);
	}

	public function check_updates() {

		if ( ! InternalStore::isGitlabConnectionOk() ) {
			return;
		}

		$now  = new \DateTime();
		$dueDate = $now->modify( 'Friday this week' );
		if ( $dueDate->getTimestamp() - $now->getTimestamp() < HOUR_IN_SECONDS * 36 ) {
			$dueDate = $now->modify( 'Friday next week' );
		}

		$currentUpdatesId = new UpdatesIdByCalenderWeek($dueDate);
		if ( InternalStore::getReportedUpdatesId() == $currentUpdatesId->asString() ) {
			return;
		}

		if ( InternalStore::isReporting() ) {
			return;
		}
		InternalStore::setIsReporting( true );

		$list         = $this->plugin->plugins->getUpdates();
		$updatesCount = count( $list );

		if ( $updatesCount <= 0 ) {
			return;
		}

		$year = $dueDate->format( "y" );
		$week = $dueDate->format( "W" );

		$title = "Plugin updates KW$week/$year";

		$description = "";
		foreach ( $list as $plugin ) {
			$description .= "- [ ] **$plugin->name** $plugin->currentVersion -> $plugin->latestVersion \n";
		}

		$description .= "\n\n---\n\n";
		$description .= "- [ ] Merge Request für Ticket erstellen
- [ ] Updates lokal einspielen
- [ ] Updates lokal testen
- [ ] Ggf. Übersetzungen aktualisieren & committen
- [ ] Updates committen & branch in `stage` mergen
- [ ] Updates auf die Stage ausrollen
- [ ] Updates auf Stage testen
- [ ] Merge Request in `main` mergen
- [ ] Updates auf Production ausrollen
- [ ] Updates auf Production testen";

		$userId = 0;
		if(!empty(PLUGIN_UPDATE_CHECK_GITLAB_ASSIGNEE_USERNAME)){
			$userId = $this->plugin->gitlab->getUserId(
				$this->plugin->gitlabProject,
				PLUGIN_UPDATE_CHECK_GITLAB_ASSIGNEE_USERNAME
			);
		}

		$success = $this->plugin->gitlab->createIssue(
			$this->plugin->gitlabProject,
			$title,
			$description,
			$dueDate->format( "Y-m-d" ),
			$userId,
			PLUGIN_UPDATE_CHECK_GITLAB_LABELS
		);

		if($success) {
			InternalStore::setReportedUpdatesId( $currentUpdatesId );
		} else {
			error_log("Could not create plugin update ticket");
		}

		InternalStore::setIsReporting( false );
	}

}
