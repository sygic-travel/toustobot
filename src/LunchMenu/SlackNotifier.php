<?php

namespace Toustobot\LunchMenu;

use GuzzleHttp\Client;


class SlackNotifier
{
	/** @var string */
	private $webhookUrl;


	public function __construct(string $webhookUrl)
	{
		$this->webhookUrl = $webhookUrl;
	}


	public function notify(string $text)
	{
		$client = new Client();
		$client->post($this->webhookUrl, [
			'json' => [
				'username' => 'Toustobot',
				'text' => $text,
				'channel' => '#lunch',
			],
		]);
	}
}
