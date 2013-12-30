<?php
namespace FluentDOM {

  require_once(__DIR__.'/../src/_require.php');

  abstract class TestCase extends \PHPUnit_Framework_TestCase {

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
    protected function assertFluentDOMQueryEqualsXMLFile($functionName, $actual) {
      $fileName = $this->getFileName($functionName, 'tgt');
      $this->assertInstanceOf(__NAMESPACE__.'\\Query', $actual);
      $this->assertXmlStringEqualsXmlFile($fileName, (string)$actual);
    }

    /**
     * @param string $functionName
     * @throws \UnexpectedValueException
     * @return Query
     */
    protected function getQueryFixtureFromFunctionName($functionName) {
      $fileName = $this->getFileName($functionName, 'src');
      if (!file_exists($fileName)) {
        throw new \UnexpectedValueException('File Not Found: '. $fileName);
      }
      $dom = new \DOMDocument();
      $dom->load($fileName);
      $fd = new Query();
      return $fd->load($dom);
    }

    /**
     * @param string $string
     * @param null $xpath
     * @return Query
     */
    protected function getQueryFixtureFromString($string = NULL, $xpath = NULL) {
      $fd = new Query();
      if (!empty($string)) {
        $dom = new \DOMDocument();
        $dom->loadXML($string);
        $fd->load($dom);
        if (!empty($xpath)) {
          $query = new Xpath($dom);
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
    protected function getFileName($functionName, $type) {
      return sprintf(
        '%s/TestData/%s%s.%s.xml',
        empty($this->_directory) ? __DIR__ : $this->_directory,
        strToLower(substr($functionName, 4, 1)),
        substr($functionName, 5),
        $type
      );
    }
  }
}