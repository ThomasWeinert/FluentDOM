<?php

require_once(dirname(__FILE__).'/../../FluentDOMLoader.php');

class FluentDOMIniLoader implements FluentDOMLoader {

  public function load($source, $contentType) {
    if (is_string($source) &&
        in_array($contentType, array('ini', 'text/ini'))) {

      if (!file_exists($source)) {
        throw new InvalidArgumentException('File not found: '. $source);
      }

      if ($iniFile = parse_ini_file($source)) {
        $dom = new DOMDocument();
        $root = $dom->appendChild($dom->createElement('ini'));
        $this->_arrayToNodes($dom, $root, $iniFile);
        return $dom;
      }
    }
    return FALSE;
  }

  protected function _arrayToNodes(&$dom, &$node, $data) {
    if (is_array($data)) {
      foreach ($data as $key => $val) {
        if (preg_match('(^\d+$)', $key)) {
          $nodeName = $node->nodeName;
          if (substr($nodeName, -1) == 's') {
            $nodeName = substr($nodeName, 0, -1);
          }
          $childNode = $dom->createElement($nodeName);
          $this->_arrayToNodes($dom, $childNode, $val);
          $node->appendChild($childNode);
        } elseif (is_array($val)) {
          $childNode = $dom->createElement($key);
          $this->_arrayToNodes($dom, $childNode, $val);
          $node->appendChild($childNode);
        } elseif (preg_match('([\r\n\t])', $val)) {
          $childNode = $dom->createElement($key);
          $textNode = $dom->createTextNode($val);
          $childNode->appendChild($textNode);
          $node->appendChild($childNode);
        } else {
          $node->appendChild($dom->createElement($key, $val));
        }
      }
    } elseif (!empty($data)) {
      $textNode = $dom->createTextNode($data);
      $node->appendChild($textNode);
    }
  }

}

?>