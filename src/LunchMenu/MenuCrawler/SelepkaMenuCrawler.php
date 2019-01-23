<?php

namespace Toustobot\LunchMenu\MenuCrawler;

use Toustobot\LunchMenu\IMenuCrawler;
use Nette\Utils\Strings;
use Symfony\Component\DomCrawler\Crawler;
use Toustobot\LunchMenu\MenuOption;


class SelepkaMenuCrawler implements IMenuCrawler
{
	private const NAME = 'Šelepka';
	private const URL = 'http://www.selepova.cz/denni-menu/';


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
		$day = (int) $date->format('j');
		$datePattern = sprintf('/^\s*%s\s*$/u', $day);

		$html = file_get_contents(self::URL);

		$crawler = new Crawler($html);
		$crawler = $crawler->filter(".den > .datum > .cislo")
			->reduce(function (Crawler $node, int $i) use ($datePattern): bool {
				return (bool) Strings::match($node->text(), $datePattern);
			});
		$list = $crawler->parents()->eq(1)->filter('.seznam > ol > li');

		$options = [];
		$list->each(function (Crawler $item, int $i) use (&$options) {
			$text = $item->filter('h6')->text();
			$price = $item->filter('.cena')->text();

			$matches = Strings::match($text, '/^(.+)\(([0-9,]+)\)\s*$/u');
			$dish = Strings::replace($matches[0], '~\s+\|.+~', '');
			$option = new MenuOption($i + 1, $dish);
			$option->setPrice((int) $price);
			$option->setAllergens($matches[1] ?? null);

			$options[] = $option;
		});

		//$soup = trim($crawler->parents()->eq(1)->filter('.seznam > .polevka')->filterXPath('//text()[last()]')->text());

		return [
			'url' => self::URL,
			'options' => $options,
			'soups' => [

			],
		];
	}
}
