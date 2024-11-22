<?php

declare(strict_types=1);

namespace matiasdamian\AltSwitcher;

use pocketmine\plugin\PluginBase;

use matiasdamian\AltSwitcher\account\AccountManager;
use matiasdamian\AltSwitcher\command\AccountCommand;
use matiasdamian\AltSwitcher\listener\EventListener;
use jojoe77777\FormAPI\FormAPI;

class Main extends PluginBase{
	/** @var AccountManager|null */
	private ?AccountManager $accountManager = null;
	
	/** @var PluginConfiguration|null */
	private ?PluginConfiguration $pluginConfiguration = null;
	
	/**
	 * Get the AccountManager instance.
	 *
	 * @return AccountManager|null
	 */
	public function getAccountManager(): ?AccountManager{
		return $this->accountManager;
	}
	
	/**
	 * Retrieves the plugin configuration instance.
	 *
	 * @return PluginConfiguration|null Returns the PluginConfiguration instance if loaded, or null if not.
	 */
	public function getConfiguration(): ?PluginConfiguration{
		return $this->pluginConfiguration;
	}
	
	/**
	 * @return void
	 */
	public function onEnable(): void{
		if(!class_exists(FormAPI::class)){
			$this->getLogger()->error("FormAPI not found. Disabling plugin...");
			$this->getServer()->getPluginManager()->disablePlugin($this);
			return;
		}
		
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getCommandMap()->register("altswitcher", new AccountCommand($this));
		
		$this->pluginConfiguration = new PluginConfiguration($this);
		$this->accountManager = new AccountManager($this);
		
		$this->getLogger()->info("Plugin enabled.");
		
		
	}
	
	/**
	 * @return void
	 */
	public function onDisable(): void{
		$this->getLogger()->info("Plugin disabled.");
		
		if($this->accountManager !== null){
			$this->accountManager->save();
		}
	}
}