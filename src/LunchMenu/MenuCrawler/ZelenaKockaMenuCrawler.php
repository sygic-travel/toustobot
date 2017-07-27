<?php

namespace Toustobot\LunchMenu\MenuCrawler;

use Toustobot\LunchMenu\IMenuCrawler;
use Nette\Utils\Strings;
use Symfony\Component\DomCrawler\Crawler;


class ZelenaKockaMenuCrawler implements IMenuCrawler
{
	private const MENU_URL = 'http://www.zelenakocka.cz/index.php';


	public function getMenu(\DateTimeInterface $date): array
	{
		$day = (int) $date->format('j');
		$month = (int) $date->format('n');
		$year = (int) $date->format('Y');
		$datePattern = sprintf('/\s*0?%s\s*\.\s*0?%s\s*\.\s*%s\s*$/u', $day, $month, $year);

		$html = file_get_contents(self::MENU_URL);

		$crawler = new Crawler($html);
		$crawler = $crawler
			->filter('#textbox_stred > strong')
			->reduce(function (Crawler $node, int $i) use ($datePattern): bool {
				return (bool) Strings::match($node->text(), $datePattern);
			});

		$nodes = [];
		$crawler->filterXPath('//strong/following-sibling::node()')->each(function (Crawler $node, int $i) use (&$nodes) {
			$nodes[] = $node;
		});

		$menuLines = [];
		foreach ($nodes as $i => $node) {
			assert($node instanceof Crawler);

			if ($node->nodeName() === '#comment') {
				continue;
			}

			// stop on double <br>
			if ($node->nodeName() === 'br' && isset($nodes[$i-1]) && $nodes[$i-1]->nodeName() === 'br') {
				break;
			}

			$text = trim($node->text());
			if ($text !== '') {
				$menuLines[] = $text;
			}
		}

		$option = [
			'id' => 1,
			'text' => implode("\n", $menuLines),
			'price' => 109,
			'alergens' => null,
			'quantity' => null,
		];

		return [
			'url' => self::MENU_URL,
			'options' => [$option],
			'soups' => [

			],
		];
	}
}
