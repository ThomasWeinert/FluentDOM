<?php

namespace FluentDOM\Loader\Supports {

  use FluentDOM\DOM\Document;
  use FluentDOM\Loader\Libxml\Errors;
  use FluentDOM\Loader\Options;
  use FluentDOM\Loader\Supports;

  trait Libxml {

    use Supports;

    /**
     * @param array|\Traversable|Options $options
     * @return Options
     * @throws \InvalidArgumentException
     */
    public function getOptions($options): Options {
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
     * @param array|\Traversable|Options $options
     * @return Document
     * @throws \FluentDOM\Exceptions\InvalidSource\TypeString
     * @throws \FluentDOM\Exceptions\InvalidSource\TypeFile
     */
    private function loadXmlDocument(string $source, $options): Document {
      return (new Errors())->capture(
        function () use ($source, $options) {
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