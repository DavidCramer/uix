<?php

declare(strict_types=1);

namespace UIX\Contracts;

/**
 * Contract for all UIX nodes (pages, metaboxes, sections, controls, etc.)
 */
interface NodeInterface
{
    /**
     * Unique identifier for this node, prefixed by parent chain.
     */
    public function id(): string;

    /**
     * Serialize this node (and its children) to a schema array
     * that will be passed to the React frontend.
     *
     * @return array<string, mixed>
     */
    public function toSchema(): array;

    /**
     * Whether this node should be active for the current screen.
     */
    public function isActive(): bool;
}
