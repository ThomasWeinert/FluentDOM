<?php

namespace FluentDOM\Utility {

  interface NamespaceResolver {

    /**
     * @param string $prefix
     * @return string
     */
    function resolveNamespace($prefix);
  }
}