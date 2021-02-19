<?php

namespace Copilote\Communication\Wildix\Objets;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Copilote\Copilote;
use Copilote\OptionsTrait;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;
use Amp\Loop;


/**
 * Class WildixDialplans
 * @method getId() - return l'ID
 * @method findById($id) - 
 */
class WildixDialplans
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
	 * getId() -> Renvoie l'Id du dialplan
	 */
	public function getId()
	{
			$this->id = $this->data["id"];
			return $this->id;
	}


	/**
	 * delete() -> Delete ledit dialplan.
	 */
	public function delete()
	{
		$this->wildix->deleteSpecificDialplan($this->getId());
	}


	/**
	 * update($description, $arrayOfNumbers, $arrayofIncludedDialplans) -> Update le dialplan
	 * @param string $description -> La description du dialplan
	 * @param array $arrayOfNumbers -> Un tableau correspondant aux numéros que l'on doit ajouter dans le dialplan / Peut être vide
	 * @param array $arrayOfIncludedDialplans -> Un tableau correspondant aux dialplans que l'on doit ajouter dans le dialplan / Peut être vide
	 * @return \Copilote\Communication\Wildix\Objets\WildixDialplans
	 */
	public function update($description, $arrayOfNumbers, $arrayofIncludedDialplans)
	{
		$this->wildix->updateDialplan($this->getId(), $description, $arrayOfNumbers, $arrayofIncludedDialplans);
		return $this->wildix->getSpecificDialplan($this->getId());
	}
}
