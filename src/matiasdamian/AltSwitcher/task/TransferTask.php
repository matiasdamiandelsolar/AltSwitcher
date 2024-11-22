<?php

declare(strict_types=1);

namespace matiasdamian\AltSwitcher\task;

use pocketmine\scheduler\Task;
use pocketmine\player\Player;

use matiasdamian\AltSwitcher\PluginConfiguration;

class TransferTask extends Task{
	/** @var PluginConfiguration  */
	private PluginConfiguration $configuration;
	/** @var Player  */
	private Player $player;
	
	/**
	 * @param PluginConfiguration $configuration
	 * @param Player $player
	 */
	public function __construct(PluginConfiguration $configuration, Player $player){
		$this->configuration = $configuration;
		$this->player = $player;
	}
	
	/**
	 * Called when the task is run.
	 * @return void
	 */
	public function onRun() : void{
		if($this->player->isOnline()){
			
			$serverIp = $this->configuration->getServerIp();
			$serverPort = $this->configuration->getServerPort();
			
			$this->player->transfer($serverIp, $serverPort);
		}
	}
	
}