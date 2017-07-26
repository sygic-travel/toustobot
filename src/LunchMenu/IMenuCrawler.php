<?php

namespace Toustobot\LunchMenu;


interface IMenuCrawler
{
	public function getMenu(\DateTimeInterface $date): array;
}
