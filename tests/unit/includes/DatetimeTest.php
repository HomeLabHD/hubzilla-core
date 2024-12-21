<?php
/**
 * tests function from include/datetime.php
 *
 * @package test.util
 */

use Zotlabs\Tests\Unit\UnitTestCase;

class DatetimeTest extends UnitTestCase {

	// Test when the timestamp is in the past
	public function test_relative_time_past() {
		$now = new DateTime('2024-12-07 00:00:00');
		$timestamp = datetime_convert(date_default_timezone_get(), 'UTC', '2023-12-05 10:30:00');
		$result = relative_time($timestamp, $now);
		$this->assertEquals('1 year ago', $result);
	}

	// Test when the timestamp is in the future
	public function test_relative_time_future() {
		$now = new DateTime('2024-12-07 00:00:00');
		$timestamp = datetime_convert(date_default_timezone_get(), 'UTC', '2024-12-09 12:00:00');
		$result = relative_time($timestamp, $now);
		$this->assertEquals('in 2 days', $result);
	}

	// Test for "now" case (timestamp exactly equal to current time)
	public function test_relative_time_now() {
		$now = new DateTime('2024-12-07 00:00:00');
		$timestamp = datetime_convert(date_default_timezone_get(), 'UTC', '2024-12-07 00:00:00');
		$result = relative_time($timestamp, $now);
		$this->assertEquals('now', $result);
	}

	// Test for future time with smaller units (e.g., minutes)
	public function test_relative_time_future_minutes() {
		$now = new DateTime('2024-12-07 10:30:00');
		$timestamp = datetime_convert(date_default_timezone_get(), 'UTC', '2024-12-07 10:35:00');
		$result = relative_time($timestamp, $now);
		$this->assertEquals('in 5 minutes', $result);
	}

	// Test for past time with smaller units (e.g., seconds)
	public function test_relative_time_past_seconds() {
		$now = new DateTime('2024-12-07 10:30:00');
		$timestamp = datetime_convert(date_default_timezone_get(), 'UTC', '2024-12-07 10:29:58');
		$result = relative_time($timestamp, $now);
		$this->assertEquals('2 seconds ago', $result);
	}
}


