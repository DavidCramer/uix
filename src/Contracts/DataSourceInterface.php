<?php

declare(strict_types=1);

namespace UIX\Contracts;

/**
 * Contract for data sources that back a UIX node.
 */
interface DataSourceInterface
{
    /**
     * Load saved data for a node.
     *
     * @param string   $key    Storage key (slug / option name).
     * @param int|null $postId Post ID (required for post-meta sources).
     *
     * @return array<string, mixed>
     */
    public function load(string $key, ?int $postId = null): array;

    /**
     * Save data for a node.
     *
     * @param string               $key    Storage key.
     * @param array<string, mixed> $data   Data to persist.
     * @param int|null             $postId Post ID (required for post-meta sources).
     */
    public function save(string $key, array $data, ?int $postId = null): void;

    /**
     * The source type identifier (used in the schema so the REST layer
     * knows which source to route through).
     */
    public function type(): string;
}
