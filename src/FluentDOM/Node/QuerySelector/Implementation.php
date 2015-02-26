<?php

namespace FluentDOM\Node\QuerySelector {

  use FluentDOM\Document;
  use FluentDOM\Element;
  use FluentDOM\Node\QuerySelector;

  /**
   * Class Implementation
   * @package FluentDOM\Node\QuerySelector
   *
   * @codeCoverageIgnore
   */
  class Implementation implements QuerySelector {

    /**
     * @param string $selector
     * @return Element|null
     */
    public function querySelector($selector) {
      $node = $this->querySelectorAll($selector)->item(0);
      return $node instanceof Element ? $node : NULL;
    }

    /**
     * @param string $selector
     * @return \DOMNodeList
     */
    public function querySelectorAll($selector) {
      $builder = \FluentDOM::getXPathTransformer();
      /** @var Document|Element $this */
      return $this->evaluate(
        $builder->toXpath($selector, $this instanceof \DOMDocument)
      );
    }
  }
}