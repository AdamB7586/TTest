<?php
namespace TheoryTest\Tests;

use DBAL\Database;
use Smarty;
use TheoryTest\Car\User;
use TheoryTest\Car\TheoryTest;
use PHPUnit\Framework\TestCase;

class TheoryTestTest extends TestCase{
    
    protected static $db;
    protected static $user;
    protected static $template;
    protected static $theoryTest;
    
    public static function setUpBeforeClass() {
        self::$db = new Database($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'], $GLOBALS['DB_DBNAME']);
        if(!self::$db->isConnected()){
             $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        self::$db->query(file_get_contents(dirname(dirname(__FILE__)).'/database/database_mysql.sql'));
        self::$db->query(file_get_contents(dirname(dirname(__FILE__)).'/vendor/adamb/user/database/database_mysql.sql'));
        self::$db->query(file_get_contents(dirname(__FILE__).'/sample_data/data.sql'));
        self::$template = new Smarty();
        self::$user = new User(self::$db);
        self::$theoryTest = new TheoryTest(self::$db, self::$template, self::$user);
    }
    
    public function testConnection() {
        $this->markTestIncomplete();
    }
}
