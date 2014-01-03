<?php

namespace FluentDOM {

  interface LoaderInterface {

    function supports($contentType);

    function getDocument($source);
  }
}