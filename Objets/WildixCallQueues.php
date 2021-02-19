<?php

namespace Copilote\Communication\Wildix\Objets;


/**
 * Class WildixCallQueues
 * @method getId() - return l'ID
 * @method delete() - delete la CallQueue
 * @method addDynamicMember($member) - Ajoute un membre dynamiquement
 * @method removeDynamicMember($member) - Enlève un membre dynamiquement 
 */
class WildixCallQueues
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
	 * getId() -> Renvoie l'Id du callqueue
	 */
	public function getId()
	{
		$this->id = $this->data["id"];
		return $this->id;
	}

	/**
	 * delete() -> Delete ledit callqueue.
	 */
	public function delete()
	{
		$this->wildix->deleteSpecificCallQueue($this->getId());
	}

	/**
	 * addDynamicMember($member) - Ajoute dynamiquement $member à la CallQueue
	 * @params $member - Le membre à ajouter
	 */
	public function addDynamicMember($member)
	{
		$this->wildix->addMemberFromCallQueue($this->getId(), $member);
		return $this;
	}

	/**
	 * removeDynamicMember($member) - Enlève dynamiquement $member à la CallQueue
	 * @params $member - Le membre à enlever
	 */
	public function removeDynamicMember($member)
	{
		$this->wildix->removeDynamicMemberCallQueue($this->getId(), $member);		
		return $this;
	}
}
