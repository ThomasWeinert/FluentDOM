<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Nodes {

  use FluentDOM\Nodes;
  use FluentDOM\Utility\Constraints;

  /*
   * Fetches dom nodes for the current context.
   */
  class Fetcher {

    /** reverse the order of the fetched nodes */
    public const REVERSE = 1;
    /** include the node at the stop */
    public const INCLUDE_STOP = 2;
    /** unique and sort nodes */
    public const UNIQUE = 4;
    /** ignore the current context (use the document context) */
    public const IGNORE_CONTEXT = 8;
    /** ignore the current context (use the document context) */
    public const FORCE_SORT = 16;

    private Nodes $_nodes;

    public function __construct(Nodes $nodes) {
      $this->_nodes = $nodes;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function fetch(
      string $expression, callable $filter = NULL, callable $stopAt = NULL, int $options = 0
    ): array {
      if ($this->validateContextIgnore($expression, $options)) {
        $nodes = $this->fetchFor(
          $expression, NULL, $filter, $stopAt, $options
        );
      } else {
        $nodes = [];
        foreach ($this->_nodes->toArray() as $context) {
          $fetchedNodes = $this->fetchFor(
            $expression, $context, $filter, $stopAt, $options
          );
          if (empty($fetchedNodes) || \count($fetchedNodes) === 0) {
            continue;
          }
          \array_push($nodes, ...$fetchedNodes);
        }
      }
      return $this->unique($nodes, $options);
    }

    /**
     * Validate if the context can be ignored.
     *
     * @param string $expression
     * @param int $options
     * @return bool
     */
    private function validateContextIgnore(string $expression, int $options): bool {
      if ('' === $expression) {
        throw new \InvalidArgumentException(
          'Invalid selector/expression.'
        );
      }
      return
        Constraints::hasOption($options, self::IGNORE_CONTEXT) ||
        (str_starts_with($expression, '/'));
    }

    /**
     * Make nodes unique if needed or forced.
     *
     * @param array $nodes
     * @param int $options
     * @return array
     */
    private function unique(array $nodes, int $options): array {
      if (
        Constraints::hasOption($options, self::FORCE_SORT) ||
        (\count($this->_nodes) > 1 && Constraints::hasOption($options, self::UNIQUE))
      ) {
        $nodes = $this->_nodes->unique($nodes);
      }
      return $nodes;
    }

    /**
     * Fetch the nodes for the provided context node. If $context
     * ist NULL the document context is used. Use $filter and
     * $stopAt to reduce the returned nodes.
     *
     * @throws \InvalidArgumentException
     */
    private function fetchFor(
      string $expression,
      \DOMNode $context = NULL,
      callable $filter = NULL,
      callable $stopAt = NULL,
      int $options = 0
    ): array {
      $nodes = $this->fetchNodes($expression, $context, $options);
      if ($filter || $stopAt) {
        return $this->filterNodes($nodes, $filter, $stopAt, $options);
      }
      return $nodes;
    }

    /**
     * Fetch the nodes for the provided context node. If $context
     * ist NULL the document context is used.
     *
     * @throws \InvalidArgumentException
     */
    private function fetchNodes(
      string $expression, \DOMNode $context = NULL, int $options = 0
    ): array {
      $nodes = $this->_nodes->xpath($expression, $context);
      if (!$nodes instanceof \DOMNodeList) {
        throw new \InvalidArgumentException(
          'Given selector/expression did not return a node list.'
        );
      }
      $nodesArray = iterator_to_array($nodes);
      if (Constraints::hasOption($options, self::REVERSE)) {
        /** @noinspection PhpRedundantOptionalArgumentInspection */
        return array_reverse($nodesArray, FALSE);
      }
      return $nodesArray;
    }

    /**
     * @param array $nodes
     * @param callable|NULL $filter
     * @param callable|NULL $stopAt
     * @param int $options
     * @return array
     */
    private function filterNodes(
      array $nodes, callable $filter = NULL, callable $stopAt = NULL, int $options = 0
    ): array {
      $result = [];
      foreach ($nodes as $index => $node) {
        [$isFilter, $isStopAt] = $this->getNodeStatus(
          $node, $index, $filter, $stopAt
        );
        if ($isStopAt) {
          if (
            $isFilter &&
            Constraints::hasOption($options, self::INCLUDE_STOP)
          ) {
            $result[] = $node;
          }
          return $result;
        }
        if ($isFilter) {
          $result[] = $node;
        }
      }
      return $result;
    }

    /**
     * @param \DOMNode $node
     * @param int $index
     * @param callable|NULL $filter
     * @param callable|NULL $stopAt
     * @return bool[]
     */
    private function getNodeStatus(
      \DOMNode $node, int $index, callable $filter = NULL, callable $stopAt = NULL
    ): array {
      return [
        empty($filter) || $filter($node, $index),
        !empty($stopAt) && $stopAt($node, $index)
      ];
    }
  }
}
