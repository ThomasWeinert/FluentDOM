<?php

namespace FluentDOM\Loader {

  use FluentDOM\Document;
  use FluentDOM\LoaderInterface;

  class XmlString implements LoaderInterface {

    public function supports($contentType) {
      switch ($contentType) {
      case 'text/xml' :
        return TRUE;
      }
      return FALSE;
    }

    public function load($source) {
      if (0 === strpos($source, '<')) {
        $dom = new Document();
        $dom->loadXML($source);
        return $dom;
      }
      return NULL;
    }

  }
}