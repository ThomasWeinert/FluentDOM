<?php

require('../../vendor/autoload.php');
$markup = new FluentDOMMarkupReplacer();

$html = <<<HTML
<head>
  <body>
    'Text can be //italic//, !!bold!!,
and contain ##quotes##, [[with caption|http://links]],
[[with caption and target|http://links|_self]],
and [[http://links/without-caption]].
Escaped literal markup sequences: \/\/, \!\!, \#\#.'
  </body>
</head>
HTML;

echo $markup->replace(
  FluentDOM($html, 'text/html')->find('//body')
);

class FluentDOMMarkupReplacer {

  /**
  * Splitting pattern, all parentheses will be captured if not empty.
  * so make sure only a single one matches each markup
  *
  * @var string
  */
  private $_splitPattern = '(
    (//(?:.+?)//)|
    (!!(?:.+?)!!)|
    (\\#\\#(?:.+?)\\#\\#)|
    (\\[\\[.*?\\]\\])
  )x';

  /**
  * List of replacements, first match is used to covert the element
  *
  * pattern => array(element name, attributes, text content)
  *
  * If a pattern matches the configuration is used to create the
  * replacement node. The configuration constists of an element name
  * a list of attributes and the text content of the new element.
  *
  * Each of the attribute values and the text content is a replacement pattern.
  *
  * @var array(string=>array(string,array(string=>string),string))
  */
  private $_replacements = array(
    '(^//(.*)//$)D' => array(
      'em', array(), '$1'
    ),
    '(^!!(.*)!!$)D' => array(
      'strong', array(), '$1'
    ),
    '(^##(.*)##$)D' => array(
      'span', array('class' => 'quoteblock'), '$1'
    ),
    '(^\\[\\[(.+)\\|(.+)\\|(.+)\\]\\]$)D' => array(
      'a', array('href' => '$2', 'target' => '$3'), '$1'
    ),
    '(^\\[\\[(.+)\\|(.+)\\]\\]$)D' => array(
      'a', array('href' => '$2'), '$1'
    ),
    '(^\\[\\[(.+)\\]\\]$)D' => array(
      'a', array('href' => '$1'), '$1'
    )
  );

  /**
  * A list of replacement done on the strings to replace escapin sequences
  *
  * var array(string=>string)
  */
  private $_escapings = array(
    '\\/\\/' => '//',
    '\\!\\!' => '!!',
    '\\#\\#' => '##'
  );

  /**
  * Replace markup in all text nodes inside the current selection
  *
  * @param FluentDOM\Query $fd
  * @return FluentDOM\Query
  */
  public function replace(FluentDOM\Query $fd) {
    $fd
      ->find('descendant-or-self::text()')
      ->each(array($this, 'replaceNode'));
    return $fd->spawn();
  }

  /**
  * Callback used to replace a single text node.
  *
  * @param DOMText $node
  */
  public function replaceNode(DOMText $node) {
    if (preg_match($this->_splitPattern, $node->nodeValue)) {
      // split using the pattern, but capture the delimiter strings
      $parts = preg_split(
        $this->_splitPattern,
        $node->nodeValue,
        -1,
        PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
      );
      $items = array();
      foreach ($parts as $part) {
        $items[] = $this->createNodes($node->ownerDocument, $part);
      }
      // replace the text node
      FluentDOM($node)->replaceWith($items);
    } else {
      FluentDOM($node)
        ->text(
          strtr(FluentDOM($node)->text(), $this->_escapings
        )
      );
    }
  }

  /**
  * Create nodes for the given string
  *
  * @param DOMDocument $dom
  * @param string $string
  * @return DOMElement
  */
  private function createNodes($dom, $string) {
    foreach ($this->_replacements as $pattern => $replacement) {
      if (preg_match($pattern, $string)) {
        $node = $dom->createElement($replacement[0]);
        foreach ($replacement[1] as $attributeName => $attributePattern) {
          $node->setAttribute(
            $attributeName,
            preg_replace(
              $pattern, $attributePattern, $string
            )
          );
        }
        $text = $dom->createTextNode(
          preg_replace(
            $pattern, $replacement[2], $string
          )
        );
        $node->appendChild($text);
        return $node;
      }
    }
    return $dom->createTextNode(
      strtr($string, $this->_escapings)
    );
  }
}
