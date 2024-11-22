<?php

declare(strict_types=1);

namespace matiasdamian\AltSwitcher\command\handler;

use matiasdamian\AltSwitcher\command\AccountCommand;
use matiasdamian\AltSwitcher\Main;
use matiasdamian\LangManager\LangManager;
use pocketmine\player\Player;
use pocketmine\player\IPlayer;

use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;

class GroupSubcommandHandler{
	/** @var AccountCommand */
	private readonly AccountCommand $command;
	
	public function __construct(AccountCommand $command
	){
		$this->command = $command;
	}
	
	/**
	 * @return Main
	 */
	private function getPlugin(): Main{
		return $this->command->getPlugin();
	}
	
	/**
	 * Allows the player to group accounts together.
	 * @param Player $player
	 * @param array $args
	 * @return bool
	 */
	public function execute(Player $player, array $args): bool{
		$accountManager = $this->getPlugin()->getAccountManager();
		
		if(isset($args[1])){
			$receiver = strtolower($args[1]);
			
			$requester = $accountManager->hasGroupRequest($receiver);
			$requesterGroup = $accountManager->getAccountGroup(strval($requester));
			
			if($requester !== false && $requesterGroup !== null && strcasecmp($player->getName(), $requester) === 0){
				$accountManager->removeGroupRequest($requester);
				
				$receiverGroup = $accountManager->getAccountGroup($receiver);
				
				if($requesterGroup->isInGroup($receiver)){
					LangManager::send("altswitcher-already-grouped", $player);
					return true;
				}
				
				// Group the accounts
				$requesterGroup->groupAccount($receiver, $player->getXuid());
				$receiverGroup->ungroupAccount($receiver);
				
				foreach($receiverGroup->getAccounts() as $account){
					$requesterGroup->groupAccount(
						$account->getUsername(),
						$account->getXuid(),
						$account->getLastLogin()
					);
				}
				
				LangManager::send("altswitcher-grouped", $player);
				return true;
			}
			
			if($requester !== false){
				LangManager::send("altswitcher-cannot-group", $player, $requester, $player->getName());
				return true;
			}
			return true;
		}
		
		$maxGroupSize = $this->getPlugin()->getConfiguration()->getMaxGroupSize();
		$currentGroupSize = count($accountManager->getAccountGroup($player->getName())->getAccounts());
		
		if($currentGroupSize >= $maxGroupSize){
			LangManager::send("altswitcher-group-limit", $player, $maxGroupSize - 1);
			return true;
		}
		
		$form = new CustomForm(function(Player $player, $data){
			if(is_array($data) && isset($data[2]) && is_string($username = $data[2])){
				$playerB = $player->getServer()->getOfflinePlayer($username);
				
				if($playerB instanceof IPlayer){
					if(strcasecmp($playerB->getName(), $player->getName()) === 0){
						LangManager::send("altswitcher-other", $player);
						return;
					}
					$this->getPlugin()->getAccountManager()->setGroupRequest($player->getName(), $username);
					LangManager::send("altswitcher-finish", $player, $username, $player->getName());
				}
				return;
			}
			
			// If no valid data is provided, return to the main form
			$this->command->sendMainForm($player);
		});
		
		$form->setTitle(LangManager::translate("altswitcher-title", $player));
		$form->addLabel(LangManager::translate("altswitcher-disclaimer", $player));
		$form->addLabel(LangManager::translate("altswitcher-enter-username", $player));
		$form->addInput(LangManager::translate("altswitcher-username", $player));
		
		$player->sendForm($form);
		return true;
	}
	
}