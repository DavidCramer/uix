<?php

declare(strict_types=1);

namespace UIX\Data;

/**
 * Re-export the interface from the Contracts namespace for convenience.
 * Consumers can use either UIX\Data\DataSourceInterface or UIX\Contracts\DataSourceInterface.
 */
interface DataSourceInterface extends \UIX\Contracts\DataSourceInterface {}
