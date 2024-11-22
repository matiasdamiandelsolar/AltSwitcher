<?php

declare(strict_types=1);

namespace matiasdamian\AltSwitcher\account;

class AccountGroup{
	/** @var Account[] */
	private array $accounts = [];
	
	/**
	 * AccountGroup constructor.
	 *
	 * Initializes an empty account group.
	 */
	public function __construct(){
		//NOOP
	}
	
	/**
	 * Get the list of accounts in the group.
	 *
	 * @return Account[] The list of accounts.
	 */
	public function getAccounts(): array{
		return $this->accounts;
	}
	
	/**
	 * Check if an account is in the group by username.
	 *
	 * @param string $username The username to check.
	 * @return bool True if the account is in the group, false otherwise.
	 */
	public function isInGroup(string $username): bool{
		return (bool) array_filter($this->accounts, fn($account) => strcasecmp($account->getUsername(), $username) === 0);
	}
	
	/**
	 * Remove an account from the group by username.
	 *
	 * @param string $username The username of the account to ungroup.
	 * @return bool True if the account was removed, false otherwise.
	 */
	public function ungroupAccount(string $username): bool{
		if(count($this->accounts) < 1){
			return false;
		}
		
		foreach($this->accounts as $index => $account){
			if(strcasecmp($account->getUsername(), $username) === 0){
				unset($this->accounts[$index]);
				$this->accounts = array_values($this->accounts);
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Add an account to the group.
	 *
	 * @param string $username The username of the account to add.
	 * @param string $xuid The XUID of the account to add.
	 * @param int $last_login The last login timestamp of the account.
	 * @return bool True if the account was added, false if already in the group.
	 */
	public function groupAccount(string $username, string $xuid, int $last_login = 0): bool{
		if($this->isInGroup($username)){
			return false;
		}
		if($last_login === 0){
			$last_login = time();
		}
		$this->accounts[] = new Account($username, $xuid, $last_login);
		return true;
	}
	
	/**
	 * @param string $username
	 * @return array
	 */
	public function exempt(string $username) : array{
		$accounts = [];
		foreach($this->accounts as $account){
			if(strcasecmp($account->getUsername(), $username) !== 0){
				$accounts[] = $account;
			}
		}
		return $accounts;
	}
	
}