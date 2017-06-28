<?php

class IniLoader implements FluentDOM\Loadable {

  public function supports($contentType) {
    return in_array($contentType, array('ini', 'text/ini'));
  }

  public function load($source, $contentType = 'text/ini', $options = []) {
    if (is_string($source) && $this->supports($contentType)) {
      if (!file_exists($source)) {
        throw new InvalidArgumentException('File not found: '. $source);
      }
      if ($iniFile = parse_ini_file($source)) {
        $document = new FluentDOM\Document();
        $root = $document->appendChild($document->createElement('ini'));
        $this->_arrayToNodes($document, $root, $iniFile);
        return $document;
      }
    }
    return FALSE;
  }

  public function loadFragment($source, $contentType = 'text/ini', $options = []) {
    throw new \FluentDOM\Exceptions\InvalidFragmentLoader(self::class);
  }

  private function _arrayToNodes(FluentDOM\Document $document, DOMNode $node, $data) {
    if (is_array($data)) {
      foreach ($data as $key => $val) {
        if (preg_match('(^\d+$)', $key)) {
          $nodeName = $node->nodeName;
          if (substr($nodeName, -1) == 's') {
            $nodeName = substr($nodeName, 0, -1);
          }
          $childNode = $document->createElement($nodeName);
          $this->_arrayToNodes($document, $childNode, $val);
          $node->appendChild($childNode);
        } elseif (is_array($val)) {
          $childNode = $document->createElement($key);
          $this->_arrayToNodes($document, $childNode, $val);
          $node->appendChild($childNode);
        } elseif (preg_match('([\r\n\t])', $val)) {
          $childNode = $document->createElement($key);
          $textNode = $document->createTextNode($val);
          $childNode->appendChild($textNode);
          $node->appendChild($childNode);
        } else {
          $node->appendChild($document->createElement($key, $val));
        }
      }
    } elseif (!empty($data)) {
      $textNode = $document->createTextNode($data);
      $node->appendChild($textNode);
    }
  }

}

