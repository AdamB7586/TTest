<?php
namespace TheoryTest\Tests;

use DBAL\Database;
use Smarty;
use UserAuth\User;
use TheoryTest\Car\TheoryTest;
use PHPUnit\Framework\TestCase;

class TheoryTestTest extends TestCase{
    
    protected static $db;
    protected static $user;
    protected static $template;
    protected static $theoryTest;
    
    public function setUp() {
        self::$db = new Database($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'], $GLOBALS['DB_DBNAME']);
        if(!self::$db->isConnected()){
             $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        self::$template = new Smarty();
        self::$user = new User(self::$db);
        self::$theoryTest = new TheoryTest(self::$db, self::$template, self::$user);
    }
    
    public function tearDown() {
        unset(self::$db);
        unset(self::$template);
        unset(self::$user);
        unset(self::$theoryTest);
    }
    
    public function testConnection() {
        $this->markTestIncomplete();
    }
}
