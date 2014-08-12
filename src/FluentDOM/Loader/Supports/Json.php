<?php

namespace FluentDOM\Loader\Supports {

  use FluentDOM\Exceptions\JsonError;
  use FluentDOM\Loader\Supports;

  trait Json {

    use Supports;

    /**
     * @param string $source
     * @throws JsonError
     * @return mixed
     */
    private function getJson($source)  {
      $json = FALSE;
      if (!$this->startsWith($source, '{[')) {
        $source = file_get_contents($source);
      }
      if ($this->startsWith($source, '{[')) {
        $json = json_decode($source);
        if (!($json || is_array($json))) {
          throw new JsonError(
            is_callable('json_last_error') ? json_last_error() : -1
          );
        }
      }
      return $json;
    }

    private function getValueAsString($value) {
      if (is_bool($value)) {
        return $value ? 'true' : 'false';
      } else {
        return (string)$value;
      }
    }
  }
}