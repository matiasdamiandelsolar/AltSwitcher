<?php

declare(strict_types=1);

namespace matiasdamian\AltSwitcher;

use pocketmine\utils\Config;

class PluginConfiguration{
	/** @var Main */
	private Main $plugin;
	/** @var Config */
	private readonly Config $config;
	
	/** @var bool */
	private bool $transferOnSwitch = true;
	/** @var string */
	private string $serverIp = "127.0.0.1";
	/** @var int */
	private int $serverPort = 19132;
	/** @var bool */
	private bool $allowUngroup = true;
	/** @var int */
	private int $maxGroupSize = 2;
	/** @var bool */
	private bool $banAltAccounts = true;
	
	/**
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin){
		$this->config = $plugin->getConfig();
		$this->plugin = $plugin;
		$this->loadConfigValues();
	}
	
	/**
	 * Load and validate configuration values.
	 */
	private function loadConfigValues(): void{
		$this->transferOnSwitch = $this->getConfigValue("account-switch.transfer-on-switch", false);
		$this->serverIp = $this->validateServerIp();
		$this->serverPort = $this->validateServerPort();
		$this->allowUngroup = $this->getConfigValue("groups.allow-ungroup", false);
		$this->maxGroupSize = $this->getConfigValue("groups.max-group-size", 0);
		$this->banAltAccounts = $this->getConfigValue("ban-alt-accounts", false);
	}
	
	/**
	 * Get a configuration value with a default.
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	private function getConfigValue(string $key, $default){
		return $this->config->getNested($key, $default);
	}
	
	/**
	 * Validate and return the server IP address from the configuration.
	 *
	 * @return string|null
	 */
	private function validateServerIp(): ?string{
		$serverIp = trim(strval($this->config->getNested("account-switch.server-ip")));
		
		if($serverIp === ""){
			return null;
		}
		
		if(filter_var($serverIp, FILTER_VALIDATE_IP)){
			return $serverIp;
		}
		
		$this->plugin->getLogger()->warning("'server-ip' must be a valid IP address or hostname");
		return null;
	}
	
	/**
	 * Validate and return the server port from the configuration.
	 *
	 * @return int
	 */
	private function validateServerPort(): int{
		$serverPort = (int)$this->config->getNested("account-switch.server-port");
		if($serverPort < 0 || $serverPort > 65535){
			$this->plugin->getLogger()->warning("'server-port' must be a valid port number");
			return 0; // Default to 0 if invalid
		}
		return $serverPort;
	}
	
	
	/**
	 * @return array
	 */
	public function asArray(): array{
		return $this->config->getAll();
	}
	
	/**
	 * Retrieves the server IP address from the configuration.
	 *
	 * @return string The server IP address.
	 */
	public function getServerIp(): string{
		return $this->serverIp;
	}
	
	/**
	 * Retrieves the server port from the configuration.
	 *
	 * @return int The server port.
	 */
	public function getServerPort(): int{
		return $this->serverPort;
	}
	
	/**
	 * Checks if account transfer on switch is enabled.
	 *
	 * @return bool True if transfer on switch is enabled, false otherwise.
	 */
	public function isTransferOnSwitch(): bool{
		return $this->transferOnSwitch;
	}
	
	/**
	 * Checks if ungrouping of accounts is allowed.
	 *
	 * @return bool True if ungrouping is allowed, false otherwise.
	 */
	public function isAllowUngroup(): bool{
		return $this->allowUngroup;
	}
	
	/**
	 * Retrieves the maximum group size for accounts.
	 *
	 * @return int The maximum number of accounts per group.
	 */
	public function getMaxGroupSize(): int{
		return $this->maxGroupSize;
	}
	
	/**
	 * Checks if banning alts is enabled.
	 * @return bool
	 */
	public function isBanAltAccounts(): bool{
		return $this->banAltAccounts;
	}
	
}