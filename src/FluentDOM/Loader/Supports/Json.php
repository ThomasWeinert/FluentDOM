<?php

namespace FluentDOM\Loader\Supports {

  use FluentDOM\Exceptions\JsonError;
  use FluentDOM\Loader\Supports;

  trait Json {

    use Supports;

    /**
     * @param mixed $source
     * @param string $contentType
     * @throws JsonError
     * @return mixed
     */
    private function getJson($source, $contentType)  {
      if ($this->supports($contentType)) {
        if (is_string($source)) {
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
        return $source;
      }
      return FALSE;
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