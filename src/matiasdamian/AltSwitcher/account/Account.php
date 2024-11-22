<?php

declare(strict_types=1);

namespace matiasdamian\AltSwitcher\account;

class Account{
	private string $username;
	private string $xuid;
	private int $last_login;
	
	/**
	 * Account constructor.
	 *
	 * Initializes an Account with a username, XUID, and the last login timestamp.
	 *
	 * @param string $username The username of the account.
	 * @param string $xuid The XUID (Xbox unique ID) of the account.
	 * @param int $last_login The timestamp of the account's last login.
	 */
	public function __construct(string $username, string $xuid, int $last_login){
		$this->username = $username;
		$this->xuid = $xuid;
		$this->last_login = $last_login;
	}
	
	/**
	 * Get the username of the account.
	 *
	 * @return string The username of the account.
	 */
	public function getUsername(): string{
		return $this->username;
	}
	
	/**
	 * Get the XUID of the account.
	 *
	 * @return string The XUID of the account.
	 */
	public function getXuid(): string{
		return $this->xuid;
	}
	
	/**
	 * Get the last login timestamp of the account.
	 *
	 * @return int The last login timestamp.
	 */
	public function getLastLogin(): int{
		return $this->last_login;
	}
	
	/**
	 * Set the XUID of the account.
	 *
	 * @param string $xuid The new XUID to set for the account.
	 */
	public function setXuid(string $xuid): void{
		$this->xuid = $xuid;
	}
	
	/**
	 * Updates the last login time.
	 *
	 * @return void
	 */
	public function updateLastLogin() : void{
		$this->last_login = time();
	}
	
	/**
	 * Convert the account data into an array.
	 *
	 * @return array An array representation of the account.
	 */
	public function asArray(): array{
		return [
			"username" => $this->getUsername(),
			"xuid" => $this->getXuid(),
			"last_login" => $this->getLastLogin(),
		];
	}
}