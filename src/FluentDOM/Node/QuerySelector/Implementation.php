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
      $hasPhpCss = class_exists('PhpCss');
      $hasCssSelector = class_exists('Symfony\Component\CssSelector\CssSelector');
      $isDocumentContext = $this instanceof \DOMDocument;
      if ($hasPhpCss) {
        $options = ($isDocumentContext)
          ? \PhpCss\Ast\Visitor\Xpath::OPTION_USE_CONTEXT_DOCUMENT
          : \PhpCss\Ast\Visitor\Xpath::OPTION_USE_CONTEXT_SELF;
        $expression = \PhpCss::toXpath($selector, $options);
      } elseif ($hasCssSelector) {
        CssSelector::enableHtmlExtension();
        $expression = (($isDocumentContext) ? '/' : './').CssSelector::toXpath($selector);
      } else {
        throw new \LogicException(
          'Install "carica/phpcss" or "symfony/css-selector" to support css selectors.'
        );
      }
      /** @var Document|Element $this */
      return $this->evaluate($expression);
    }
  }
}