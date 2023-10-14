<?php
/*
 * FluentDOM
 *
 * @link https://thomas.weinert.info/FluentDOM/
 * @copyright Copyright 2009-2023 FluentDOM Contributors
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
declare(strict_types=1);

namespace FluentDOM\Utility {

  /**
   * Allow to load a stream resource using generated URI/context.
   */
  class ResourceWrapper {

    private static array $_streams = [];

    private mixed $_stream = NULL;

    private string $_id = '';

    /**
     * @var resource
     */
    public mixed $context = NULL;

    /**
     * Return an URI to open the stream, the actual stream will be stored in the
     * class and removed by the destructor.
     *
     * @param resource $stream
     * @param string $protocol
     * @return string
     */
    public static function createURI(mixed $stream, string $protocol = 'fluentdom-resource'): string {
      self::register($protocol);
      do {
        $id = \uniqid('fd', TRUE);
      } while(isset(self::$_streams[$id]));
      self::$_streams[$id] = $stream;
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
    public static function createContext(mixed $stream, string $protocol = 'fluentdom-resource'): array {
      self::register($protocol);
      return [
        $protocol.'://context', \stream_context_create([$protocol => ['stream' => $stream]])
      ];
    }

    /**
     * Register the stream wrapper for the given protocol, if it is not registered yet.
     *
     * @param string $protocol
     */
    private static function register(string $protocol): void {
      if (!\in_array($protocol, \stream_get_wrappers(), TRUE)) {
        \stream_wrapper_register($protocol, __CLASS__);
      }
    }

    /**
     * @param string $path
     * @param int $flags
     * @return array
     */
    public function url_stat(
      /** @noinspection PhpUnusedParameterInspection */
      string $path , int $flags
    ): array {
      return [];
    }

    public function stream_open(
      /** @noinspection PhpUnusedParameterInspection */
      string $path, string $mode, int $options, string &$opened_path = NULL
    ): bool {
      [$protocol, $id] = \explode('://', $path);
      $context = \stream_context_get_options($this->context);
      if (
        isset($context[$protocol]['stream']) &&
        \is_resource($context[$protocol]['stream'])
      ) {
        $this->_stream = $context[$protocol]['stream'];
        return TRUE;
      }
      if (isset(self::$_streams[$id])) {
        $this->_stream = self::$_streams[$id];
        $this->_id = $id;
        return TRUE;
      }
      return FALSE;
    }

    public function stream_read(int $count): bool|string {
      return \fread($this->_stream, $count);
    }

    public function stream_write(string $data): bool|int {
      return \fwrite($this->_stream, $data);
    }

    public function stream_eof(): bool {
      return \feof($this->_stream);
    }

    public function stream_seek(int $offset, int $whence): int {
      return \fseek($this->_stream, $offset, $whence);
    }

    public function __destruct() {
      if (isset($this->_id, self::$_streams[$this->_id])) {
        unset(self::$_streams[$this->_id]);
      }
    }
  }
}
