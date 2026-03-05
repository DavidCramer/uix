<?php

declare(strict_types=1);

namespace UIX\Data;

use UIX\Contracts\DataSourceInterface;

/**
 * Persists data as a single serialized WP option.
 */
class OptionsSource implements DataSourceInterface
{
    public function load(string $key, ?int $postId = null): array
    {
        $data = get_option($key, []);
        return is_array($data) ? $data : [];
    }

    public function save(string $key, array $data, ?int $postId = null): void
    {
        update_option($key, $data, false);
    }

    public function type(): string
    {
        return 'options';
    }
}
