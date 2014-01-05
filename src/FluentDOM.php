<?php

class FluentDOM {

  private static $_load = TRUE;

  public static function Query($source = NULL, $contentType = 'text/xml') {
    if (self::$_load && !class_exists('FluentDOM\Query')) {
      include(__DIR__.'/_require.php');
    }
    $query = new FluentDOM\Query();
    if (isset($source)) {
      $query->load($source, $contentType);
    }
    return $query;
  }
}