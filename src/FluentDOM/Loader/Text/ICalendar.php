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
  use FluentDOM\Loader\Supports;

  /**
   * Load a iCalendar (*.ics) file
   */
  class ICalendar extends ContentLines {

    use Supports;

    const XMLNS = 'urn:ietf:params:xml:ns:xcal';

    protected $_attributeProperties = [
      'CLASS' => 'PUBLIC',
      'PRODID' => NULL,
      'REV' => NULL,
      'UID' => NULL,
      'VERSION' => NULL
    ];

    protected $_parameters = [
      'ALTREP' => 'text',
      'CN' => 'text',
      'CUTYPE' => 'text',
      'DELEGATED-FROM' => 'cal-address',
      'DELEGATED-TO' => 'cal-address',
      'DIR' => 'uri',
      'ENCODING' => 'text',
      'FMTTYPE' => 'text',
      'FBTYPE' => 'text',
      'LANGUAGE' => 'language-tag',
      'MEMBER' => 'cal-address',
      'PARTSTAT' => 'text',
      'RANGE' => 'text',
      'RELATED' => 'text',
      'RELTYPE' => 'text',
      'ROLE' => 'text',
      'RSVP' => 'boolean',
      'SENT-BY' => 'cal-address',
      'TZID' => 'text'
    ];

    protected $_properties = [
      // General Properties
      'CALSCALE' => 'text',
      'METHOD' => 'text',
      'PRODID' => 'text',
      'VERSION' => 'text',
      // Descriptive Component Properties
      'ATTACH' => 'uri',
      'CATEGORIES' => 'text',
      'CLASS' => 'text',
      'COMMENT' => 'text',
      'DESCRIPTION' => 'text',
      'GEO' => 'text', // @todo [lat, lng] support
      'LOCATION' => 'text',
      'PERCENT-COMPLETE' => 'integer',
      'PRIORITY' => 'integer',
      'RESOURCES' => 'text',
      'STATUS' => 'text',
      'SUMMARY' => 'text',
      'COMPLETED' => 'date-time',
      'DTEND' => 'date-time',
      'DUE' => 'date-time',
      'DTSTART' => 'date-time',
      'DURATION' => 'duration',
      'FREEBUSY' => 'period',
      'TRANSP' => 'text',
      'TZID' => 'text',
      'TZNAME' => 'text',
      'TZOFFSETFROM' => 'utc-offset',
      'TZOFFSETTO' => 'utc-offset',
      'TZURL' => 'uri',
      // Relationship Component Properties
      'ATTENDEE' => 'cal-address',
      'CONTACT' => 'text',
      'ORGANIZER' => 'cal-address',
      'RECURRENCE-ID' => 'date-time',
      'RELATED-TO' => 'text',
      'URL' => 'uri',
      'UID' => 'uri',
      // Recurrence Component Properties
      'EXDATE' => 'date-time',
      'RDATE' => 'date-time',
      'RRULE' => 'recur', // @todo support special type
      // Alarm Component Properties
      'ACTION' => 'text',
      'REPEAT' => 'integer',
      'TRIGGER' => 'duration',
      // Change Management Component Properties
      'CREATED' => 'date-time',
      'DTSTAMP' => 'date-time',
      'LAST-MODIFIED' => 'date-time',
      'SEQUENCE' => 'integer'
    ];

    protected $_defaultType = 'text';

    /**
     * @var Element
     */
    private $_currentNode;

    /**
     * @return string[]
     */
    public function getSupported() {
      return array('text/calendar');
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
        $dom
          ->appendElement('icalendar')
          ->appendElement('vcalendar')
          ->append($this);
        return $dom;
      }
      return NULL;
    }
  }
}