<?php

namespace FluentDOM\DOM {

  class EntityReference extends \DOMEntityReference implements Node {

    use Node\StringCast;
    use Node\Xpath;
  }
}