<?php
#Author: Tazz

namespace Tazz\StaffMode\Commands;

use Tazz\StaffMode\API\StaffAPI;
use Tazz\StaffMode\Main;
use Tazz\StaffMode\Utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;

class Staff extends PluginCommand {

    public function __construct(Main $plugin) {
        parent::__construct("staffmode", $plugin);
        $this->setDescription("Staffmode");
        $this->setPermission("staff.command");
    }

    public function execute(CommandSender $player, string $commandLabel, array $args) {
        if ($player instanceof Player) {
            if ($player->hasPermission("staff.command")) {
                if (StaffAPI::isStaff($player)) {
                    StaffAPI::setStaff($player, false);
                    $player->sendMessage(Utils::getConfigMessage("staffmode_disable"));
                } else {
                    StaffAPI::setStaff($player, true);
                    $player->sendMessage(Utils::getConfigMessage("staffmode_enable"));
                }
            }
        }
    }
}
