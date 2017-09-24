<?php

namespace Toustobot\LunchMenu;


interface IMenuCrawler
{
	public function getName(): string;
	public function getUrl(): string;
	public function getMenu(\DateTimeInterface $date): array;
}
