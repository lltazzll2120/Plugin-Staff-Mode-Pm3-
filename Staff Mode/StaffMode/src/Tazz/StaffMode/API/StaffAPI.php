<?php
#Author: Tazz

namespace Tazz\StaffMode\API;

use Tazz\StaffMode\Main;
use Tazz\StaffMode\Utils\Utils;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\Enchant;
use pocketmine\Player;

class StaffAPI {

    public static $staffs = [];
    public static $vanish = [];
    public static $undo = [];
    public static $playerData = [];

    public static $os = ["Unknown", "Android", "iOS", "macOS", "FireOS", "GearVR", "HoloLens", "Windows 10", "Windows", "Dedicated", "Orbis", "Playstation 4", "Nintento Switch", "Xbox One"];
    public static $ui = ["Classic UI", "Pocket UI"];
    public static $controls = ["Unknown", "Mouse", "Touch", "Controller"];
    public static $gui = [-2 => "Minimum", -1 => "Medium", 0 => "Maximum"];


    /**
     * @param Player $player
     * @return bool
     */
    public static function isStaff(Player $player): bool {
        return isset(self::$staffs[$player->getName()]);
    }

    /**
     * @param Player $player
     * @param bool $value
     */
    public static function setStaff(Player $player, bool $value) {
        if ($value) {
            self::$staffs[$player->getName()] = true;
            self::giveItems($player);
        } else {
            self::restoreItems($player);
            unset(self::$staffs[$player->getName()]);
        }
    }

    public static function giveItems(Player $player) {
        self::$staffs[$player->getName()] = array($player->getArmorInventory()->getContents(), $player->getInventory()->getContents());
        $player->getInventory()->clearAll(true);
        $player->getArmorInventory()->clearAll(true);
        $player->getInventory()->setItem(0, Item::get(Item::PACKED_ICE)->setCustomName(Utils::getIntoConfig("freeze")));
        $knockback = Item::get(Item::STICK)->setCustomName(Utils::getIntoConfig("stick"));
        $knockback->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::KNOCKBACK)));
        $player->getInventory()->setItem(1, $knockback);
        $player->getInventory()->setItem(2, Item::get(Item::DYE, 10)->setCustomName(Utils::getIntoConfig("vanish")));
        $player->getInventory()->setItem(3, Item::get(Item::ENDER_EYE)->setCustomName(Utils::getIntoConfig("random_tp")));
        $player->getInventory()->setItem(4, Item::get(Item::CHEST)->setCustomName(Utils::getIntoConfig("inventory_inspect")));
        $player->getInventory()->setItem(5, Item::get(Item::BLAZE_ROD)->setCustomName(Utils::getIntoConfig("kill")));
        $player->getInventory()->setItem(6, Item::get(Item::FEATHER)->setCustomName(Utils::getIntoConfig("fly")));
        $player->getInventory()->setItem(7, Item::get(Item::CAULDRON)->setCustomName(Utils::getIntoConfig("clearinv")));
        $player->getInventory()->setItem(8, Item::get(Item::PAPER)->setCustomName(Utils::getIntoConfig("information")));
    }

    public static function restoreItems(Player $player) {
        $player->getInventory()->clearAll(true);
        $player->getArmorInventory()->clearAll(true);
        $player->getArmorInventory()->setContents(self::$staffs[$player->getName()][0]);
        $player->getInventory()->setContents(self::$staffs[$player->getName()][1]);
    }
}