<?php

#Author: Tazz

declare(strict_types=1);

namespace Tazz\invmenu\transaction;

use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\item\Item;
use pocketmine\Player;

class InvMenuTransaction{

	/** @var Player */
	private $player;

	/** @var Item */
	private $out;

	/** @var Item */
	private $in;

	/** @var SlotChangeAction */
	private $action;

	/** @var InventoryTransaction */
	private $transaction;

	public function __construct(Player $player, Item $out, Item $in, SlotChangeAction $action, InventoryTransaction $transaction){
		$this->player = $player;
		$this->out = $out;
		$this->in = $in;
		$this->action = $action;
		$this->transaction = $transaction;
	}

	public function getPlayer() : Player{
		return $this->player;
	}

	public function getOut() : Item{
		return $this->out;
	}

	public function getIn() : Item{
		return $this->in;
	}

	/**
	 * Returns the item that was clicked / taken out of the inventory.
	 *
	 * @link InvMenuTransaction::getOut()
	 * @return Item
	 */
	public function getItemClicked() : Item{
		return $this->getOut();
	}

	/**
	 * Returns the item that an item was clicked with / placed in the inventory.
	 *
	 * @link InvMenuTransaction::getIn()
	 * @return Item
	 */
	public function getItemClickedWith() : Item{
		return $this->getIn();
	}

	public function getAction() : SlotChangeAction{
		return $this->action;
	}

	public function getTransaction() : InventoryTransaction{
		return $this->transaction;
	}

	public function continue() : InvMenuTransactionResult{
		return new InvMenuTransactionResult(false);
	}

	public function discard() : InvMenuTransactionResult{
		return new InvMenuTransactionResult(true);
	}
}