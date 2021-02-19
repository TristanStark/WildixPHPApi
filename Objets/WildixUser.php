<?php

namespace Copilote\Communication\Wildix\Objets;


/**
 * Class WildixUser
 * @method applyPersonnalInfo($data) - Applique les données reçues
 * @method call() - Appelle ledit user
 * @method sendSMS($body) - Envoie un SMS
 */
class WildixUser
{
		// Les données personnelles qu'on récupère sur la personne

		/**
		 * @var string
		 */
		public $dn = '';

		/**
		 * @var string
		 */
		public $id = '';

		/**
		 * @var string
		 */
		public $name = '';

		/**
		 * @var string
		 */
		public $login = '';

		/**
		 * @var string
		 */
		public $extension = '';

		/**
		 * @var string
		 */
		public $officePhone = '';

		/**
		 * @var string
		 */
		public $mobilePhone = '';

		/**
		 * @var string
		 */
		public $faxNumber = '';

		/**
		 * @var string
		 */
		public $email = '';

		/**
		 * @var string
		 */
		public $pbxDn = '';

		/**
		 * @var string
		 */
		public $role = '';

		/**
		 * @var string
		 */
		public $groupDn = '';

		/**
		 * @var string
		 */
 		public $language = '';

		/**
		 * @var string
		 */
		public $dialplan = '';

		/**
		 * @var string
		 */
		public $faxDialplan = '';

		/**
		 * @var string
		 */
		public $department = '';

		/**
		 * @var string
		 */
		public $picture = '';

		/**
		 * @var string
		 */
		public $sourceId = '';

		/**
		 * @var string
		 */
		public $licenseType = '';

		/**
		 * @var string
		 */
		public $jid = '';

		/**
		 * @var string
		 */
		public $sipPassword = '';

		public function __construct($wildix)
		{
			$this->wildix = $wildix;
		}

		public function applyPersonnalInfo($data)
		{

	     	foreach ($data as $variable => $value)
           		$this->$variable = $value;
			return $this;
		}

		public function call()
		{
			$this->wildix->call($this->extension);
			return $this;
		}

		public function sendSMS($body)
		{
			$this->wildix->sendSMS($this->mobilePhone, $body);
			return $this;
		}
}