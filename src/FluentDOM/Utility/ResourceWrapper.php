<?php
/**
 * Allow to load a stream resource using generated URI/context.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2017 FluentDOM Contributors
 */

namespace FluentDOM\Utility {

  /**
   * Allow to load a stream resource using generated URI/context.
   */
  class ResourceWrapper {

    /**
     * @var array
     */
    private static $__streams = [];

    /**
     * @var null|resource
     */
    private $__stream;
    /**
     * @var string
     */
    private $__id = '';

    /**
     * @var resource
     */
    public $context;

    /**
     * Return an URI to open the stream, the actual stream will be stored in the
     * class and removed by the destructor.
     *
     * @param resource $stream
     * @param string $protocol
     * @return string
     */
    public static function createURI($stream, string $protocol = 'fluentdom-resource'): string {
      self::register($protocol);
      do {
        $id = uniqid('fd', TRUE);
      } while(isset(self::$__streams[$id]));
      self::$__streams[$id] = $stream;
      return $protocol.'://'.$id;
    }

    /**
     * Return an URI and a context to open the stream, the actual stream will be stored in the
     * context.
     *
     * @param resource $stream
     * @param string $protocol
     * @return array
     */
    public static function createContext($stream, string $protocol = 'fluentdom-resource'): array {
      self::register($protocol);
      return [
        $protocol.'://context', stream_context_create([$protocol => ['stream' => $stream]])
      ];
    }

    /**
     * Register the stream wrapper for the given protocol, if it is not registered yet.
     *
     * @param string $protocol
     */
    private static function register($protocol) {
      if (!in_array($protocol, stream_get_wrappers(), TRUE)) {
        stream_wrapper_register($protocol, __CLASS__);
      }
    }

    /**
     * @param string $path
     * @param int $flags
     * @return array
     */
    public function url_stat(string $path , int $flags) {
      return [];
    }

    /**
     * @param string $path
     * @param string $mode
     * @param int $options
     * @param string $opened_path
     * @return bool
     */
    public function stream_open($path, $mode, $options, &$opened_path): bool {
      list($protocol, $id) = explode('://', $path);
      $context = stream_context_get_options($this->context);
      if (
        isset($context[$protocol], $context[$protocol]['stream']) &&
        is_resource($context[$protocol]['stream'])
      ) {
        $this->__stream = $context[$protocol]['stream'];
        return TRUE;
      }
      if (isset(self::$__streams[$id])) {
        $this->__stream = self::$__streams[$id];
        $this->__id = $id;
        return TRUE;
      }
      return FALSE;
    }

    /**
     * @param int $count
     * @return bool|string
     */
    public function stream_read(int $count) {
      return fread($this->__stream, $count);
    }

    /**
     * @param string $data
     * @return bool|int
     */
    public function stream_write(string $data) {
      return fwrite($this->__stream, $data);
    }

    /**
     * @return bool
     */
    public function stream_eof(): bool {
      return feof($this->__stream);
    }

    /**
     * @param int $offset
     * @param int $whence
     * @return int
     */
    public function stream_seek(int $offset, int $whence): int {
      return fseek($this->__stream, $offset, $whence);
    }

    public function __destruct() {
      if (isset($this->__id, self::$__streams[$this->__id])) {
        unset(self::$__streams[$this->__id]);
      }
    }
  }
}
