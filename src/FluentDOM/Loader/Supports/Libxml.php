<?php

namespace FluentDOM\Loader\Supports {

  use FluentDOM\Document;
  use FluentDOM\Loader\Options;
  use FluentDOM\Loader\Supports;
  use FluentDOM\Loader\Libxml\Errors;

  trait Libxml {

    use Supports;

    /**
     * @param array|\Traversable|Options $options
     * @return Options
     */
    public function getOptions($options) {
      $result = new Options(
        $options,
        [
          Options::CB_IDENTIFY_STRING_SOURCE => function($source) {
            return $this->startsWith($source, '<');
          }
        ]
      );
      $result['libxml'] = (int)$result['libxml'];
      $result['encoding'] = empty($result['encoding']) ? 'utf-8' : $result['encoding'];
      return $result;
    }

    /**
     * @param string $source
     * @param string $contentType
     * @param array|\Traversable|Options $options
     * @return Document
     */
    private function loadXmlDocument($source, $contentType, $options) {
      return (new Errors())->capture(
        function () use ($source, $contentType, $options) {
          $document = new Document();
          $document->preserveWhiteSpace = FALSE;
          $settings = $this->getOptions($options);
          $settings->isAllowed($sourceType = $settings->getSourceType($source));
          switch ($sourceType) {
          case Options::IS_FILE :
            $document->load($source, $settings[Options::LIBXML_OPTIONS]);
            break;
          case Options::IS_STRING :
          default :
            $document->loadXML($source, $settings[Options::LIBXML_OPTIONS]);
          }
          return $document;
        }
      );
    }
  }
}