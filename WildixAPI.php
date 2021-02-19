<?php

use Copilote\Communication\Wildix\Objets\WildixDialplans;
use Copilote\Communication\Wildix\Objets\WildixUser;
use Copilote\Communication\Wildix\Objets\ListCallQueues;
use Copilote\Communication\Wildix\Objets\ListDialplans;
use \Wildix\Integrations\Client;
/**
 * Class WildixAPI
 * @method getPersonnalInfo($id) - Trouve le CallQueues correspondant à l'ID
 * @method sendSMS($number, $message) - Envoie un message contenant $message à $number
 * @method call($number) - Appelle $number
 * @method getPersonnalCallHistory() - Renvoie l'historique d'appel personnel
 * @method getUserCallHistory($userExtension) - Renvoie l'historique d'appel de $userExtension
 * @method getGlobalCallHistory() - Renvoie l'historique d'appel global
 * @method getListOfDialplans() - Renvoie la liste de tout les dialplans
 * @method createNewDialplan($name, $description, $numbers) - Créé un new dialplan
 * @method getSpecificDialplan($id) - Renvoie l e dialplan correspondant à $id
 * @method deleteSpecificDialplan($id) - Supprime le dialplan correspondant
 * @method updateDialplan($id, $description, $arrayOfNumbers, $arrayofIncludedDialplans) - Update le dialplan $id avec les paramètres
 * @method getListOfPagingGroups() - Renvoie la liste de tout les paging groups
 * @method dialplanUpdateGenerealSettings() - Update les général settings du dialplan
 * @method getListCallQueue() - Renvoie une liste de toutes les callQueues
 * @method getCallQueue($idCallQueue) -
 * @method addMemberFromCallQueue() -
 * @method removeDynamicMemberCallQueue() -
 * @method getListOfColleagues() -
 * @method getSpecificColleague() -
 * @method configure() -
 * @method connect() -
 */
class WildixAPI
{
	public $__WILDIX__API__OK_RESPONSE__ = "ok";

	public function __construct($PBX_url, $username, $password)
	{
		// Les datas utiles
		$this->login = "";
		$this->password = "";
		$this->user = new WildixUser($this);

		$this->options = [
			"url" => $PBX_url,
			"password" => $password,
			"username" => $username,
		];


		// Les URL pour les différentes fonctions des API
		$this->connect_url = '/api/v1/personal/login';
		$this->personal_info_url = '/api/v1/personal/info';
		$this->pbx_call_queue_url = '/api/v1/PBX/settings/CallQueues/';
		$this->call_url = '/api/v1/Calls/';
		$this->dialplan_list_url = '/api/v1/PBX/Dialplans/';
		$this->list_colleagues_url = '/api/v1/Colleagues/';
		$this->dialplan_create_url = '/api/v1/PBX/Dialplans/';
		$this->specific_dialplan_url = '/api/v1/PBX/Dialplans/';
		$this->get_paging_group_url = '/api/v1/Dialplan/PagingGroups/';
		$this->global_call_history_url = '/api/v1/PBX/CallHistory/';
		$this->call_history_url = '/api/v1/CallHistory/';
		$this->dialplan_update_general_settings_url = '/api/v1/Dialplan/GeneralSettings/';
		$this->sms_url = '/api/v1/originate/sms';

		$this->client = null;
		$this->configure($this->options["url"], $this->options["username"], $this->options["password"]);
	}

	/* *******************************************************************************************************
	**************************************** GESTION DE L'UTILISATEURS ***************************************
	*********************************************************************************************************/


	/**
	 * getPersonnalInfo() -> Récupère les informations personnelles de l'utilisateur connecté
	 */
	public function getPersonnalInfo()
	{
			$response = $this->client->get($this->personal_info_url, []);
			$personnal_info = json_decode($response->getBody()->getContents(), true);
			$this->user->applyPersonnalInfo($personnal_info);
			return $this->user;
	}
	/* *******************************************************************************************************
	**************************************** LES SMS *********************************************************
	*********************************************************************************************************/

	/**
	 * sendSMS($numer, $message) - Envoie un SMS. (kilucru)
	 * @params $number - Le numéro auquel envoyer un SMS
	 * @params $message - Le contenu du message à envoyer
	 */
	private function _sendSMS($number, $message)
	{
		$this->client->post($this->sms_url, [
			'params' => [
				'number' => $number,
				'message' => $message,
			],
		]);
	}

	/**
	 * sendSMS($numer, $message) - Envoie un SMS. (kilucru)
	 * @params $number - Le numéro auquel envoyer un SMS
	 * @params $message - Le contenu du message à envoyer
	 */
	public function sendSMS($number, $message)
	{
		$j = 0;
		$tailleBloc = 300;
		while (($j * $tailleBloc) < strlen($message))
		{
			$this->_sendSMS($number, substr($message, ($j * $tailleBloc)));
			$j++;
		}
	}


	/* *******************************************************************************************************
	**************************************** LES APPELS ******************************************************
	*********************************************************************************************************/

	/**
	 * call($number) -> Appelle le numéro
	 * $number -> Le numéro à appeler.
	 */
	public function call($number)
	{
		$this->client->post($this->call_url, [
			'params' => [
				'number' => $number,
			],
		]);
	}

	/**
	 * getPersonnalCallHistory() -> Récupère l'historique des derniers appels de l'utilistaru /!\ BUGGUÉ, NE RÉCUPÈRE QUE LES 100 DERNIERS /!\
	 * @return array
	 */
	public function getPersonnalCallHistory()
	{
		$response = $this->client->get($this->call_history_url, []);
		$list_of_dialplans = json_decode($response->getBody()->getContents(), true);
		return $list_of_dialplans;
	}

	/**
	 * getUserCallHistory($userExtension) -> Récupère l'historique des derniers appels de l'utilsiateur /!\ BUGGUÉ, NE RÉCUPÈRE QUE LES 100 DERNIERS /!\
	 * @params $userExtension -> l'extension de l'utilisateur (le numéro à 3 chiffres qui permet de l'identifier)
	 * @return array
	 */
	public function getUserCallHistory($userExtension)
	{
		$response = $this->client->get('api/v1/User/' . $userExtension . '/CallHistory/', []);
		$list_of_dialplans = json_decode($response->getBody(), true);
		return $list_of_dialplans;
	}

	/**
	 * getGlobalCallHistory() -> Récupère l'historique des derniers appels /!\ BUGGUÉ, NE RÉCUPÈRE QUE LES 100 DERNIERS /!\
	 * @param count => le nombre d'appel à récupérer: cappé à 100.
	 * @return array
	 */
	public function getGlobalCallHistory($count)
	{
		$response = $this->client->get($this->global_call_history_url, ['query' => ['count' => $count]]);
		// echo $response->getBody();
		$list_of_calls = json_decode($response->getBody()->getContents(), true);
		return $list_of_calls;
	}

	/* *******************************************************************************************************
	**************************************** GESTION DES DIALPLANS *******************************************
	*********************************************************************************************************/

	/**
	 * getListOfDialplans -> Renvoie une liste contenant tout les dialplasn existants
	 * @return Copilote\Communication\Wildix\Objets\Dialplan;
	 */
	public function getListOfDialplans()
	{
		$response = $this->client->get($this->dialplan_list_url, []);
		$list_of_dialplans = json_decode($response->getBody()->getContents(), true);
		return new ListDialplans($list_of_dialplans, $this);
	}

	/**
	 * C'est privé!
	 * Récupère le dernier dialplan existant
	 * @return Copilote\Communication\Wildix\Objets\Dialplan;
	 */
	private function _getLastDialplan()
	{
		$list_of_dialplans = $this->getListOfDialplans();
		$goodDialplan = False;
		foreach ($list_of_dialplans->data["result"]["records"] as $dialplan)
		{
			$goodDialplan = $dialplan;
			if ($dialplan["id"] > $goodDialplan["id"])
			{
				$goodDialplan = $dialplan;
			}
		}
		return new WildixDialplans($goodDialplan, $this);
	}


	/**
	 * createNewDialplan($name, $description, $numbers) -> Créé un dialplan avec les données sus nommées
	 * @params $id ->  Id du dialplan a get
	 * @return Copilote\Communication\Wildix\Objets\Dialplan;
	 */
	public function createNewDialplan($name, $description)
	{
		$response = $this->client->post($this->dialplan_create_url, [
			'params' => [
				'name' => $name,
				'description' => $description,
			],
		]);
		$response;
		return $this->_getLastDialplan();
	}

	/**
	 * getSpecificDialplan($id) -> Renvoie le dialplan spécifique correspondant à l'id
	 * @params $id ->  Id du dialplan a get
	 * @return Copilote\Communication\Wildix\Objets\Dialplan;
	 */
	public function getSpecificDialplan($id)
	{
		$response = $this->client->get($this->specific_dialplan_url . $id . '/', []);
		$specific_dialplan = json_decode($response->getBody()->getContents(), true);
		return new WildixDialplans($specific_dialplan, $this, False);
	}

	/**
	 * deleteSpecificDialplan($id) -> Supprime le dialplan correspondant à l'$id
	 * @params id - L'Id du dialplan a supprimer
	 */
	public function deleteSpecificDialplan($id)
	{
		$response = $this->client->delete($this->specific_dialplan_url . $id . '/', []);
		$response;
		return $this->__WILDIX__API__OK_RESPONSE__;
	}


	/**
	 * updateDialplan($id, $description, $arrayOfNumbers, $arrayofIncludedDialplans) -> Update le dialplan
	 * @param $id -> L'ID du dialplan que l'on doit update
	 * @param string $description -> La description du dialplan
	 * @param array $arrayOfNumbers -> Un tableau correspondant aux numéros que l'on doit ajouter dans le dialplan / Peut être vide
	 * @param array $arrayOfIncludedDialplans -> Un tableau correspondant aux dialplans que l'on doit ajouter dans le dialplan / Peut être vide
	 * @return $this->__WILDIX__API__OK_RESPONSE__;
	 */
	public function updateDialplan($id, $description, $arrayOfNumbers, $arrayofIncludedDialplans)
	{
		$response = $this->client->put('PUT', $this->specific_dialplan_url . $id . '/', [
			'description' => $description,
			'numbers' => $arrayOfNumbers,
			'includedDialplans' => $arrayofIncludedDialplans
		]);
		$response;
		return $this->__WILDIX__API__OK_RESPONSE__;

	}

	/* *******************************************************************************************************
	*********************************** GESTION DES DIALPLANS PAGING *****************************************
	*********************************************************************************************************/


	/**
	 * getListOfPagingGroups -> Renvoie la liste de toutes les Paging Groups
	 * Les paging groups sont des groupes permettant de diffuser des messages d'urgence, pour prévenir d'évènement grave tels
	 * que, par exemple, des attaques terroristes ou incendies. Ces messages sont diffusés par haut parleur sur les casques
	 * des postes correspondants.
	 * /!\ À UTILISER AVEC PRÉCAUTION /!\
	 * @return arrray $list_of_dialplans
	 */
	public function getListOfPagingGroups()
	{
		$response = $this->client->get($this->get_paging_group_url, []);
		$list_of_dialplans = json_decode($response->getBody()->getContents(), true);
		return $list_of_dialplans;
	}

	/* *******************************************************************************************************
	******************************* GESTION DES DIALPLANS GENERAL SETTINGS ***********************************
	*********************************************************************************************************/

	/**
	 * Update general settings
	 *
	 *	Update list of all available dialplan general settings on the PBX.
	 *
	 * @param array associatif: /!\ Important: voici les paramètres de cet $array
	 * 	 - faxLang => string : A language code. Example: EN, IT, FR, DE, RN, BG, ES, NL, EN-US, EE
	 *   - parkTimeout	=> integer [ 0 .. 32767 ] : Timeout (secs).
	 *   - prefixExtLine => string (>= 0 characters) : A numeric string.
	 *   - internationalPrefix => string (>= 0 characters) : A numeric string.
	 *   - nationalPrefix => string (>= 0 characters) : A numeric string.
	 *   - intDialTimeout => string [ 10 .. 600 ] characters : A numeric string.
	 *   - transferDigitTimeout [ @required ] => string (>= 0 characters) : A numeric string.
	 *   - transferInterDigitTimeout [ @required ] => string [ 1 .. 12 ] characters : A numeric string.
	 *   - endOfDigit => string : Enum: 0 1 2 : 0 - "None", 1 - "*", 2 - "#".
	 *   - buttonSound => boolean : Enum: true false : Playback tones while entering number.
	 *   - attachFormatVm => string: Enum: "mp3" "wav" : Quality of recorded Voicemails.
	 *   - attachFormatRec => string: Enum: "mp3" "wav" : Quality of calls recordings.
	 *   - recNotify => boolean: Enum: true false : Send mail notification after the record is complete.
	 *   - recAttach => boolean: Enum: true false : Attach files with records to emails.
	 *   - describeVoiceMail => boolean: Enum: true false : Convert Voicemails to text and send by email.
	 *   - trunkRegistrationNotification => boolean: Enum: true false: Notify by email in case SIP trunk registration status is changed.
	 *   - voiceMailMessageMoreInfo => boolean: Enum: true false: Announce date, time and caller phone number for Voicemail messages.
	 *   - ctiConnectCt3PhoneBooks => boolean: Enum: true false: Use CTI connect 3 phonebooks.
	 *   - userVariables => string: Custom user variables. Delimiter - \n (new line). Example: VAR1=VAL1\nVAR2=VAL2
	 *   - quickDialPatterns => string: Set quick dial patterns. Delimiter - \n (new line). Example: 1[0-9][0-9]\n2[0-9][0-9]
	 * Attention! Tout manquement à ce tableau entraînera une exception!
	 * Les paramètres obligatoires sont signalés par un [ @required ]
	 */
	public function dialplanUpdateGenerealSettings($data)
	{
		$response = $this->client->put($this->dialplan_update_general_settings_url, ['params' => ["data" => $data]]);
		$list_of_call_queues = json_decode($response->getBody()->getContents(), true);
		return $list_of_call_queues;
	}


	/* *******************************************************************************************************
	**************************************** GESTION DES CALL QUEUES *****************************************
	*********************************************************************************************************/

	/**
	* Fonctionnement des call queues:
	* A, B, C sont dans une call queue
	* A reçoit un appel mais ne décroche pas
	* L'appel va donc être transféré à B
	* B reçoit l'appel destiné à A mais ne décroche pas
	* L'appel est donc transféré à C
	*/

	/**
	 * getListCallQueue -> Renvoie la liste de toutes les callqueues
	 * @return Copilote\Communication\Wildix\Objets\ListCallQueues
	 */
	public function getListCallQueue()
	{
		$response = $this->client->get($this->pbx_call_queue_url, []);
		$list_of_call_queues = json_decode($response->getBody()->getContents(), true);
		return new ListCallQueues($list_of_call_queues, $this);
	}

	/**
	 * getCallQueue($idCallQueue) -> Renvoie une callqueue spécifique, identifiée par son $id;
	 * @params $id - L'ID de la call queue que l'on doit récupérer
	 * @return $callQueue
	 */
	public function getCallQueue($idCallQueue)
	{
		$response = $this->client->get($this->pbx_call_queue_url . $idCallQueue . '/', []);
		$call_queues = json_decode($response->getBody()->getContents(), true);
		return new ListCallQueues($call_queues, $this);
	}


	/**
	 * addMemberFromCallQueue($idCallQueue, $member)
	 * @params $idCallQueue - l'id call queue de la call queue
	 * @params $member - Le member a rajouter
	 */
	public function addMemberFromCallQueue($idCallQueue, $member)
	{
		$response = $this->client->post($this->pbx_call_queue_url . $idCallQueue . '/' . 'members/dynamic/', [
			'params' => [
				'member' => $member,
			],
		]);
		$response;
	}



	/**
	 * removeDynamicMemberCallQueue($idCallQueue, $member)
	 * @params $idCallQueue - l'id call queue de la call queue
	 * @params $member - Le member a enlever
	 */
	public function removeDynamicMemberCallQueue($idCallQueue, $member)
	{
		$response = $this->client->delete($this->pbx_call_queue_url . $idCallQueue . 'members/dynamic/' . urlencode($member), [
		]);
		$response;
	}

	/* *******************************************************************************************************
	**************************************** GESTION DES COLLÈGUES *******************************************
	*********************************************************************************************************/

	/**
	 * getListOfColleagues -> Renvoie la liste des collègues du compte
	 * @return array of \Copilote\Communication\Wildix\Objets\WildixUser;
	 */
	public function getListOfColleagues()
	{
		$listColleagues = [];
		$response = $this->client->get($this->list_colleagues_url, []);
		$list_of_colleagues = json_decode($response->getBody()->getContents(), true);
		foreach ($list_of_colleagues["result"]["records"] as $informations)
		{
			$temp = new WildixUser($this);
			$temp->applyPersonnalInfo($informations);
			$listColleagues[] = $temp;
		}
		return $listColleagues;
	}

	/**
	 * getSpecificColleague($id) -> Renvoie le collègue correspondant à ID
	 * @return \Copilote\Communication\Wildix\Objets\WildixUser;
	 */
	public function getSpecificColleague($id)
	{
		$response = $this->client->request('GET', $this->list_colleagues_url . $id . '/', [
			'cookies' => $this->jar
		]);
		$list_of_colleagues = json_decode($response->getBody(), true);
		$temp = new WildixUser($this);
		$temp->applyPersonnalInfo($list_of_colleagues);
		return $temp;
	}

	/* *******************************************************************************************************
	********************************* GESTION DES CONFIGURATION PLUS CODE DE BASE ****************************
	*********************************************************************************************************/

	/**
	 * Configure le WildixAPI avec les données suivantes:
	 * @params $base_url : l'url de base du wildix auquel se connecter: par exemple: https://busipolis.wildixin.com
	 * @params $app_id: APP_ID
	 * @params $secret_key: APP_SECRET
	 * @params $app_name: APP_NAME
	 */
	public function configure($base_url, $app_id, $secret_key, $app_name)
	{
		$this->base_url = $base_url;
		$this->app_id = $app_id;
		$this->secret_key = $secret_key;
		$this->app_name = $app_name;
		$this->client = new Client(['host' => $this->base_url, "app_id" => $this->app_id, "secret_key" => $this->secret_key, "app_name" => $this->app_name], []);
	}
}

