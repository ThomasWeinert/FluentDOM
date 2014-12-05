<?php
namespace FluentDOM\Loader\Text {

  use FluentDOM\TestCase;

  require_once(__DIR__ . '/../../TestCase.php');

  class ICalendarTest extends TestCase {

    /**
     * @covers FluentDOM\Loader\Text\ICalendar
     */
    public function testSupportsExpectingTrue() {
      $loader = new ICalendar();
      $this->assertTrue($loader->supports('text/calendar'));
    }

    /**
     * @covers FluentDOM\Loader\Text\ICalendar
     */
    public function testSupportsExpectingFalse() {
      $loader = new ICalendar();
      $this->assertFalse($loader->supports('text/html'));
    }

    /**
     * @covers FluentDOM\Loader\Text\ICalendar
     */
    public function testLoad() {
      $loader = new ICalendar();
      $this->assertXmlStringEqualsXmlFile(
        __DIR__.'/TestData/icalendar-simple.xml',
        $loader->load(
          'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:uid1@example.com
DTSTAMP:19970714T170000Z
ORGANIZER;CN=John Doe:MAILTO:john.doe@example.com
DTSTART:19970714T170000Z
DTEND:19970715T035959Z
SUMMARY:Bastille Day Party
END:VEVENT
END:VCALENDAR',
          'text/calendar'
        )->saveXML()
      );
    }

    /**
     * @covers FluentDOM\Loader\Text\ICalendar
     */
    public function testLoadWithInvalidSourceExpectingNull() {
      $loader = new CSV();
      $this->assertNull(
        $loader->load(FALSE, 'text/calendar')
      );
    }
  }
}