<?php

namespace Copilote\Communication\Wildix\Objets;

use Copilote\Communication\Wildix\Objets\WildixCallQueues;

/**
 * Class ListCallQueues
 * @method findById($id) - Trouve le CallQueues correspondant à l'ID
 * @method findByName($name) - Trouve le CallQueues correspondant au nom
 * @method findByDescription($description) - Trouve le CallQueues correspondant à la description
 */
class ListCallQueues
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
	 * findById($id) -> Renvoie un callqueue correspondant à l'ID correspondant
	 * @params $id - L'ID du callqueue
	 * @return \Copilote\Communication\Wildix\Objets\WildixCallQueues
	 */
	public function findById($id)
	{
		foreach ($this->data["result"]["records"] as $callqueue)
		{
			if ($callqueue["id"] == $id)
			{
				return new WildixCallQueues($callqueue, $this->wildix);
			}
		}
	}

	/**
	 * findByName($name) -> Renvoie un callqueue correspondant au nom correspondant
	 * @params $name - Le nom du callqueue
	 * @return \Copilote\Communication\Wildix\Objets\WildixCallQueues
	 */
 	public function findByName($name)
	{
			foreach ($this->data["result"]["records"] as $callqueue)
			{
				if ($callqueue["name"] == $name)
				{
					return new WildixCallQueues($callqueue, $this->wildix);
				}
			}
	}
}
