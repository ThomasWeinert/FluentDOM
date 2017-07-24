<?php

namespace FluentDOM\DOM\Node\QuerySelector {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;
  use FluentDOM\DOM\Node\QuerySelector;
  use FluentDOM\DOM\Xpath\Transformer;

  trait Implementation {

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
        $builder->toXpath(
          $selector,
          $this instanceof \DOMDocument ? Transformer::CONTEXT_DOCUMENT : Transformer::CONTEXT_CHILDREN
        )
      );
    }
  }
}