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

namespace FluentDOM\Loader\LoaderSupports {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\DocumentFragment;
  use FluentDOM\Exceptions\InvalidSource;
  use FluentDOM\Exceptions\LoadingError;
  use FluentDOM\Exceptions\UnattachedNode;
  use FluentDOM\Loader\LoaderOptions;
  use FluentDOM\Loader\LoaderResult;
  use FluentDOM\Loader\LoaderSupports;

  trait JsonSupports {

    use LoaderSupports;

    /**
     * Load the json string into an DOMDocument
     * @throws InvalidSource
     * @throws \DOMException
     * @throws UnattachedNode
     */
    public function load(mixed $source, string $contentType, iterable  $options = []): ?LoaderResult {
      if (FALSE !== ($json = $this->getJson($source, $contentType, $options))) {
        $document = new Document('1.0', 'UTF-8');
        $this->transferTo($document, $json);
        return new LoaderResult($document, $contentType);
      }
      return NULL;
    }

    /**
     * @throws UnattachedNode
     * @throws \DOMException
     * @throws InvalidSource
     * @see Loadable::loadFragment
     */
    public function loadFragment(
      mixed $source, string $contentType, iterable $options = []
    ): ?DocumentFragment {
      if (FALSE !== ($json = $this->getJson($source, $contentType, $options))) {
        $document = new Document('1.0', 'UTF-8');
        $fragment = $document->createDocumentFragment();
        $this->transferTo($fragment, $json);
        return $fragment;
      }
      return NULL;
    }

    /**
     * @throws \Exception
     * @throws InvalidSource
     */
    private function getJson(mixed $source, string $contentType, iterable $options)  {
      if ($this->supports($contentType)) {
        if (\is_string($source)) {
          $json = FALSE;
          $settings = $this->getOptions($options);
          if ($settings->isAllowed($sourceType = $settings->getSourceType($source))) {
            switch ($sourceType) {
              /** @noinspection PhpMissingBreakStatementInspection */
            case LoaderOptions::IS_FILE :
              $source = \file_get_contents($source);
              /* $source now contains file constants as string continue with that */
            case LoaderOptions::IS_STRING :
              $json = \json_decode($source, FALSE);
              if (!($json || \is_array($json))) {
                throw new LoadingError\Json(
                  \is_callable('json_last_error') ? \json_last_error() : -1
                );
              }
            }
          }
        } else {
          $json = $source;
        }
        return ($json || \is_array($json)) ? $json : FALSE;
      }
      return FALSE;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getOptions(iterable $options): LoaderOptions {
      return new LoaderOptions(
        $options,
        [
          LoaderOptions::CB_IDENTIFY_STRING_SOURCE => function($source) {
            return $this->startsWith($source, '{') || $this->startsWith($source, '[');
          }
        ]
      );
    }

    abstract protected function transferTo(\DOMNode $target, mixed $json): void;

    /**
     * @param mixed $value
     * @return string
     */
    private function getValueAsString(mixed $value): string {
      if (\is_bool($value)) {
        return $value ? 'true' : 'false';
      }
      return (string)$value;
    }

    /**
     * @param string $nodeName
     * @param \stdClass $properties
     * @param \DOMNode $parent
     * @return string|NULL
     */
    private function getNamespaceForNode(
      string $nodeName, \stdClass $properties, \DOMNode $parent
    ): ?string {
      $prefix = \substr($nodeName, 0, (int)\strpos($nodeName, ':'));
      $xmlns = $this->getNamespacePropertyName($prefix);
      return $properties->{$xmlns} ?? $parent->lookupNamespaceUri(empty($prefix) ? '' : $prefix);
    }

    /**
     * Get the property name for a namespace prefix
     *
     * @param string $prefix
     * @return string
     */
    protected function getNamespacePropertyName(string $prefix): string {
      return empty($prefix) ? 'xmlns' : 'xmlns:'.$prefix;
    }
  }
}
