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
  class ICalendar implements Loadable {

    use Supports;

    const XMLNS = 'urn:ietf:params:xml:ns:xcal';

    /**
     * @var string
     */
    private $_linePattern = "(
        (?P<name>[A-Z\d-]+)
        (?:
          ;
          (?P<paramName>[A-Z\d-]+)
          =
          (?P<paramValue>[^:]+)
        )?
        :
        (?P<value>.+)
      )x";

    /**
     * @var Element
     */
    private $_currentNode;

    /**
     * @return string[]
     */
    public function getSupported() {
      return array('text/calendar');
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
        $dom->registerNamespace('xCal', self::XMLNS);
        $this->_currentNode = $dom->appendElement('xCal:iCalendar');
        $lineBuffer = '';
        foreach ($lines as $line) {
          $firstChar = substr($line, 0, 1);
          if ($lineBuffer != '' && $firstChar != ' ' && $firstChar != "\t") {
            if ($token = $this->parseLine($lineBuffer)) {
              $this->appendToken($token);
            }
            $lineBuffer = '';
          }
          $lineBuffer .= ltrim($line);
        }
        if ($lineBuffer != '' && ($token = $this->parseLine($lineBuffer))) {
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
        return new MapIterator($source, function($line) { return rtrim($line, "\r\n"); } );
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
        $this->_currentNode = $this->_currentNode->appendElement('xCal:'.$token['value']);
        break;
      case 'END' :
        $this->_currentNode = $this->_currentNode->parentNode;
        break;
      default :
        $itemNode = $this->_currentNode->appendElement(
          'xCal:'.$token['name'],
          str_replace(
            array('\\,', '\\n'),
            array(',', "\n\n"),
            $token['value']
          )
        );
        if (!empty($token['paramName'])) {
          $paramName = strtolower($token['paramName']);
          $itemNode->setAttribute(
            $paramName, $token['paramValue']
          );
          if ($paramName == 'tzid') {
            $timezone = new \DateTimeZone($token['paramValue']);
            $offset = $timezone->getOffset(new \DateTime($token['value']));
            $offsetHours = floor(abs($offset) / 3600);
            $offsetMinutes = floor((abs($offset) - $offsetHours * 3600) / 60);
            $itemNode->setAttribute(
              'tzoffset',
              sprintf(
                '%s%02d:%02d',
                $offset > 0 ? '+' : '-',
                $offsetHours,
                $offsetMinutes
              )
            );
          }
        }
      }
    }

    /**
     * Parse the token line using a PCRE
     *
     * @param string $line
     * @return array|FALSE
     */
    private function parseLine($line) {
      if (preg_match($this->_linePattern, $line, $parts)) {
        return $parts;
      }
      return FALSE;
    }
  }
}