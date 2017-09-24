<?php

namespace Toustobot\LunchMenu\MenuCrawler;

use Toustobot\LunchMenu\IMenuCrawler;
use Nette\Utils\Strings;
use Symfony\Component\DomCrawler\Crawler;
use Toustobot\LunchMenu\MenuOption;
use Toustobot\Utils\Matcher;


class HelanMenuCrawler implements IMenuCrawler
{
	private const NAME = 'Helan';
	private const URL = 'http://helan.cz/centrum-veveri';


	public function getName(): string
	{
		return self::NAME;
	}


	public function getUrl(): string
	{
		return self::URL;
	}


	public function getMenu(\DateTimeInterface $date): array
	{
		$html = file_get_contents(self::URL);

		$crawler = new Crawler($html);
		$crawler = $crawler
			->filter('.et_pb_all_tabs > .et_pb_tab p > strong')
			->reduce(function (Crawler $node, int $i) use ($date): bool {
				return Matcher::matchesDate($date, $node->text());
			});
		$list = $crawler->parents()->eq(0)->nextAll()->filter('ol')->first()->filter('li');

		$options = [];
		$list->each(function (Crawler $item, int $i) use (&$options) {
			$matches = Strings::split($item->text(), '/\s+[(]\s*([0-9,]+)\s*[)]\s*$/u');

			$option = new MenuOption($i, trim($matches[0]));
			$option->setPrice(79);
			$option->setAllergens($matches[1] ?? null);

			$options[] = $option;
		});

		//$soup = Strings::replace($crawler->parents()->first()->html(), '#.*</strong>\s*#u', '');

		return [
			'url' => self::URL,
			'options' => $options,
			'soups' => [

			],
		];
	}
}
