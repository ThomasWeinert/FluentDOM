<?php
/**
 * Load a iCalendar (*.ics) file
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright (c) 2009-2014 Bastian Feder, Thomas Weinert
 */

namespace FluentDOM\Loader\Text {

  use FluentDOM\Document;
  use FluentDOM\Element;
  use FluentDOM\Iterators\MapIterator;
  use FluentDOM\Loadable;
  use FluentDOM\Loader\Supports;

  /**
   * Load a iCalendar (*.ics) file
   */
  class VCard extends ContentLines {

    use Supports;

    const XMLNS = 'urn:ietf:params:xml:ns:vcard-4.0';

    protected $_attributeProperties = [
      'CLASS' => 'PUBLIC',
      'PRODID' => NULL,
      'REV' => NULL,
      'UID' => NULL,
      'VERSION' => NULL
    ];

    protected $_parameters = [
      'LANGUAGE' => 'language-tag',
      'PREF' => 'integer',
      'ALTID' => 'text',
      'PID' => 'text',
      'TYPE' => 'text',
      'MEDIATYPE' => 'text',
      'CALSCALE' => 'text',
      'SORT-AS' => 'text',
      'GEO' => 'uri',

      'LABEL' => 'text'
    ];

    protected $_properties = [
      // General Properties
      'SOURCE' => 'uri',
      'KIND' => 'text',
      // Identification Properties
      'FN' => 'text',
      'N' => ['family', 'given', 'other', 'prefix', 'suffix'],
      'NICKNAME' => 'text',
      'PHOTO' => 'uri',
      'BDAY' => 'date',
      'ANNIVERSARY' => 'date',
      'GENDER' => ['sex', 'identity'],
      // Delivery Addressing Properties
      'ADR' => ['pobox', 'ext', 'street', 'locality', 'region', 'code', 'country'],
      // Communications Properties
      'TEL' => 'text',
      'EMAIL' => 'text',
      'IMPP' => 'text',
      'LANG' => 'language-tag',
      // Geographical Properties
      'TZ' => 'text',
      'GEO' => 'uri',
      // Organizational Properties
      'TITLE' => 'text',
      'ROLE' => 'text',
      'LOGO' => 'uri',
      'ORG' => ['orgnam', 'orgunit'],
      'MEMBER' => 'uri',
      'RELATED' => 'uri',
      // Explanatory Properties
      'CATEGORIES' => 'text',
      'NOTE' => 'text',
      'PRODID' => 'text',
      'REV' => 'text',
      'SOUND' => 'uri',
      'UID' => 'uri',
      'CLIENTPIDMAP' => ['pid', 'uri'],
      'URL' => 'uri',
      'VERSION' => 'text',
      // Security Properties,
      'KEY' => 'uri',
      // Calendar Properties
      'FBURL' => 'uri',
      'CALADRURI' => 'uri',
      'CALURI' => 'uri'
    ];

    /**
     * @return string[]
     */
    public function getSupported() {
      return array('text/vcard');
    }

    /**
     * @see Loadable::load
     * @param \PDOStatement $source
     * @param string $contentType
     * @param array $options
     * @return Document|NULL
     */
    public function load($source, $contentType, array $options = []) {
      if ($this->supports($contentType) && ($this->_lines = $this->getLines($source))) {
        $dom = new Document('1.0', 'UTF-8');
        $dom->registerNamespace('', self::XMLNS);
        $dom->appendElement('vcards')->append($this);
        return $dom;
      }
      return NULL;
    }
  }
}