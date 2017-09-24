<?php

namespace Toustobot\LunchMenu;


class MenuOption
{
	/** @var string */
	private $id;

	/** @var string */
	private $text;

	/** @var float|null */
	private $price;

	/** @var string|null */
	private $quantity;

	/** @var string|null */
	private $allergens;


	public function __construct(string $id, string $text)
	{
		$this->id = $id;
		$this->text = $text;
	}


	public function getId(): ?string
	{
		return $this->id;
	}


	public function getText(): ?string
	{
		return $this->text;
	}


	public function getPrice(): ?float
	{
		return $this->price;
	}


	public function setPrice(?float $price)
	{
		$this->price = $price;
	}


	public function getQuantity(): ?string
	{
		return $this->quantity;
	}


	public function setQuantity(?string $quantity)
	{
		$this->quantity = $quantity;
	}


	public function getAllergens(): ?string
	{
		return $this->allergens;
	}


	public function setAllergens(?string $allergens)
	{
		$this->allergens = $allergens;
	}
}
