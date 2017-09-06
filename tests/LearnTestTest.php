<?php
namespace TheoryTest\Tests;

use DBAL\Database;
use Smarty;
use UserAuth\User;
use TheoryTest\Car\LearnTest;
use PHPUnit\Framework\TestCase;

class LearnTestTest extends TestCase{
    
    protected static $db;
    protected static $user;
    protected static $template;
    protected static $learnTest;
    
    public function setUp() {
        self::$db = new Database($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'], $GLOBALS['DB_DBNAME']);
        self::$template = new Smarty();
        self::$user = new User(self::$db);
        self::$learnTest = new LearnTest(self::$db, self::$template, self::$user);
    }
    
    public function tearDown() {
        unset(self::$db);
        unset(self::$template);
        unset(self::$user);
        unset(self::$learnTest);
    }
    
    public function testConnection() {
        $this->markTestIncomplete();
    }
}
