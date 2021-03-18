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

namespace FluentDOM\DOM\Node\QuerySelector {

  use FluentDOM\DOM\Document;
  use FluentDOM\DOM\Element;
  use FluentDOM\Xpath\Transformer;

  trait Implementation {

    /**
     * @param string $selector
     * @return Element|NULL
     */
    public function querySelector(string $selector): ?Element {
      $node = $this->querySelectorAll($selector)->item(0);
      return $node instanceof Element ? $node : NULL;
    }

    /**
     * @param string $selector
     * @return \DOMNodeList
     */
    public function querySelectorAll(string $selector): \DOMNodeList {
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
