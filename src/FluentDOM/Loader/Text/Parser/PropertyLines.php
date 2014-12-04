<?php

namespace FluentDOM\Loader\Text\Parser {


  class PropertyLines implements \Iterator {

    /**
     * @var string
     */
    private $_linePattern = "(
        (?P<name>[A-Z\\d-]+)
        (?P<parameters>
          (?:;
            (?:[A-Z\\d-]+)
            =
            (?:
              (?:\"[^\"]*\")|
              (?:uri:[a-zA-Z-\\d]+:[^:;]+)|
              [^:;]+
            )
          )+
        )?
        (?::
          (?P<value>.*)
        )?
      )x";

    private $_parametersPattern = "(
      ;
      (?P<paramName>[A-Z\\d-]+)
      =
      (?P<paramValue>
        (?P<quotedValue>\"[^\"]*\")|
        (?P<typedValue>
          (?P<paramType>uri):(?P<paramValueTyped>[a-z-\\d]+:[^:;]+)
        )|
        [^:;]+
      )
    )x";

    /**
     * @var \Iterator
     */
    private $_lines = NULL;

    private $_buffer = '';

    private $_key = -1;
    private $_current = NULL;

    public function __construct(\Traversable $lines) {
      $this->_lines = new \IteratorIterator($lines);
    }

    public function rewind() {
      $this->_lines->rewind();
      $this->_key = -1;
      $this->_current = NULL;
      $this->next();
    }

    public function next() {
      $this->_lines->next();
      $this->_current = NULL;
      while ($this->_lines->valid()) {
        $line = $this->_lines->current();
        $firstChar = substr($line, 0, 1);
        if ($this->_buffer != '' && $firstChar != ' ' && $firstChar != "\t") {
          $this->_current = $this->parseLine($this->_buffer);
          $this->_buffer = ltrim($line);
          if ($this->_current) {
            return;
          }
        }
        $this->_buffer .= ltrim($line);
        $this->_lines->next();
      }
    }

    public function key() {
      return $this->_key;
    }

    public function current() {
      return $this->_current;
    }

    public function valid() {
      return $this->_current !== NULL;
    }

    /**
     * Parse the token line using a PCRE
     *
     * @param string $line
     * @return array|FALSE
     */
    private function parseLine($line) {
      if (preg_match($this->_linePattern, $line, $parts)) {
        $result = [
          'name' => $parts['name'],
          'value' => isset($parts['value']) ? $parts['value'] : '',
          'parameters' => []
        ];
        if (
          !empty($parts['parameters']) &&
          preg_match_all($this->_parametersPattern, $parts['parameters'], $matches, PREG_SET_ORDER)
        ) {
          foreach ($matches as $match) {
            if (empty($match['paramName'])) {
              continue;
            } elseif (isset($match['typedValue'])) {
              $paramType = $match['paramType'];
              $paramValue = $this->unescape($match['paramValueTyped']);
            } else {
              $paramType = 'text';
              if (isset($match['quotedValue'])) {
                $paramValue = $this->unquote($match['quotedValue']);
              } else {
                $paramValue = $this->unescape($match['paramValue']);
              }
            }
            $result['parameters'][$match['paramName']] = [
              'type' => $paramType,
              'value' => $paramValue
            ];
          }
        }
        return $result;
      }
      return NULL;
    }

    private function unescape($string) {
      return str_replace(
        array('\\,', ';', '\\n'),
        array(',', "\n", "\n\n"),
        $string
      );
    }

    private function unquote($string) {
      return str_replace('\\n', "\n", substr($string, 1, -1));
    }
  }
}