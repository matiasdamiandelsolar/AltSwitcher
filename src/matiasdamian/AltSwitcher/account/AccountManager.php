<?php

declare(strict_types=1);

namespace matiasdamian\AltSwitcher\account;

use pocketmine\utils\Config;
use pocketmine\player\Player;

use matiasdamian\AltSwitcher\Main;

class AccountManager{
	/** @var AccountGroup[] */
	private array $accountGroups = [];
	
	/** @var Config|null */
	private ?Config $config;
	
	/** @var string[] */
	private array $activeUsernames = [];
	
	/** @var string[] */
	private array $groupRequests = [];
	
	/**
	 * AccountManager constructor.
	 *
	 * @param Main $plugin The plugin instance.
	 */
	public function __construct(Main $plugin){
		$this->config = new Config($plugin->getDataFolder() . "data.yml", Config::YAML);
		$this->loadGroups();
	}
	
	/**
	 * Loads all account groups from the configuration file.
	 */
	private function loadGroups(): void{
		foreach($this->config->getAll() as $index => $groupAccounts){
			$this->accountGroups[$index] = new AccountGroup();
			
			foreach($groupAccounts as $account){
				$this->accountGroups[$index]->groupAccount(
					$account["username"],
					$account["xuid"],
					$account["last_login"]
				);
			}
		}
	}
	
	/**
	 * Get all account groups.
	 *
	 * @return AccountGroup[] The list of account groups.
	 */
	public function getAllGroups(): array{
		return $this->accountGroups;
	}
	
	/**
	 * Save all account groups.
	 */
	public function save(): void{
		$data = [];
		
		
		foreach($this->accountGroups as $index => $group){
			if(count($group->getAccounts()) < 1){
				continue;
			}
			$data[$index] = array_map(fn($account) => $account->asArray(), $group->getAccounts());
		}
		
		$this->config->setAll($data);
		$this->config->save();
	}
	
	/**
	 * Get the account group for a given username.
	 *
	 * @param string $username The username to search for.
	 * @return AccountGroup|null The account group, or null if not found.
	 */
	public function getAccountGroup(string $username): ?AccountGroup{
		foreach($this->accountGroups as $group){
			if($group->isInGroup($username)){
				return $group;
			}
		}
		
		return null;
	}
	
	/**
	 * Remove a username from all account groups.
	 *
	 * @param string $username The username to ungroup.
	 * @return bool True if the account was removed, false otherwise.
	 */
	public function ungroupAccount(string $username): bool{
		foreach($this->accountGroups as $group){
			if($group->ungroupAccount($username)){
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Group two accounts together.
	 *
	 * @param string $usernameA The first username.
	 * @param string $usernameB The second username.
	 * @param string $xuid The XUID of the second username.
	 * @return bool True if the accounts were grouped, false if the first username is not in any group.
	 */
	public function groupAccount(string $usernameA, string $usernameB, string $xuid): bool{
		$group = $this->getAccountGroup($usernameA);
		
		if($group !== null){
			$group->groupAccount($usernameB, $xuid);
			return true;
		}
		
		return false;
	}
	
	/**
	 * Register a player to an account group.
	 * Will not register the player if it is already in another group.
	 *
	 * @param Player $player The player to register.
	 */
	public function registerPlayer(Player $player): void{
		$xuid = $player->getXuid();
		$username = $player->getName();
		$accountGroup = $this->getAccountGroup($username);
		
		if($accountGroup === null){
			$group = new AccountGroup();
			$group->groupAccount($username, $xuid);
			$this->accountGroups[] = $group;
		}else{
			foreach($accountGroup->getAccounts() as $account){
				if(strcasecmp($account->getUsername(), $username) === 0){
					if($account->getXuid() !== $xuid){
						$account->setXuid($xuid);
					}
				}
			}
		}
	}
	
	/**
	 * Get the active username for a given UUID.
	 *
	 * @param string $uuid The UUID to check.
	 * @return string|null The active username, or null if not found.
	 */
	public function getActiveUsername(string $uuid): ?string{
		return $this->activeUsernames[$uuid] ?? null;
	}
	
	/**
	 * Set the active username for a given UUID.
	 *
	 * @param string $uuid The UUID to associate with a username.
	 * @param string $username The username to associate.
	 * @return bool True if the username was set, false if the same username is already active.
	 */
	public function setActiveUsername(string $uuid, string $username): bool{
		if(!isset($this->activeUsernames[$uuid])){
			$this->activeUsernames[$uuid] = $username;
			return true;
		}
		
		if($this->activeUsernames[$uuid] === $username){
			return false;
		}
		
		$this->activeUsernames[$uuid] = $username;
		return true;
	}
	
	public function removeActiveUsername(string $username): bool{
		$uuid = array_search($username, $this->activeUsernames);
		if($uuid !== false){
			unset($this->activeUsernames[$uuid]);
			return true;
		}
		return false;
	}
	
	/**
	 * @param string $username
	 * @return void
	 */
	public function removeGroupRequest(string $username) : void{
		$usernameB = array_search($username, $this->groupRequests);
		if($usernameB !== false){
			unset($this->groupRequests[$usernameB]);
		}
	}
	
	/**
	 * @param string $usernameA
	 * @param string $usernameB
	 * @return void
	 */
	public function setGroupRequest(string $usernameA, string $usernameB): void{
		$this->groupRequests[$usernameA] = strtolower($usernameB);
	}
		
		/**
		 * @param string $usernameB
		 * @return string|null
		 */
		public function getGroupRequest(string $usernameB): ?string{
			return $this->groupRequests[$usernameB] ?? null;
		}
	
	/**
	 * @param string $usernameB
	 * @return string|false
	 */
		public function hasGroupRequest(string $usernameB): bool|string{
			if(isset($this->groupRequests[$usernameB])){
				return $this->groupRequests[$usernameB];
			}
			return false;
		}
	
}