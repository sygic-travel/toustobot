<?php

namespace Toustobot\LunchMenu\MenuCrawler;

use Toustobot\LunchMenu\IMenuCrawler;
use Nette\Utils\Strings;
use Symfony\Component\DomCrawler\Crawler;


class HelanMenuCrawler implements IMenuCrawler
{
	private const MENU_URL = 'http://helan.cz/centrum-veveri';


	public function getMenu(\DateTimeInterface $date): array
	{
		$day = (int) $date->format('j');
		$month = (int) $date->format('n');
		$datePattern = sprintf('/^\s*%s\s*\.\s*%s\s*\.\s*$/u', $day, $month);

		$html = file_get_contents(self::MENU_URL);

		$crawler = new Crawler($html);
		$crawler = $crawler
			->filter('.et_pb_all_tabs > .et_pb_tab > p > strong')
			->reduce(function (Crawler $node, int $i) use ($datePattern): bool {
				return (bool) Strings::match($node->text(), $datePattern);
			});
		$list = $crawler->parents()->eq(0)->nextAll()->filter('ol')->first()->filter('li');

		$options = [];
		$list->each(function (Crawler $item, int $i) use (&$options) {
			$matches = Strings::split($item->text(), '/\s+[(]\s*([0-9,]+)\s*[)]\s*$/u');
			$options[] = [
				'id' => $i,
				'text' => trim($matches[0]),
				'price' => 79,
				'alergens' => $matches[1] ?? null,
				'quantity' => null,
			];
		});

		//$soup = Strings::replace($crawler->parents()->first()->html(), '#.*</strong>\s*#u', '');

		return [
			'url' => self::MENU_URL,
			'options' => $options,
			'soups' => [

			],
		];
	}
}
