<?php
#Author: Tazz

namespace Tazz\StaffMode\Events\Listeners;

use Tazz\StaffMode\API\StaffAPI;
use Tazz\StaffMode\Utils\Utils;
use Tazz\muqsit\invmenu\InvMenu;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\Player;
use pocketmine\Server;

class PlayerListener implements Listener {

    public static $players = [];
    private static $interact = [];

    public function onPlayerDamageByPlayer(EntityDamageByEntityEvent $event) {
        $entity = $event->getEntity();
        $damager = $event->getDamager();

        if ($entity instanceof Player and $damager instanceof Player) {
            if (StaffAPI::isStaff($damager)) {
                $item = $damager->getInventory()->getItemInHand();
                if ($item->getId() === Item::PACKED_ICE) {
                    $event->setCancelled(true);

                    if ($entity->isImmobile()) {
                        $entity->setImmobile(false);
                        $entity->sendMessage(Utils::getConfigMessage("player_unfreeze_success"));
                        $damager->sendMessage(Utils::getConfigMessage("unfreeze_success", array($entity->getName())));
                    } else {
                        $entity->setImmobile(true);
                        $entity->sendMessage(Utils::getConfigMessage("player_freeze_success"));
                        $damager->sendMessage(Utils::getConfigMessage("freeze_success", array($entity->getName())));
                    }
                }

                if ($item->getId() === Item::CHEST) {
                    $event->setCancelled(true);
                    $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
                    $menu->setListener(InvMenu::readonly());
                    $glass = Item::get(160, 14);
                    $glass->setCustomName("§c---");
                    $menu->getInventory()->setItem(36, $glass);
                    $menu->getInventory()->setItem(37, $glass);
                    $menu->getInventory()->setItem(38, $glass);
                    $menu->getInventory()->setItem(39, $glass);
                    $menu->getInventory()->setItem(40, $glass);
                    $menu->getInventory()->setItem(41, $glass);
                    $menu->getInventory()->setItem(42, $glass);
                    $menu->getInventory()->setItem(43, $glass);
                    $menu->getInventory()->setItem(44, $glass);
                    $glass->setCustomName(Utils::getIntoConfig("inventory_armor_1"));
                    $menu->getInventory()->setItem(45, $glass);
                    $glass->setCustomName(Utils::getIntoConfig("inventory_armor_2"));
                    $menu->getInventory()->setItem(47, $glass);
                    $glass->setCustomName(Utils::getIntoConfig("inventory_armor_3"));
                    $menu->getInventory()->setItem(49, $glass);
                    $glass->setCustomName(Utils::getIntoConfig("inventory_armor_4"));
                    $menu->getInventory()->setItem(51, $glass);
                    $glass->setCustomName(Utils::getIntoConfig("inventory_armor_5"));
                    $menu->getInventory()->setItem(53, $glass);
                    foreach($entity->getInventory()->getContents() as $value => $item){
                        $menu->getInventory()->setItem($value, $item);
                    }
                    $menu->getInventory()->setItem(46, $entity->getArmorInventory()->getHelmet());
                    $menu->getInventory()->setItem(48, $entity->getArmorInventory()->getChestplate());
                    $menu->getInventory()->setItem(50, $entity->getArmorInventory()->getLeggings());
                    $menu->getInventory()->setItem(52, $entity->getArmorInventory()->getBoots());
                    $menu->setName(Utils::getConfigMessage("inventory_title", array($entity->getName())));
                    $menu->send($damager);
                }

                if ($item->getId() === Item::BLAZE_ROD) {
                    $event->setCancelled(true);
                    $entity->kill();
                    $damager->sendMessage(Utils::getConfigMessage("kill_success", array($entity->getName())));
                }

                if ($item->getId() === Item::CAULDRON) {
                    $event->setCancelled(true);
                    StaffAPI::$undo[$damager->getName()] = array($entity->getName(), array($entity->getInventory()->getContents(), $entity->getArmorInventory()->getContents()));
                    $entity->getInventory()->clearAll();
                    $entity->getArmorInventory()->clearAll();
                    $damager->sendMessage(Utils::getConfigMessage("clear_success", array($entity->getName())));
                }

                if ($item->getId() === Item::PAPER) {
                    $event->setCancelled(true);
                    $damager->sendMessage(Utils::getConfigMessage("informations_title", array($entity->getName())));
                    $description = Utils::getConfigMessage("informations_description");
                    $description = str_replace(["{name}", "{ping}", "{xuid}", "{uuid}", "{os}", "{controls}", "{ui_type}", "{gui_scale}", "{model}"], [$entity->getName(), $entity->getPing(), $entity->getXuid(), $entity->getUniqueId(), StaffAPI::$os[StaffAPI::$playerData[$damager->getName()]["DeviceOS"]], StaffAPI::$controls[StaffAPI::$playerData[$damager->getName()]["CurrentInputMode"]], StaffAPI::$ui[StaffAPI::$playerData[$damager->getName()]["UIProfile"]], StaffAPI::$gui[StaffAPI::$playerData[$damager->getName()]["GuiScale"]], StaffAPI::$playerData[$damager->getName()]["DeviceModel"]], $description);
                    $damager->sendMessage($description);
                }
            }
        }
    }

    public function onPlayerDamage(EntityDamageEvent $event) {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            if (StaffAPI::isStaff($player)) {
                $event->setCancelled(true);
            }
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if (StaffAPI::isStaff($player)) {
            if (!isset(self::$interact[$player->getName()])) {
                self::$interact[$player->getName()] = 1;
            }

            if (round(self::$interact[$player->getName()], 1) + 1 <= round(microtime(true), 1)) {
                self::$interact[$player->getName()] = microtime(true);
                if ($item->getId() === Item::DYE and $item->getDamage() === 10) {
                    $player->getInventory()->setItem(2, Item::get(Item::DYE, 9)->setCustomName(Utils::getIntoConfig("unvanish")));
                    $player->sendMessage(Utils::getConfigMessage("vanish_success"));
                    StaffAPI::$vanish[$player->getName()] = true;
                    $player->addEffect(new EffectInstance(Effect::getEffect(Effect::INVISIBILITY), 20 * 60 * 60 * 24 * 365, 1, false));
                    foreach ($player->getServer()->getOnlinePlayers() as $players) {
                        assert($players instanceof Player);
                        $players->hidePlayer($player);
                    }
                }

                if ($item->getId() === Item::DYE and $item->getDamage() === 9) {
                    $player->getInventory()->setItem(2, Item::get(Item::DYE, 10)->setCustomName(Utils::getIntoConfig("vanish")));
                    $player->sendMessage(Utils::getConfigMessage("unvanish_success"));
                    unset(StaffAPI::$vanish[$player->getName()]);
                    $player->removeEffect(14);
                    foreach ($player->getServer()->getOnlinePlayers() as $players) {
                        assert($players instanceof Player);
                        $players->showPlayer($player);
                    }
                }

                if ($item->getId() === Item::ENDER_EYE) {
                    $players = Server::getInstance()->getOnlinePlayers();
                    if (count($players) <= 1) {
                        $player->sendMessage(Utils::getConfigMessage("random_tp_not_found"));
                        return;
                    }
                    $random = $players[array_rand($players)];
                    while ($random === $player) {
                        $random = $players[array_rand($players)];
                    }
                    $player->teleport($random);
                    $player->sendMessage(Utils::getConfigMessage("random_tp_success", array($random->getName())));
                    if (isset(StaffAPI::$vanish[$player->getName()])) $random->hidePlayer($player);
                }

                if ($item->getId() === Item::FEATHER) {
                    if ($player->getAllowFlight() === true) {
                        $player->setAllowFlight(false);
                        $player->setFlying(false);
                        $player->sendMessage(Utils::getConfigMessage("unfly_success"));
                    } else {
                        $player->setAllowFlight(true);
                        $player->setFlying(true);
                        $player->sendMessage(Utils::getConfigMessage("fly_success"));
                    }
                }
            }
        }
    }

    public function onPlayerBlockPlace(BlockPlaceEvent $event) {
        if ($event->getItem()->getCustomName() === Utils::getIntoConfig("inventory_inspect") or $event->getItem()->getCustomName() === Utils::getIntoConfig("clearinv")) {
            $event->setCancelled(true);
        }
    }

    public function onInventoryPickupItem(InventoryPickupItemEvent $event) {
        $player = $event->getInventory()->getHolder();
        if ($player instanceof Player) {
            if (StaffAPI::isStaff($player)) {
                $event->setCancelled(true);
            }
        }
    }

    public function onPlayerInventoryChange(InventoryTransactionEvent $event) {
        if (StaffAPI::isStaff($event->getTransaction()->getSource())) {
            $event->setCancelled(true);
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        if (StaffAPI::isStaff($player)) StaffAPI::setStaff($player, false);
    }

    public function onPacketReceived(DataPacketReceiveEvent $receiveEvent) {
        $pk = $receiveEvent->getPacket();
        if($pk instanceof LoginPacket) {
            StaffAPI::$playerData[$pk->username] = $pk->clientData;
        }
    }
}