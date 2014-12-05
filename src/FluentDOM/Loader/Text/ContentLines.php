<?php

namespace FluentDOM\Loader\Text {

  use FluentDOM\Iterators\MapIterator;
  use FluentDOM\Loadable;
  use FluentDOM\Appendable;
  use FluentDOM\Element;

  abstract class ContentLines implements Loadable, \IteratorAggregate, Appendable {

    protected $_attributeProperties = [];

    protected $_parameters = [];

    protected $_properties = [];

    protected $_defaultType = 'unknown';

    protected $_lines = [];

    public function getIterator() {
      return new ContentLines\Iterator(
        new \IteratorIterator($this->_lines)
      );
    }

    public function appendTo(Element $parent) {
      $currentNode = $parent;
      foreach ($this as $token) {
        switch ($token->name) {
        case 'BEGIN' :
          $currentNode = $currentNode->appendElement(strtolower($token->value));
          break;
        case 'END' :
          $currentNode = $currentNode->parentNode;
          break;
        case 'XML' :
          // @todo implement XML property
          break;
        default :
          if (array_key_exists($token->name, $this->_attributeProperties)) {
            $currentNode->setAttribute(strtolower($token->name), (string)$token->value);
          } else {
            $itemNode = $currentNode->appendElement(strtolower($token->name));
            if (!empty($token->parameters)) {
              $parametersNode = $this->appendParametersNode($itemNode);
              foreach ($token->parameters as $name => $parameter) {
                $parameterNode = $parametersNode->appendElement(strtolower($name));
                $this->appendValueNode(
                  $parameterNode,
                  strtolower(
                    isset($this->_parameters[$name])
                      ? $this->_parameters[$name] : $this->_defaultType
                  ),
                  $parameter
                );
              }
            }
            if (!empty($token->value)) {
              $tokenType = $token->type
                ?: (isset($this->_properties[$token->name])
                ? $this->_properties[$token->name]
                : 'unknown');
              if (is_array($tokenType)) {
                $elements = explode(';', (string)$token->value);
                foreach ($elements as $index => $element) {
                  $elementName = isset($tokenType[$index])
                    ? $tokenType[$index] : end($tokenType[$token->name]);
                  if (!empty($element)) {
                    $itemNode->appendElement($elementName, $element);
                  }
                }
              } else {
                $this->appendValueNode($itemNode, strtolower($tokenType), $token->value);
              }
            }
          }
        }
      }
    }

    private function appendParametersNode(Element $parent) {
      return $parent->appendElement('parameters');
    }

    private function appendValueNode(Element $parent, $type, $values) {
      if (is_array($values) || $values instanceof \Traversable) {
        foreach ($values as $value) {
          $parent->appendElement($type, $value);
        }
      } elseif (!empty($values)) {
        $parent->appendElement($type, (string)$values);
      }
    }

    protected function getLines($source) {
      $result = null;
      if ($this->isFile($source)) {
        $file = new \SplFileObject($source);
        $file->setFlags(\SplFileObject::DROP_NEW_LINE);
        return $file;
      } elseif (is_string($source)) {
        $result = new \ArrayIterator(explode("\n", $source));
      } elseif (is_array($source)) {
        $result = new \ArrayIterator($source);
      } elseif ($source instanceof \Traversable) {
        $result = $source;
      }
      if (empty($result)) {
        return null;
      } else {
        return new MapIterator($result, function($line) { return rtrim($line, "\r\n"); } );
      }
    }

    private function isFile($source) {
      return (is_string($source) && (FALSE === strpos($source, "\n")));
    }
  }
}