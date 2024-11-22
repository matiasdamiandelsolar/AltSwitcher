<?php

declare(strict_types=1);

namespace matiasdamian\AltSwitcher\command;

/**
 * Interface AccountSubcommands
 *
 * Subcommands used in the plugin.
 */
interface AccountSubcommands{
	/**
	 * Subcommand for managing accounts.
	 *
	 * @var string
	 */
	public const MANAGE_SUBCOMMAND = "manage";
	
	/**
	 * Subcommand for grouping accounts.
	 *
	 * @var string
	 */
	public const GROUP_SUBCOMMAND = "group";
}