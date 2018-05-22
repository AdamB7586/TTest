<?php
namespace TheoryTest\Tests;

Use TheoryTest\Car\Review;

class ReviewTest extends SetUp {
    
    protected static $review;
    
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        self::$user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        self::$review = new Review(self::$db, self::$config, self::$template, self::$user);
    }
    
    public function testExample() {
        $this->markTestIncomplete();
    }
}
