<?php

namespace Toustobot\LunchMenu\MenuCrawler;

use Toustobot\LunchMenu\IMenuCrawler;
use Nette\Utils\Strings;
use Symfony\Component\DomCrawler\Crawler;
use Toustobot\LunchMenu\MenuOption;
use Toustobot\Utils\Matcher;


class PlzenskyDvurMenuCrawler implements IMenuCrawler
{
	private const NAME = 'Plzeňský dvůr';
	private const URL = 'http://www.plzenskydvur.cz/tydennimenu/';


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
		$crawler = $crawler->filter('.listek > .tyden > p.title')
			->reduce(function (Crawler $node, int $i) use ($date): bool {
				return Matcher::matchesDate($date, $node->text());
			});
		$list = $crawler->nextAll()->filter('.text')->first()->filter('p.menu_title');


		$options = [];
		$list->each(function (Crawler $item, int $i) use (&$options) {
			$matches = Strings::split($item->nextAll()->first()->text(), '/^\s*([0-9]+\s*(?:g|ks))\s*/u', PREG_SPLIT_NO_EMPTY);

			$option = new MenuOption($item->text(), $matches[1]);
			$option->setPrice($i === 0 ? 139 : ($i === 1 ? 89 : 99));
			$option->setQuantity($matches[0]);

			$options[] = $option;
		});

		//$soup = $crawler->nextAll()->filter('.text')->first()->filter('p')->first()->text();

		return [
			'url' => self::URL,
			'options' => $options,
			'soups' => [

			],
		];
	}
}
