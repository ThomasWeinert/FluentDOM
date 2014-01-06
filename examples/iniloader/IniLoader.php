<?php

require_once(dirname(__FILE__).'/../../src/_require.php');

class IniLoader implements FluentDOM\Loadable {

  public function supports($contentType) {
    return in_array($contentType, array('ini', 'text/ini'));
  }

  public function load($source, $contentType = 'text/ini') {
    if (is_string($source) && $this->supports($contentType)) {

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

  private function _arrayToNodes(DOMDocument $dom, DOMNode $node, $data) {
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

