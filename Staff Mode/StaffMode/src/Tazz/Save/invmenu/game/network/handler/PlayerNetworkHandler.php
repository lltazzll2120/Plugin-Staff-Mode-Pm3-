<?php

#Author: Tazz

declare(strict_types=1);

namespace Ayzrix\Tazz\invmenu\session\network\handler;

use Closure;
use Tazz\invmenu\session\network\NetworkStackLatencyEntry;

interface PlayerNetworkHandler{

	public function createNetworkStackLatencyEntry(Closure $then) : NetworkStackLatencyEntry;
}