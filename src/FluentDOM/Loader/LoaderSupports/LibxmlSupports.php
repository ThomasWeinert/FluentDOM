<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

declare(strict_types=1);

namespace FluentDOM\Loader\LoaderSupports {

  use FluentDOM\DOM\Document;
  use FluentDOM\Exceptions\InvalidSource\TypeFile as InValidFileSource;
  use FluentDOM\Exceptions\InvalidSource\TypeString as InvalidStringSource;
  use FluentDOM\Exceptions\LoadingError\FileNotLoaded;
  use FluentDOM\Loader\Libxml\Errors;
  use FluentDOM\Loader\LoaderOptions;
  use FluentDOM\Loader\LoaderSupports;

  trait LibxmlSupports {

    use LoaderSupports;

    /**
     * @throws \InvalidArgumentException
     */
    public function getOptions(iterable $options): LoaderOptions {
      $result = new LoaderOptions(
        $options,
        [
          LoaderOptions::CB_IDENTIFY_STRING_SOURCE => function($source) {
            return $this->startsWith($source, '<');
          }
        ]
      );
      $result[LoaderOptions::LIBXML_OPTIONS] = (int)$result[LoaderOptions::LIBXML_OPTIONS];
      $result[LoaderOptions::ENCODING] = empty($result[LoaderOptions::ENCODING]) ? 'utf-8' : $result[LoaderOptions::ENCODING];
      return $result;
    }

    /**
     * @throws InvalidStringSource
     * @throws InValidFileSource
     * @throws FileNotLoaded
     * @throws \Throwable
     */
    private function loadXmlDocument(string $source, iterable $options): Document {
      return (new Errors())->capture(
        function () use ($source, $options): Document {
          $settings = $this->getOptions($options);
          $settings->isAllowed($sourceType = $settings->getSourceType($source));
          $document = new Document();
          $document->preserveWhiteSpace = (bool)$settings[LoaderOptions::PRESERVE_WHITESPACE];
          switch ($sourceType) {
          case LoaderOptions::IS_FILE :
            if (!$document->load($source, $settings[LoaderOptions::LIBXML_OPTIONS])) {
              throw new FileNotLoaded($source);
            }
            break;
          case LoaderOptions::IS_STRING :
          default :
            $document->loadXML($source, $settings[LoaderOptions::LIBXML_OPTIONS]);
          }
          return $document;
        }
      );
    }
  }
}
