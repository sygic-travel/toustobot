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
	private const URL = 'http://www.hotel-brno-sono.cz/restaurace/';


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
			->filter('#tydenni-menu .fmenu > .item > .item-head')
			->reduce(function (Crawler $node, int $i) use ($date): bool {
				return Matcher::matchesDate($date, $node->text());
			});

		$item = $crawler->parents()->first();
		$list = $item->filter('.foodlist > .itemd');

		$options = [];
		$list->each(function (Crawler $item, int $i) use (&$options) {
			$matches = Strings::match($item->filter('p')->first()->text(), '/^\s*.\)\s*([0-9]+\s*(?:g|ks|l|ml))\s+(.*?)(?:\s+\(([0-9,\s]+)\)?)?$/u');
			$priceMatches = Strings::match($item->filter('.itemd-price')->first()->text(), '/[0-9]+/');


			$option = new MenuOption($i - 1, $matches[2]);
			$option->setPrice(((int) $priceMatches[0]) ?? null);
			$option->setAllergens($matches[3] ?? null);

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
