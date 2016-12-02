<?php

namespace FluentDOM\Loader\Supports {

  use FluentDOM\Document;
  use FluentDOM\DocumentFragment;
  use FluentDOM\Exceptions\LoadingError;
  use FluentDOM\Loader\Options;
  use FluentDOM\Loader\Supports;
  use FluentDOM\Loader\Result;

  trait Libxml {

    use Supports;

    /**
     * @param array|\Traversable|Options $options
     * @return Options
     */
    public function getOptions($options) {
      $result = new Options(
        $options,
        [
          'identifyStringSource' => function($source) {
            return $this->startsWith($source, '<');
          }
        ]
      );
      $result['libxml'] = (int)$result['libxml'];
      return $result;
    }
  }
}