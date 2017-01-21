<?php

namespace FluentDOM {

  interface NamespaceResolver {

    /**
     * @param string $prefix
     * @return string
     */
    function resolveNamespace($prefix);
  }
}