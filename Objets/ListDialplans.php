<?php

namespace Copilote\Communication\Wildix\Objets;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Copilote\Copilote;
use Copilote\OptionsTrait;

use Copilote\Communication\Wildix\Objets\WildixDialplans;

/**
 * Class ListDialplans
 * @method findById($id) - Trouve le dialplan correspondant à l'ID
 * @method findByName($name) - Trouve le dialplan correspondant au nom
 * @method findByDescription($description) - Trouve le dialplan correspondant à la description
 */
class ListDialplans
{
	/*
	 * $data Doit être sous format d'array comprenant l'id, le name et la description
	 */
	public function __construct($data, $wildixAPI)
	{
		$this->wildix = $wildixAPI;
		$this->data = $data;
		$this->id = False;
	}

	/**
	 * findById($id) -> Renvoie un dialplan correspondant à l'ID correspondant
	 * @params $id - L'ID du dialplan
	 * @return \Copilote\Communication\Wildix\Objets\WildixDialplans
	 */
	public function findById($id)
	{
		foreach ($this->data["result"]["records"] as $dialplan)
		{
			if ($dialplan["id"] == $id)
			{
				return new WildixDialplans($dialplan, $this->wildix, False);
			}
		}
		return False;
	}

	/**
	 * findByName($name) -> Renvoie un dialplan correspondant au nom correspondant
	 * @params $name - Le nom du dialplan
	 * @return \Copilote\Communication\Wildix\Objets\WildixDialplans
	 */
 	public function findByName($name)
	{
		foreach ($this->data["result"]["records"] as $dialplan)
		{
			if ($dialplan["name"] == $name)
			{
				return new WildixDialplans($dialplan, $this->wildix, False);
			}
		}
		return False;
	}

	/**
	 * findByDescription($description) -> Renvoie un dialplan correspondant à la description correspondant
	 * @params $description - La description du dialplan
	 * @return \Copilote\Communication\Wildix\Objets\WildixDialplans
	 */	
	public function findByDescription($description)
	{
		foreach ($this->data["result"] as $dialplan)
		{
			if ($dialplan["description"] == $description)
			{
				return new WildixDialplans($dialplan, $this->wildix, False);
			}
		}
		return False;
	}
}
