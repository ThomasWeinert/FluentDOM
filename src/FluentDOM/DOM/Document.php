<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\DOM {

  use FluentDOM\Utility\Namespaces;
  use FluentDOM\Utility\QualifiedName;

  /**
   * @method Attribute createAttributeNS($namespaceURI, $name)
   * @method CdataSection createCdataSection($data)
   * @method Comment createComment($data)
   * @method DocumentFragment createDocumentFragment()
   * @method ProcessingInstruction createProcessingInstruction($target, $data = NULL)
   * @method Text createTextNode($content)
   *
   * @property-read Element $documentElement
   * @property-read Element $firstElementChild
   * @property-read Element $lastElementChild
   */
  class Document extends \DOMDocument implements Node\ParentNode {

    use
      Node\ParentNode\Properties,
      Node\QuerySelector\Implementation,
      Node\Xpath;

    /**
     * @var Xpath
     */
    private $_xpath;

    /**
     * @var Namespaces
     */
    private $_namespaces;

    /**
     * Map dom node classes to extended descendants.
     *
     * @var array
     */
    private static $_classes = [
      'DOMDocument' => self::class,
      'DOMAttr' => Attribute::class,
      'DOMCdataSection' => CdataSection::class,
      'DOMComment' => Comment::class,
      'DOMElement' => Element::class,
      'DOMProcessingInstruction' => ProcessingInstruction::class,
      'DOMText' => Text::class,
      'DOMDocumentFragment' => DocumentFragment::class,
      'DOMEntityReference' => EntityReference::class
    ];

    /**
     * @param string $version
     * @param string $encoding
     */
    public function __construct($version = '1.0', $encoding = 'UTF-8') {
      parent::__construct($version, $encoding ?: 'UTF-8');
      foreach (self::$_classes as $superClass => $className) {
        $this->registerNodeClass($superClass, $className);
      }
      $this->_namespaces = new Namespaces();
    }

    public function __clone() {
      $this->_namespaces = clone $this->_namespaces;
    }

    /**
     * Generate an xpath instance for the document, if the document of the
     * xpath instance does not match the document, regenerate it.
     *
     * @return Xpath
     */
    public function xpath(): Xpath {
      if (
        $this->_xpath instanceof Xpath &&
        $this->_xpath->document === $this
      ) {
        return $this->_xpath;
      }
      $this->_xpath = new Xpath($this);
      foreach ($this->_namespaces as $prefix => $namespaceURI) {
        $this->_xpath->registerNamespace($prefix, $namespaceURI);
      }
      return $this->_xpath;
    }

    /**
     * register a namespace prefix for the document, it will be used in
     * createElement and setAttribute
     *
     * @param string $prefix
     * @param string $namespaceURI
     * @throws \LogicException
     */
    public function registerNamespace(string $prefix, string $namespaceURI): void {
      $this->_namespaces[$prefix] = $namespaceURI;
      if (NULL !== $this->_xpath && $prefix !== '#default') {
        $this->_xpath->registerNamespace($prefix, $namespaceURI);
      }
    }

    /**
     * Get set the namespaces registered for the document object.
     *
     * If the argument is provided ALL namespaces will be replaced.
     *
     * @param array|\Traversable $namespaces
     * @return Namespaces
     * @throws \LogicException
     */
    public function namespaces($namespaces = NULL): Namespaces {
      if (NULL !== $namespaces) {
        $this->_namespaces->assign([]);
        foreach($namespaces as $prefix => $namespaceURI) {
          $this->registerNamespace($prefix, $namespaceURI);
        }
      }
      return $this->_namespaces;
    }

    /**
     * If here is a ':' in the element name, consider it a namespace prefix
     * registered on the document.
     *
     * Allow to add a text content and attributes directly.
     *
     * If $content is an array, the $content argument  will be merged with the $attributes
     * argument.
     *
     * @param string $qualifiedName
     * @param string|array $content
     * @param array|NULL $attributes
     * @return Element
     *@throws \LogicException
     */
    public function createElement($qualifiedName, $content = NULL, array $attributes = NULL): Element {
      [$prefix, $localName] = QualifiedName::split($qualifiedName);
      $namespaceURI = '';
      if ($prefix !== FALSE) {
        if (empty($prefix)) {
          $qualifiedName = $localName;
        } else {
          if ($this->namespaces()->isReservedPrefix($prefix)) {
            throw new \LogicException(
              \sprintf('Can not use reserved namespace prefix "%s" in element name.', $prefix)
            );
          }
          $namespaceURI = (string)$this->namespaces()->resolveNamespace($prefix);
        }
      } else {
        $namespaceURI = (string)$this->namespaces()->resolveNamespace('#default');
      }
      if ($namespaceURI !== '') {
        $node = $this->createElementNS($namespaceURI, $qualifiedName);
      } elseif (isset($this->_namespaces['#default'])) {
        $node = $this->createElementNS('', $qualifiedName);
      } else {
        $node = parent::createElement($qualifiedName);
      }
      $this->appendAttributes($node, $content, $attributes);
      $this->appendContent($node, $content);
      return $node;
    }

    /**
     * @param string $namespaceURI
     * @param string $qualifiedName
     * @param string|NULL $content
     * @return Element
     */
    public function createElementNS($namespaceURI, $qualifiedName, $content = NULL): Element {
      /** @var Element $node */
      $node = parent::createElementNS($namespaceURI, $qualifiedName);
      $this->appendContent($node, $content);
      return $node;
    }

    /**
     * If here is a ':' in the attribute name, consider it a namespace prefix
     * registered on the document.
     *
     * Allow to add a attribute value directly.
     *
     * @param string $name
     * @param string|NULL $value
     * @return Attribute
     * @throws \LogicException
     */
    public function createAttribute($name, $value = NULL): Attribute {
      [$prefix] = QualifiedName::split($name);
      if (empty($prefix)) {
        $node = parent::createAttribute($name);
      } else {
        $node = $this->createAttributeNS($this->namespaces()->resolveNamespace($prefix), $name);
      }
      if (NULL !== $value) {
        $node->value = $value;
      }
      return $node;
    }

    /**
     * Overload appendElement to add a text content and attributes directly.
     *
     * @param string $name
     * @param string $content
     * @param array|NULL $attributes
     * @return Element
     * @throws \LogicException
     */
    public function appendElement(string $name, $content = '', array $attributes = NULL): Element {
      $this->appendChild(
        $node = $this->createElement($name, $content, $attributes)
      );
      return $node;
    }

    /**
     * @param \DOMElement $node
     * @param string|array|NULL $content
     * @param array|NULL $attributes
     */
    private function appendAttributes(\DOMElement $node, $content = NULL, array $attributes = NULL): void {
      if (\is_array($content)) {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $attributes = (NULL === $attributes) ? $content : \array_merge($content, $attributes);
      }
      if (!empty($attributes)) {
        foreach ($attributes as $attributeName => $attributeValue) {
          $node->setAttribute($attributeName, $attributeValue);
        }
      }
    }

    /**
     * @param \DOMElement $node
     * @param string|array|NULL $content
     */
    private function appendContent(\DOMElement $node, $content = NULL): void {
      if (!((empty($content) && !\is_numeric($content)) || \is_array($content) )) {
        $node->appendChild($this->createTextNode((string)$content));
      }
    }

    /**
     * Allow to save XML fragments, providing a node list
     *
     * Overloading saveXML() with a removed type hint triggers an E_STRICT error,
     * so we the function needs a new name. :-(
     *
     * @param \DOMNode|\DOMNodeList|NULL $context
     * @param int $options
     * @return string
     */
    public function toXml($context = NULL, int $options = 0): string {
      if ($context instanceof \DOMNodeList) {
        $result = '';
        foreach ($context as $node) {
          $result .= $this->saveXML($node, $options);
        }
        return $result;
      }
      return $this->saveXML($context, $options);
    }

    /**
     * Allow to cast the document to string, returning the whole XML.
     *
     * @return string
     */
    public function __toString(): string {
      return $this->saveXML();
    }

    /**
     * Allow to save HTML fragments, providing a node list.
     *
     * This is an alias for the extended saveHTML() method. Make it
     * consistent with toXml()
     *
     * @param \DOMNode|\DOMNodeList|NULL $context
     * @return string
     */
    public function toHtml($context = NULL): string {
      return $this->saveHTML($context);
    }

    /**
     * Allow to save HTML fragments, providing a node list
     *
     * @param \DOMNode|\DOMNodeList|NULL $context
     * @return string
     */
    public function saveHTML($context = NULL): string {
      if ($context instanceof \DOMDocumentFragment) {
        $context = $context->childNodes;
      }
      if ($context instanceof \DOMNodeList) {
        $result = '';
        foreach ($context as $node) {
          $result .= parent::saveHTML($node);
        }
        return $result;
      }
      if (NULL === $context) {
        $result = '';
        $textOnly = TRUE;
        $elementCount = 0;
        foreach ($this->childNodes as $node) {
          $textOnly = $textOnly && $node instanceof \DOMText;
          $elementCount += $node instanceof \DOMElement ? 1 : 0;
          if ($node instanceof \DOMDocumentType) {
            $result .= parent::saveXML($node)."\n";
          } else {
            $result .= parent::saveHTML($node);
          }
        }
        return $textOnly || $elementCount > 1 ? $result : $result."\n";
      }
      return parent::saveHTML($context);
    }

    /**
     * Allow getElementsByTagName to use the defined namespaces.
     *
     * @param string $qualifiedName
     * @return \DOMNodeList
     * @throws \LogicException
     */
    public function getElementsByTagName($qualifiedName): \DOMNodeList {
      list($prefix, $localName) = QualifiedName::split($qualifiedName);
      $namespaceURI = (string)$this->namespaces()->resolveNamespace((string)$prefix);
      if ($namespaceURI !== '') {
        return $this->getElementsByTagNameNS($namespaceURI, $localName);
      }
      return parent::getElementsByTagName($localName);
    }

    /**
     * @param string|null $qualifiedName
     * @param string|null $publicId
     * @param string|null $systemId
     * @return \DOMDocumentType
     */
    public function createDocumentType(
      string $qualifiedName = NULL, string $publicId = NULL, string $systemId = NULL
    ): \DOMDocumentType {
      return (new Implementation())->createDocumentType($qualifiedName, (string)$publicId, (string)$systemId);
    }
  }
}
