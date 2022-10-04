<?php
#Author: Tazz

namespace Tazz\StaffMode\Tasks;

use Tazz\StaffMode\API\StaffAPI;
use Tazz\StaffMode\Commands\Staff;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class InvisibilityTask extends Task {

    public function onRun(int $currentTick) {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            if (StaffAPI::isStaff($player) and isset(StaffAPI::$vanish[$player->getName()])) {
                foreach (Server::getInstance()->getOnlinePlayers() as $target) {
                    $target->hidePlayer($player);
                }
            }
        }
    }
}