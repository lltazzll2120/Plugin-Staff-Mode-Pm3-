<?php

#Author: Tazz

declare(strict_types=1);

namespace Tazz\invmenu\session\network\handler;

use Closure;
use Tazz\invmenu\session\network\NetworkStackLatencyEntry;

final class ClosurePlayerNetworkHandler implements PlayerNetworkHandler{

	/** @var Closure */
	private $creator;

	/**
	 * @param Closure $creator
	 *
	 * @phpstan-param Closure(Closure) : NetworkStackLatencyEntry $creator
	 */
	public function __construct(Closure $creator){
		$this->creator = $creator;
	}

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry{
		return ($this->creator)($then);
	}
}