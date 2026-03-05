<?php

declare(strict_types=1);

namespace UIX\Data;

use UIX\Contracts\DataSourceInterface;

/**
 * Persists data as a single serialized post_meta entry.
 */
class PostMetaSource implements DataSourceInterface
{
    public function load(string $key, ?int $postId = null): array
    {
        if (! $postId) {
            return [];
        }
        $data = get_post_meta($postId, $key, true);
        return is_array($data) ? $data : [];
    }

    public function save(string $key, array $data, ?int $postId = null): void
    {
        if (! $postId) {
            return;
        }
        update_post_meta($postId, $key, $data);
    }

    public function type(): string
    {
        return 'post_meta';
    }
}
