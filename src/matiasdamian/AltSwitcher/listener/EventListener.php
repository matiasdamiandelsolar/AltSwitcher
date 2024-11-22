<?php

declare(strict_types=1);

namespace matiasdamian\AltSwitcher\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\player\PlayerInfo;
use pocketmine\player\XboxLivePlayerInfo;
use pocketmine\permission\BanList;
use Ramsey\Uuid\Uuid;

use matiasdamian\AltSwitcher\Main;

class EventListener implements Listener{
	
	/** @var Main */
	private Main $plugin;
	
	/**
	 * @param Main $plugin The plugin instance.
	 */
	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}
	
	/**
	 * Get the plugin instance.
	 *
	 * @return Main
	 */
	private function getPlugin(): Main{
		return $this->plugin;
	}
	
	/**
	 * Handle player login.
	 *
	 * @param PlayerLoginEvent $event
	 */
	public function onLogin(PlayerLoginEvent $event): void{
		$player = $event->getPlayer();
		$this->getPlugin()->getAccountManager()->registerPlayer($player);
		
		$accountGroup = $this->getPlugin()->getAccountManager()->getAccountGroup($player->getName());
		if($accountGroup !== null){
			foreach($accountGroup->getAccounts() as $account){
				if(strcasecmp($account->getUsername(), $player->getName()) === 0){
					$account->updateLastLogin();
				}
			}
		}
	}
	
	/**
	 * Check if alt accounts are banned.
	 *
	 * @param PlayerPreLoginEvent $event
	 * @priority MONITOR
	 */
	public function onPlayerPreLogin(PlayerPreLoginEvent $event): void{
		$username = $event->getPlayerInfo()->getUsername();
		
		if($event->isKickFlagSet(PlayerPreLoginEvent::KICK_FLAG_BANNED) ||
			
			$this->getPlugin()->getConfiguration()->isBanAltAccounts()){
			return;
		}
		
		$accountGroup = $this->getPlugin()->getAccountManager()->getAccountGroup($username);
		
		if($accountGroup !== null){
			$banList = $this->getPlugin()->getServer()->getNameBans();
			
			foreach($accountGroup->getAccounts() as $account){
				if($banList->isBanned($username)){
					$event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_BANNED);
					return;
				}
			}
		}
	}
	
	/**
	 * Set active UUID.
	 *
	 * @param PlayerPreLoginEvent $event
	 * @priority HIGHEST
	 */
	public function onPlayerPreLoginEvent(PlayerPreLoginEvent $event): void{
		$playerInfo = $event->getPlayerInfo();
		
		$activeUsername = $this->getPlugin()->getAccountManager()->getActiveUsername(
			$playerInfo->getUuid()->toString()
		);
		
		if($activeUsername !== null && strcasecmp($activeUsername, $playerInfo->getUsername()) !== 0){
			$this->setActiveUsername($playerInfo, $activeUsername);
		}
	}
	
	/**
	 * @param PlayerInfo $playerInfo
	 * @param string $username
	 * @return void
	 */
	private function setActiveUsername(PlayerInfo $playerInfo, string $activeUsername) : void{
		$class = match(true){
			$playerInfo instanceof XboxLivePlayerInfo => PlayerInfo::class,
			$playerInfo instanceof PlayerInfo => PlayerInfo::class,
			default => null
		};
		if($class === null){
			return;//NOOP
		}
		$reflectionClass = new \ReflectionClass($class);
		$property = $reflectionClass->getProperty("username");
		$property->setAccessible(true);
		$property->setValue($playerInfo, $activeUsername);
	}
	
}