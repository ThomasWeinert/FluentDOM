<?php
/**
 * Create list of nodes for a FluentDOM\Nodes object from different values
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Nodes {

  use FluentDOM\Constraints;
  use FluentDOM\Document;
  use FluentDOM\Nodes;

  /**
    * Create list of nodes for a FluentDOM\Nodes object from different values
    */
  class Builder {

    /**
     * @var Nodes
     */
    private $_nodes;

    /**
     * @param Nodes $nodes
     */
    public function __construct(Nodes $nodes) {
      $this->_nodes = $nodes;
    }

    /**
     * @return Nodes
     */
    public function getOwner() {
      return $this->_nodes;
    }

    /**
     * @param mixed $content
     * @param bool $includeTextNodes
     * @param int $limit
     * @return array|\Traversable null
     */
    private function getNodeList(
      $content,
      $includeTextNodes = TRUE,
      $limit = -1
    ) {
      if ($callback = Constraints::isCallable($content)) {
        $content = $callback();
      }
      if ($content instanceof \DOMElement) {
        return array($content);
      } elseif ($includeTextNodes && Constraints::isNode($content)) {
        return array($content);
      } elseif (Constraints::isNodeList($content)) {
        return $this->getLimitedArray($content, $limit);
      }
      return NULL;
    }

    /**
     * Match selector against context and return matched elements.
     *
     * @param mixed $selector
     * @param \DOMNode $context optional, default value NULL
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getTargetNodes($selector, \DOMNode $context = NULL) {
      if ($nodes = $this->getNodeList($selector)) {
        return $nodes;
      } elseif (is_string($selector)) {
        $result = $this->getOwner()->xpath(
          $this->getOwner()->prepareSelector(
            $selector,
            Nodes::CONTEXT_SELF
          ),
          $context
        );
        if (!($result instanceof \Traversable)) {
          throw new \InvalidArgumentException('Given selector did not return an node list.');
        }
        return iterator_to_array($result);
      }
      throw new \InvalidArgumentException('Invalid selector');
    }

    /**
     * Convert a given content into and array of nodes
     *
     * @param mixed $content
     * @param boolean $includeTextNodes
     * @param integer $limit
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @return array
     */
    public function getContentNodes($content, $includeTextNodes = TRUE, $limit = -1) {
      $result = FALSE;
      if ($nodes = $this->getNodeList($content, $includeTextNodes, $limit)) {
        $result = $nodes;
      } elseif (is_string($content)) {
        $result = $this->getXmlFragment($content, $includeTextNodes, $limit);
      }
      if (!is_array($result) || empty($result)) {
        throw new \InvalidArgumentException('Invalid/empty content parameter.');
      } else {
        //if a node is not in the current document import it
        $document = $this->getOwner()->getDocument();
        foreach ($result as $index => $node) {
          if ($node->ownerDocument !== $document) {
            $result[$index] = $document->importNode($node, TRUE);
          }
        }
      }
      return $result;
    }

    /**
     * Convert $content to a DOMElement. If $content contains several elements use the first.
     *
     * @param mixed $content
     * @return \DOMElement
     */
    public function getContentElement($content) {
      $contentNodes = $this->getContentNodes($content, FALSE, 1);
      return $contentNodes[0];
    }

    /**
     * Convert a given content xml string into and array of nodes
     *
     * @param string $xml
     * @param boolean $includeTextNodes
     * @param integer $limit
     * @throws \UnexpectedValueException
     * @return array
     */
    public function getXmlFragment($xml, $includeTextNodes = TRUE, $limit = -1) {
      $xml = $this->getContentAsString($xml);
      if (!$xml) {
        return array();
      }
      $result = array();
      $fragment = $this->getOwner()->getDocument()->createDocumentFragment();
      if ($fragment->appendXML($xml)) {
        for ($i = $fragment->childNodes->length - 1; $i >= 0; $i--) {
          $element = $fragment->childNodes->item($i);
          if ($element instanceof \DOMElement ||
            ($includeTextNodes && Constraints::isNode($element))) {
            array_unshift($result, $element);
            $element->parentNode->removeChild($element);
          }
        }
      }
      return $this->getLimitedArray($result, $limit);
    }

    /**
     * @param string $html
     * @throws \UnexpectedValueException
     * @return array
     */
    public function getHtmlFragment($html) {
      $html = $this->getContentAsString($html);
      if (!$html) {
        return array();
      }
      $htmlDom = new Document();
      $status = libxml_use_internal_errors(TRUE);
      $htmlDom->loadHtml('<html-fragment>'.$html.'</html-fragment>');
      libxml_clear_errors();
      libxml_use_internal_errors($status);
      $result = array();
      $nodes = $htmlDom->evaluate('//html-fragment[1]/node()');
      $document = $this->getOwner()->getDocument();
      if ($nodes instanceof \Traversable) {
        foreach ($nodes as $node) {
          $result[] = $document->importNode($node, TRUE);
        }
      }
      return $result;
    }

    /**
     * @param string $content
     * @return string bool
     * @throws \UnexpectedValueException
     */
    private function getContentAsString($content) {
      if (is_string($content) || method_exists($content, '__toString')) {
        $content = (string)$content;
        return empty($content) ? FALSE : $content;
      }
      throw new \UnexpectedValueException('Invalid document fragment');
    }

    /**
     * Get the inner xml of a given node or in other words the xml of all children.
     *
     * @param \DOMNode $context
     * @return string
     */
    public function getInnerXml($context) {
      $result = '';
      $dom = $this->getOwner()->getDocument();
      $nodes = $this->getOwner()->xpath(
        '*|text()[normalize-space(.) != ""]|self::text()[normalize-space(.) != ""]',
        $context
      );
      foreach ($nodes as $child) {
        $result .= $dom->saveXML($child);
      }
      return $result;
    }

    /**
     * Get the inner and outer wrapper nodes. Simple means that they are the
     * same nodes.
     *
     * @param \DOMElement $template
     * @param bool $simple
     * @return \DOMElement[]
     */
    public function getWrapperNodes($template, &$simple) {
      $wrapper = $template->cloneNode(TRUE);
      $targets = NULL;
      $target = NULL;
      if (!$simple) {
        // get the first element without child elements.
        $targets = $this->getOwner()->xpath('.//*[count(*) = 0]', $wrapper);
      }
      if ($simple || $targets->length === 0) {
        $target = $wrapper;
        $simple = TRUE;
      } elseif ($targets instanceof \DOMNodeList) {
        $target = $targets->item(0);
      }
      return array($target, $wrapper);
    }

    /**
     * @param array|\Traversable $nodes
     * @param int $limit
     * @return array
     */
    private function getLimitedArray($nodes, $limit = -1) {
      if ($limit > 0) {
        if (is_array($nodes)) {
          return array_slice($nodes, 0, $limit);
        } else {
          return iterator_to_array(
            new \LimitIterator(
              new \IteratorIterator($nodes), 0, $limit
            ),
            FALSE
          );
        }
      }
      return is_array($nodes) ? $nodes : iterator_to_array($nodes, FALSE);
    }
  }
}