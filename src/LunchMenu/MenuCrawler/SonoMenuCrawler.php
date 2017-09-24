<?php

namespace Toustobot\LunchMenu\MenuCrawler;

use Toustobot\LunchMenu\IMenuCrawler;
use Nette\Utils\Strings;
use Symfony\Component\DomCrawler\Crawler;
use Toustobot\LunchMenu\MenuOption;
use Toustobot\Utils\Matcher;


class SonoMenuCrawler implements IMenuCrawler
{
	private const NAME = 'Sono';
	private const URL = 'https://www.sonocentrum.cz/the-restaurant/denni-menu/';


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
			->filter('article > p > strong')
			->reduce(function (Crawler $node, int $i) use ($date): bool {
				return Matcher::matchesDate($date, $node->text());
			});
		$list = $crawler->parents()->first()->nextAll()->filter('table')->first()->filter('tbody > tr');

		$options = [];
		$list->each(function (Crawler $item, int $i) use (&$options) {
			if ($i === 0) {
				//$soup = $item->filter('td')->first()->text();die;
				return;
			}

			$matches = Strings::split($item->filter('td')->first()->text(), '/\s+([0-9,]+)\s*$/u');

			$option = new MenuOption($i - 1, $matches[0]);
			$option->setPrice((int) $item->filter('td')->eq(1)->text());
			$option->setAllergens($matches[1] ?? null);

			$options[] = $option;
		});

		return [
			'url' => self::URL,
			'options' => $options,
			'soups' => [

			],
		];
	}
}
