<?php

#Author: Tazz

declare(strict_types=1);

namespace Tazz\invmenu\session\network\handler;

use Closure;
use Tazz\invmenu\session\network\NetworkStackLatencyEntry;
use pocketmine\network\mcpe\protocol\types\DeviceOS;

final class PlayerNetworkHandlerRegistry{

	/** @var PlayerNetworkHandler */
	private static $default;

	/** @var PlayerNetworkHandler[] */
	private static $game_os_handlers = [];

	public static function init() : void{
		self::registerDefault(new ClosurePlayerNetworkHandler(static function(Closure $then) : NetworkStackLatencyEntry{
			return new NetworkStackLatencyEntry(mt_rand() * 1000 /* TODO: remove this hack */, $then);
		}));
		self::register(DeviceOS::PLAYSTATION, new ClosurePlayerNetworkHandler(static function(Closure $then) : NetworkStackLatencyEntry{
			$timestamp = mt_rand();
			return new NetworkStackLatencyEntry($timestamp, $then, $timestamp * 1000);
		}));
	}

	public static function registerDefault(PlayerNetworkHandler $handler) : void{
		self::$default = $handler;
	}

	public static function register(int $os_id, PlayerNetworkHandler $handler) : void{
		self::$game_os_handlers[$os_id] = $handler;
	}

	public static function get(int $os_id) : PlayerNetworkHandler{
		return self::$game_os_handlers[$os_id] ?? self::$default;
	}
}