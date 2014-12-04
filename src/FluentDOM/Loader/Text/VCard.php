<?php
/**
 * Load a iCalendar (*.ics) file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\Text {

  use FluentDOM\Document;
  use FluentDOM\Element;
  use FluentDOM\Iterators\MapIterator;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Supports;

  /**
   * Load a iCalendar (*.ics) file
   */
  class VCard implements Loadable {

    use Supports;

    const XMLNS = 'urn:ietf:params:xml:ns:vcard-4.0';

    /**
     * @var Element
     */
    private $_currentNode;

    /**
     * @return string[]
     */
    public function getSupported() {
      return array('text/vcard');
    }

    /**
     * @see Loadable::load
     * @param \PDOStatement $source
     * @param string $contentType
     * @param array $options
     * @return Document|NULL
     */
    public function load($source, $contentType, array $options = []) {
      if ($this->supports($contentType) && ($lines = $this->getLines($source))) {
        $dom = new Document('1.0', 'UTF-8');
        $dom->registerNamespace('', self::XMLNS);
        $this->_currentNode = $dom->appendElement('vcards');
        $tokens = new Parser\PropertyLines($lines);
        foreach ($tokens as $token) {
          $this->appendToken($token);
        }
        return $dom;
      }
      return NULL;
    }

    private function getLines($source) {
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

    /**
     * Append the token data to the dom, If the name is BEGIN an new group element ist created
     * and set as the current element. END switches the current element to its parent
     *
     * All other tokens are appended as with ther name in lowecase as element name.
     * Parameters are converted to attributes.
     *
     * @param array $token
     */

    private function appendToken(array $token) {
      switch ($token['name']) {
      case 'BEGIN' :
        $this->_currentNode = $this->_currentNode->appendElement(strtolower($token['value']));
        break;
      case 'END' :
        $this->_currentNode = $this->_currentNode->parentNode;
        break;
      default :
        $itemNode = $this->_currentNode->appendElement(strtolower($token['name']));
        if (!empty($token['parameters'])) {
          $parametersNode = $itemNode->appendElement('parameters');
          foreach ($token['parameters'] as $name => $parameter) {
            if ($name === 'VALUE') {
              continue;
            }
            $parametersNode
              ->appendElement(strtolower($name))
              ->appendElement($parameter['type'], $parameter['value']);
          }
        }
        if (!empty($token['parameters']['VALUE'])) {
          $itemNode->appendElement(
            $token['parameters']['VALUE']['type'],
            str_replace(
              array('\\,', ';', '\\n'),
              array(',', "\n", "\n\n"),
              $token['parameters']['VALUE']['value']
            )
          );
        } elseif (!empty($token['value'])) {
          $itemNode->appendElement(
            'text',
            str_replace(
              array('\\,', ';', '\\n'),
              array(',', "\n", "\n\n"),
              $token['value']
            )
          );
        }
      }
    }
  }
}