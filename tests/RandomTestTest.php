<?php
namespace TheoryTest\Tests;

use TheoryTest\Car\RandomTest;

class RandomTestTest extends SetUp {
    
    protected static $theoryTest;
    
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        self::$user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        self::$theoryTest = new RandomTest(self::$db, self::$config, self::$template, self::$user);
    }
    
    public function testConnection() {
        $this->markTestIncomplete();
    }
}
