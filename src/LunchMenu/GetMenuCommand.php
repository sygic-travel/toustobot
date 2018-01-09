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
use Toustobot\LunchMenu\MenuCrawler\ZelenaKockaMenuCrawler;


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
			new HelanMenuCrawler(),
			new PlzenskyDvurMenuCrawler(),
			new SelepkaMenuCrawler(),
			new SonoMenuCrawler(),
			new ZelenaKockaMenuCrawler(),
		];


		// get & format menus
		$formatter = new MenuFormatter();
		$formattedMenus = $formatter->formatHeader($now) . "\n\n";

		foreach ($menuCrawlers as $menuCrawler) {
			assert($menuCrawler instanceof IMenuCrawler);

			$name = $menuCrawler->getName();
			$url = $menuCrawler->getUrl();

			try {
				// try to load the menu twice (sometimes we get random web/network errors)
				try {
					$menu = $menuCrawler->getMenu($now);
				} catch (\Throwable $e) {
					sleep(1);
					$menu = $menuCrawler->getMenu($now);
				}
				$formattedMenus .= $formatter->formatMenuHeader($name, $url);
				$formattedMenus .= $formatter->formatMenuBody($menu) . "\n";
			} catch (\Throwable $e) {
				$formattedMenus .= $formatter->formatMenuHeader($name, $url);
				$formattedMenus .= "_Nepodařilo se načíst menu._\n\n";
			}
		}

		echo $formattedMenus;

		// notify Slack
		$slackUrl = $input->getOption('slack-url');
		if ($slackUrl) {
			$slackNotifier = new SlackNotifier($slackUrl);
			$slackNotifier->notify($formattedMenus);
		}
	}
}
