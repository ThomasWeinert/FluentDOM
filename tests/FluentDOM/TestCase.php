<?php
namespace FluentDOM {

  use FluentDOM\DOM\Xpath;
  use PHPUnit\Framework\Error\Notice;
  use PHPUnit\Framework\Error\Warning;
  use PHPUnit\Framework\Error\Deprecated;

  require_once __DIR__.'/../../vendor/autoload.php';

  abstract class TestCase extends \PHPUnit\Framework\TestCase {

    const XML = '
      <items version="1.0">
        <group id="1st">
          <item index="0">text1</item>
          <item index="1">text2</item>
          <item index="2">text3</item>
        </group>
        <html>
          <div class="test1 test2">class testing</div>
          <div class="test2">class testing</div>
          <div>class testing</div>
        </html>
      </items>
    ';

    const HTML = '
      <html>
        <body>
          <p>Paragraph One</p>
          <p>Paragraph Two</p>
        </body>
      </html>
    ';

    protected $_directory = __DIR__;

    public function expectError($severity) {
      $levels = [
        E_NOTICE => [Notice::class],
        E_WARNING => [Warning::class],
        E_DEPRECATED => [Deprecated::class]
      ];
      if ($levels[$severity]) {
        foreach ($levels[$severity] as $class) {
          if (class_exists($class)) {
            $this->expectException($class);
            break;
          }
        }
      } else {
        throw new \InvalidArgumentException('Can not map severity to exception class.');
      }
    }

    /**
     * Tests, if the content of a file equals the given string
     *
     * The the file to be compared is identified by the given function name.
     *
     * @param string $functionName
     * @param string $actual
     *
     * @uses getFileName()
     */
    protected function assertFluentDOMQueryEqualsXMLFile($functionName, Query $actual) {
      $fileName = $this->getFileName($functionName, 'tgt');
      $this->assertXmlStringEqualsXmlFile($fileName, (string)$actual->formatOutput());
    }

    /**
     * @param string $functionName
     * @throws \UnexpectedValueException
     * @return Query
     */
    protected function getQueryFixtureFromFunctionName($functionName): Query {
      $fileName = $this->getFileName($functionName, 'src');
      if (!file_exists($fileName)) {
        throw new \UnexpectedValueException('File Not Found: '. $fileName);
      }
      $document = new \DOMDocument();
      $document->load($fileName);
      $fd = new Query();
      return $fd->load($document);
    }

    /**
     * @param string|NULL $string
     * @param string|NULL $xpath
     * @return Query
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    protected function getQueryFixtureFromString($string = NULL, $xpath = NULL): Query {
      $fd = new Query();
      /** @noinspection IsEmptyFunctionUsageInspection */
      if (!empty($string)) {
        $document = new \DOMDocument();
        $document->loadXML($string);
        $fd->load($document);
        /** @noinspection IsEmptyFunctionUsageInspection */
        if (!empty($xpath)) {
          $query = new Xpath($document);
          $nodes = $query->evaluate($xpath);
          $fd = $fd->spawn();
          $fd->push($nodes);
        }
      }
      return $fd;
    }

    /**
     * @param string $functionName
     * @param string $type
     * @return string
     */
    protected function getFileName($functionName, $type): string {
      /** @noinspection SubStrUsedAsArrayAccessInspection */
      return sprintf(
        '%s/TestData/%s%s.%s.xml',
        empty($this->_directory) ? __DIR__ : $this->_directory,
        strtolower(substr($functionName, 4, 1)),
        substr($functionName, 5),
        $type
      );
    }
  }
}
