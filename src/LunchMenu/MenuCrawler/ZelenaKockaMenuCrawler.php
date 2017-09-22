<?php

namespace Toustobot\LunchMenu\MenuCrawler;

use Toustobot\LunchMenu\IMenuCrawler;
use Symfony\Component\DomCrawler\Crawler;
use Toustobot\Utils\Matcher;


class ZelenaKockaMenuCrawler implements IMenuCrawler
{
	private const MENU_URL = 'http://www.zelenakocka.cz/index.php';


	public function getMenu(\DateTimeInterface $date): array
	{
		$html = file_get_contents(self::MENU_URL);

		$crawler = new Crawler($html);
		$crawler = $crawler
			->filter('#textbox_stred > strong')
			->reduce(function (Crawler $node, int $i) use ($date): bool {
				return Matcher::matchesDate($date, $node->text());
			});

		$nodes = [];
		$crawler->filterXPath('//strong/following-sibling::node()')->each(function (Crawler $node, int $i) use (&$nodes) {
			if ($node->nodeName() === '#comment') {
				return;
			}

			if ($node->nodeName() === '#text' && trim($node->text()) === '') {
				return;
			}

			$nodes[] = $node;
		});

		$menuLines = [];
		foreach ($nodes as $i => $node) {
			assert($node instanceof Crawler);

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
