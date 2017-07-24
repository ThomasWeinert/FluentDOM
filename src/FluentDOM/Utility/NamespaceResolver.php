<?php

namespace FluentDOM\Utility {

  interface NamespaceResolver {

    /**
     * @param string $prefix
     * @return string|NULL
     */
    function resolveNamespace(string $prefix);
  }
}