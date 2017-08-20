<?php

namespace FluentDOM\Utility {

  class ResourceWrapper {

    private static $__streams = [];

    private $__stream = NULL;
    private $__id = '';

    public $context;

    public static function createURI($stream, $protocol = 'fluentdom-resource') {
      self::register($protocol);
      do {
        $id = uniqid('fd', TRUE);
      } while(isset(self::$__streams[$id]));
      self::$__streams[$id] = $stream;
      return $protocol.'://'.$id;
    }

    public static function createContext($stream, $protocol = 'fluentdom-resource') {
      self::register($protocol);
      return [
        $protocol.'://context', stream_context_create([$protocol => ['stream' => $stream]])
      ];
    }

    private static function register($protocol) {
      if (!in_array($protocol, stream_get_wrappers(), TRUE)) {
        stream_wrapper_register($protocol, __CLASS__);
      }
    }

    public function url_stat(string $path , int $flags) {
      return [];
    }

    public function stream_stat() {
      return fstat($this->__stream);
    }

    public function stream_open($path, $mode, $options, &$opened_path) {
      list($protocol, $id) = explode('://', $path);
      $options = stream_context_get_options($this->context);
      if (
        isset($options[$protocol], $options[$protocol]['stream']) &&
        is_resource($options[$protocol]['stream'])
      ) {
        $this->__stream = $options[$protocol]['stream'];
        return TRUE;
      }
      if (isset(self::$__streams[$id])) {
        $this->__stream = self::$__streams[$id];
        $this->__id = $id;
        return TRUE;
      }
      return FALSE;
    }

    public function stream_read($count) {
      return fread($this->__stream, $count);
    }

    public function stream_write($data) {
      return fwrite($this->__stream, $data);
    }

    public function stream_tell() {
      return ftell($this->__stream);
    }

    public function stream_eof() {
      return feof($this->__stream);
    }

    public function stream_seek($offset, $whence) {
      return fseek($this->__stream, $offset, $whence);
    }

    public function __destruct() {
      if (isset($this->__id, self::$__streams[$this->__id])) {
        unset(self::$__streams[$this->__id]);
      }
    }
  }
}
