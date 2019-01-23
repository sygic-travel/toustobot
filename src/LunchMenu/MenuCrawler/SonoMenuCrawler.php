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
	private const URL = 'http://www.flames-grill.cz/jidelni-listek/';


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
			->filter('#weekmenu .foodlist > .item > .date')
			->reduce(function (Crawler $node, int $i) use ($date): bool {
				return Matcher::matchesDate($date, $node->text());
			});

		$item = $crawler->parents()->eq(1);
		$list = $item->filter('.item');

		$options = [];
		$list->each(function (Crawler $item, int $i) use (&$options) {
			$priceNode = $item->filter('.price')->first();
			if ($priceNode->count() > 0) {
				$dish = Strings::replace(trim($item->text()), '~\s+~', ' ');
				$matches = Strings::match($dish, '~^(?:([A-G])\))?+\s*(.+)\s(?:\d+\sCZK)$~u');
				$priceMatches = Strings::match($priceNode->text(), '/[0-9]+/');

				$option = new MenuOption($matches[1] ?: ($i + 1), $matches[2]);
				$option->setPrice(((int) $priceMatches[0]) ?? null);
				$option->setAllergens(null);

				$options[] = $option;
			}
		});

		return [
			'url' => self::URL,
			'options' => $options,
			'soups' => [

			],
		];
	}
}
