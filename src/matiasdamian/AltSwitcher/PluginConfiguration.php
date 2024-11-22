<?php

declare(strict_types=1);

namespace matiasdamian\AltSwitcher;

use pocketmine\utils\Config;

class PluginConfiguration{
	/** @var Config  */
	private Config $config;
	
	/** @var bool  */
	private bool $transferOnSwitch = true;
	/** @var string  */
	private string $serverIp = "127.0.0.1";
	/** @var int  */
	private int $serverPort = 19132;
	/** @var bool  */
	private bool $allowUngroup = true;
	/** @var int  */
	private int $maxGroupSize = 2;
	/** @var bool */
	private bool $banAltAccounts = true;
	
	/**
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin){
		$this->config = $plugin->getConfig();
		$this->loadConfiguration($plugin);
	}
	
	/**
	 * Loads the configuration from the plugin.
	 *
	 * @param Main $plugin
	 * @return void
	 */
	private function loadConfiguration(Main $plugin) : void{
		$this->transferOnSwitch = (bool) $this->config->getNested("account-switch.transfer-on-switch");
		
		// Server IP validation
		$serverIp = strval($plugin->getConfig()->getNested("account-switch.server-ip"));
		if(trim($serverIp) !== ""){
			if(filter_var($serverIp, FILTER_VALIDATE_IP)){
				$this->serverIp = $serverIp;
			}else{
				$plugin->getLogger()->warning("'server-ip' must be a valid IP address or hostname");
			}
		}
		
		// Server port validation
		$serverPort = (int) $this->config->getNested("account-switch.server-port");
		if($serverPort >= 0 && $serverPort <= 65535){
			$this->serverPort = $serverPort;
		}else{
			$plugin->getLogger()->warning("'server-port' must be a valid port number");
		}
		
		$this->allowUngroup = boolval($this->config->getNested("groups.allow-ungroup"));
		
		$maxGroupSize = (int) $this->config->getNested("groups.max-group-size");
		if($maxGroupSize > 0){
			$this->maxGroupSize = $maxGroupSize;
		}
		
		$this->banAltAccounts = (bool) $this->config->getNested("ban-alt-accounts");
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