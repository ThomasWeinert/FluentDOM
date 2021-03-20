<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2021 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\Loader\Supports {

  use FluentDOM\DOM\Document;
  use FluentDOM\Exceptions\InvalidSource\TypeFile as InValidFileSource;
  use FluentDOM\Exceptions\InvalidSource\TypeString as InvalidStringSource;
  use FluentDOM\Exceptions\LoadingError\FileNotLoaded;
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
      $result[Options::LIBXML_OPTIONS] = (int)$result[Options::LIBXML_OPTIONS];
      $result[Options::ENCODING] = empty($result[Options::ENCODING]) ? 'utf-8' : $result[Options::ENCODING];
      return $result;
    }

    /**
     * @param string $source
     * @param array|\Traversable|Options $options
     * @return Document
     * @throws InvalidStringSource
     * @throws InValidFileSource
     * @throws FileNotLoaded
     * @throws \Throwable
     */
    private function loadXmlDocument(string $source, $options): Document {
      return (new Errors())->capture(
        function () use ($source, $options): Document {
          $settings = $this->getOptions($options);
          $settings->isAllowed($sourceType = $settings->getSourceType($source));
          $document = new Document();
          $document->preserveWhiteSpace = (bool)$settings[Options::PRESERVE_WHITESPACE];
          switch ($sourceType) {
          case Options::IS_FILE :
            if (!$document->load($source, $settings[Options::LIBXML_OPTIONS])) {
              throw new FileNotLoaded($source);
            }
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
