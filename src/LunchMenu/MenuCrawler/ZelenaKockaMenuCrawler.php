<?php

namespace Toustobot\LunchMenu\MenuCrawler;

use Toustobot\LunchMenu\IMenuCrawler;
use Symfony\Component\DomCrawler\Crawler;
use Toustobot\LunchMenu\MenuOption;
use Toustobot\Utils\Matcher;


class ZelenaKockaMenuCrawler implements IMenuCrawler
{
	private const NAME = 'Zelená Kočka';
	private const URL = 'http://www.zelenakocka.cz/index.php';


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

		$option = new MenuOption(1, implode("\n", $menuLines));
		$option->setPrice(109);

		return [
			'url' => self::URL,
			'options' => [$option],
			'soups' => [

			],
		];
	}
}
