<?php

namespace Palasthotel\WordPress\PluginUpdateCheck\Source;

use Palasthotel\WordPress\PluginUpdateCheck\Model\GitlabIssue;
use Palasthotel\WordPress\PluginUpdateCheck\Model\GitlabProjectConfiguration;

class Gitlab {

	public function validate(
		GitlabProjectConfiguration $project,
	){

		$request = curl_init( $project->getIssuesUrl() );
		curl_setopt( $request, CURLOPT_HTTPHEADER, array( "PRIVATE-TOKEN: $project->token" ) );
		curl_setopt( $request, CURLOPT_HEADER, true );
		curl_setopt( $request, CURLOPT_NOBODY, true );
		curl_setopt( $request, CURLOPT_RETURNTRANSFER, true );
		curl_exec($request);
		$httpCode = curl_getinfo($request, CURLINFO_HTTP_CODE);
		curl_close($request);

		return $httpCode == 200;
	}

	public function getUserId(
		GitlabProjectConfiguration $project,
		string $username
	){
		try {

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $project->getUserByUserNameUrl($username),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'PRIVATE-TOKEN: '.$project->token,
					'Content-Type: application/json'
				),
			));
			$response = curl_exec($curl);
			curl_close($curl);

			$response = json_decode($response);

			return (is_array($response) && count($response) > 0) ? $response[0]->id : 0;

		} catch ( \Exception $exception ) {
		}
		return 0;
	}

	public function createIssue(
		GitlabProjectConfiguration $project,
		string $title,
		string $description, // md
		string $dueDate, // YYYY-MM-DD
		int $userid,
		string $labels,
	) {
		try {

			$body = [
				'title' => $title,
				'description' => $description,
				'due_date' => $dueDate,
				'labels' => $labels,
			];
			if($userid > 0){
				$body["assignee_id"] = $userid;
			}

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $project->getIssuesUrl(),
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => json_encode( $body ),
				CURLOPT_HTTPHEADER => array(
					'PRIVATE-TOKEN: '.$project->token,
					'Content-Type: application/json'
				),
			));
			curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			return $httpCode == 201;

		} catch ( \Exception $exception ) {
			error_log($exception->getMessage());
		}
		return false;
	}
}
