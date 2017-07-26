<?php

namespace Toustobot\LunchMenu;

use Toustobot\LunchMenu\MenuCrawler\HelanMenuCrawler;
use Toustobot\LunchMenu\MenuCrawler\PlzenskyDvurMenuCrawler;
use Toustobot\LunchMenu\MenuCrawler\SelepkaMenuCrawler;
use Toustobot\LunchMenu\MenuCrawler\SonoMenuCrawler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class GetMenuCommand extends Command
{
	protected function configure()
	{
		$this
			->setName('get-menu')
			->addOption('slack-url', null, InputOption::VALUE_REQUIRED, 'Slack webhook URL to send lunch menus');
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$now = new \DateTime();

		$menuCrawlers = [
			'Helan' => new HelanMenuCrawler(),
			'Plzeňský dvůr' => new PlzenskyDvurMenuCrawler(),
			'Šelepka' => new SelepkaMenuCrawler(),
			'Sono' => new SonoMenuCrawler(),
		];


		// crawl menus
		$menus = [];
		foreach ($menuCrawlers as $name => $menuCrawler) {
			assert($menuCrawler instanceof IMenuCrawler);
			$menus[$name] = $menuCrawler->getMenu($now);
		}

		// format & output
		$formattedMenus = (new MenuFormatter())->format($now, $menus);
		echo $formattedMenus;

		// notify Slack
		$slackUrl = $input->getOption('slack-url');
		if ($slackUrl) {
			$slackNotifier = new SlackNotifier($slackUrl);
			$slackNotifier->notify($formattedMenus);
		}
	}
}
