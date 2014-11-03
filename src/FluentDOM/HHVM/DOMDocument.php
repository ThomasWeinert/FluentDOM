<?php

namespace FluentDOM\HHVM {

  /**
   * This class is a workaround for two HHVM issues:
   *
   * https://github.com/facebook/hhvm/issues/1848
   * https://github.com/facebook/hhvm/issues/2962
   *
   * Basically it looks like the create* methods are incomplete. Importing the node after
   * creating it fixes the problem.
   *
   * @package FluentDOM\HHVM
   */
  abstract class DOMDocument extends \DOMDocument {

    protected $_isHHVM = NULL;

    /**
     * @param string $version
     * @param string $encoding
     */
    public function __construct($version = '1.0', $encoding = 'UTF-8') {
      parent::__construct($version, $encoding);
      $this->_isHHVM = defined('HHVM_VERSION');
    }

    /**
     *
     * @param \DOMElement $node
     * @return \DOMNode
     */
    private function repairNodeObject($node) {
      if ($this->_isHHVM) {
        return $this->importNode($node, TRUE);
      }
      return $node;
    }

    /**
     * @param string $name
     * @return \DOMAttr
     */
    public function createAttribute($name) {
      return $this->repairNodeObject(parent::createAttribute($name));
    }

    /**
     * @param string $namespace
     * @param string $name
     * @return \DOMAttr
     */
    public function createAttributeNS($namespace, $name) {
      return $this->repairNodeObject(parent::createAttributeNS($namespace, $name));
    }

    /**
     * @param string $data
     * @return \DOMCdataSection
     */
    public function createCdataSection($data) {
      return $this->repairNodeObject(parent::createCdataSection($data));
    }

    /**
     * @param string $data
     * @return \DOMComment
     */
    public function createComment($data) {
      return $this->repairNodeObject(parent::createComment($data));
    }

    /**
     * @return \DOMDocumentFragment
     */
    public function createDocumentFragment() {
      return $this->repairNodeObject(parent::createDocumentFragment());
    }

    /**
     * @param string $name
     * @param null $content
     * @return \DOMElement
     */
    public function createElement($name, $content = NULL) {
      return $this->repairNodeObject(parent::createElement($name, $content));
    }

    /**
     * @param string $namespace
     * @param string $name
     * @param null|string $content
     * @return \DOMElement
     */
    public function createElementNS($namespace, $name, $content = NULL) {
      return $this->repairNodeObject(parent::createElementNS($namespace,$name, $content));
    }

    /**
     * @param string $target
     * @param null $data
     * @return \DOMProcessingInstruction
     */
    public function createProcessingInstruction($target, $data = NULL) {
      return $this->repairNodeObject(parent::createProcessingInstruction($target, $data));
    }

    /**
     * @param string $content
     * @return \DOMText
     */
    public function createTextNode($content) {
      return $this->repairNodeObject(parent::createTextNode($content));
    }
  }
}