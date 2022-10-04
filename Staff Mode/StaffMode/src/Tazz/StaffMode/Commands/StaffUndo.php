<?php
#Author: Tazz

namespace Tazz\StaffMode\Commands;

use Tazz\StaffMode\API\StaffAPI;
use Tazz\StaffMode\Main;
use Tazz\StaffMode\Utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\Server;

class StaffUndo extends PluginCommand {

    public function __construct(Main $plugin) {
        parent::__construct("staffback", $plugin);
        $this->setDescription("Revenir en arriere");
        $this->setPermission("staff.command");
    }

    public function execute(CommandSender $player, string $commandLabel, array $args) {
        if ($player instanceof Player) {
            if ($player->hasPermission("staff.command")) {
                if (isset(StaffAPI::$undo[$player->getName()])) {
                    $target = StaffAPI::$undo[$player->getName()][0];
                    $target = Server::getInstance()->getPlayer($target);
                    if ($target instanceof Player) {
                        $target->getInventory()->setContents(StaffAPI::$undo[$player->getName()][1][0]);
                        $target->getArmorInventory()->setContents(StaffAPI::$undo[$player->getName()][1][1]);
                        $player->sendMessage(Utils::getConfigMessage("undo_success", array($target->getName())));
                        unset(StaffAPI::$undo[$player->getName()]);
                    } else $player->sendMessage(Utils::getConfigMessage("undo_offline"));
                } else $player->sendMessage(Utils::getConfigMessage("undo_not_found"));
            }
        }
    }
}
