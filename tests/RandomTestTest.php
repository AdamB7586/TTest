<?php
namespace TheoryTest\Tests;

use DBAL\Database;
use Smarty;
use TheoryTest\Car\User;
use TheoryTest\Car\RandomTest;
use PHPUnit\Framework\TestCase;

class RandomTestTest extends TestCase{
    
    protected static $db;
    protected static $user;
    protected static $template;
    protected static $randomTest;
    
    public static function setUpBeforeClass() {
        self::$db = new Database($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'], $GLOBALS['DB_DBNAME']);
        if(!self::$db->isConnected()){
             $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        self::$template = new Smarty();
        self::$user = new User(self::$db);
        self::$randomTest = new RandomTest(self::$db, self::$template, self::$user);
    }
    
    public function testConnection() {
        $this->markTestIncomplete();
    }
}
