<?php

declare(strict_types=1);

namespace UIX\UI;

use UIX\Contracts\NodeInterface;

/**
 * Abstract base for every UIX node.
 *
 * Children are added via fluent factory methods defined on concrete subclasses
 * (e.g. Page::section(), Section::control()).  The magic __call fallback
 * handles child-type registration so new child types do not require boilerplate.
 */
abstract class Node implements NodeInterface
{
    /** @var array<string, Node> */
    protected array $children = [];

    protected ?Node $parent;

    public function __construct(
        protected readonly string $slug,
        protected readonly string $type,
        protected array $config = [],
        ?Node $parent = null,
    ) {
        $this->parent = $parent;
    }

    // ------------------------------------------------------------------
    // Identity
    // ------------------------------------------------------------------

    public function id(): string
    {
        return $this->parent
            ? $this->parent->id() . '-' . $this->slug
            : $this->slug;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function type(): string
    {
        return $this->type;
    }

    // ------------------------------------------------------------------
    // Children
    // ------------------------------------------------------------------

    public function addChild(Node $child): static
    {
        $this->children[$child->slug()] = $child;
        return $this;
    }

    /** @return array<string, Node> */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Return the parent node (null for root nodes like Page/Metabox).
     */
    public function getParent(): ?Node
    {
        return $this->parent;
    }

    // ------------------------------------------------------------------
    // Schema serialisation
    // ------------------------------------------------------------------

    /**
     * @return array<string, mixed>
     */
    public function toSchema(): array
    {
        $schema = [
            'id'       => $this->id(),
            'slug'     => $this->slug,
            'type'     => $this->type,
            'config'   => $this->config,
            'children' => array_values(
                array_map(fn(Node $n) => $n->toSchema(), $this->children)
            ),
        ];

        return array_merge($schema, $this->extraSchema());
    }

    /**
     * Subclasses can inject extra keys into the schema without overriding toSchema().
     *
     * @return array<string, mixed>
     */
    protected function extraSchema(): array
    {
        return [];
    }

    // ------------------------------------------------------------------
    // Activation
    // ------------------------------------------------------------------

    public function isActive(): bool
    {
        return $this->parent ? $this->parent->isActive() : true;
    }

    // ------------------------------------------------------------------
    // Config helpers
    // ------------------------------------------------------------------

    protected function cfg(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }
}
