<?php
#Author: Tazz

namespace Tazz\StaffMode;

use Tazz\StaffMode\API\StaffAPI;
use Tazz\StaffMode\Commands\Staff;
use Tazz\StaffMode\Commands\StaffUndo;
use Tazz\StaffMode\Events\Listeners\PlayerListener;
use Tazz\StaffMode\Tasks\InvisibilityTask;
use Tazz\invmenu\InvMenuHandler;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class Main extends PluginBase {

    private static $instance = null;

    public function onEnable(): void {
        self::$instance = $this;
        $this->saveDefaultConfig();
        $this->initEvents();
        $this->initCommands();
        $this->getScheduler()->scheduleRepeatingTask(new InvisibilityTask(), 20);
        if(!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);
    }

    public function onDisable() {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            if (StaffAPI::isStaff($player)) {
                StaffAPI::setStaff($player, false);
            }
        }
    }

    private function initEvents(): void {
        $events = [new PlayerListener()];
        foreach($events as $event) {
            $this->getServer()->getPluginManager()->registerEvents($event, $this);
        }
    }

    private function initCommands(): void {
        $commands = [new Staff($this), new StaffUndo($this)];
        foreach ($commands as $command) {
            if ($command instanceof Command) {
                $this->getServer()->getCommandMap()->register($command->getName(), $command);
            }
        }
    }

    public static function getInstance(): Main {
        return self::$instance;
    }
}
