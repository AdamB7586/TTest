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
    
    /**
     * 
     */
    public static function setUpBeforeClass() {
        self::$db = new Database($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'], $GLOBALS['DB_DBNAME']);
        if(!self::$db->isConnected()){
             $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        if(self::$db->count('users') < 1){
            self::$db->query(file_get_contents(dirname(dirname(__FILE__)).'/vendor/adamb/user/database/database_mysql.sql'));
            self::$db->query(file_get_contents(dirname(dirname(__FILE__)).'/vendor/adamb/hcldc/database/mysql_database.sql'));
            self::$db->query(file_get_contents(dirname(dirname(__FILE__)).'/vendor/adamb/hcldc/tests/sample_data/mysql_data.sql'));
            self::$db->query(file_get_contents(dirname(dirname(__FILE__)).'/database/database_mysql.sql'));
            self::$db->query(file_get_contents(dirname(__FILE__).'/sample_data/data.sql'));
        }
        self::$template = new Smarty();
        self::$template->setCacheDir('/cache/')->setCompileDir('/cache/');
        self::$user = new User(self::$db);
        self::$user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        self::$theoryTest = new TheoryTest(self::$db, self::$template, self::$user);
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::createNewTest
     * @covers TheoryTest\Car\TheoryTest::clearSettings
     * @covers TheoryTest\Car\TheoryTest::setTest
     * @covers TheoryTest\Car\TheoryTest::setTestName
     * @covers TheoryTest\Car\TheoryTest::anyExisting
     * @covers TheoryTest\Car\TheoryTest::chooseQuestions
     * @covers TheoryTest\Car\TheoryTest::buildTest
     * @covers TheoryTest\Car\TheoryTest::getQuestions
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::checkSettings
     * @covers TheoryTest\Car\TheoryTest::createQuestionHTML
     * @covers TheoryTest\Car\TheoryTest::getFirstQuestion
     * @covers TheoryTest\Car\TheoryTest::getJavascriptLocation
     * @covers TheoryTest\Car\TheoryTest::getQuestionData
     * @covers TheoryTest\Car\TheoryTest::getTestName
     * @covers TheoryTest\Car\TheoryTest::getTestType
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::numQuestions
     * @covers TheoryTest\Car\TheoryTest::questionNo
     * @covers TheoryTest\Car\TheoryTest::updateTestProgress
     * @covers TheoryTest\Car\User::checkUserAccess
     * @covers TheoryTest\Car\User::setUserSettings
     * @covers TheoryTest\Car\User::getUserSettings
     */
    public function testCreateNewTest() {
        $newTest = self::$theoryTest->createNewTest();
        $this->assertStringStartsWith('<div class="row">', $newTest);
        $this->assertNotContains('<span id="qnum">1</span> of <span id="totalq">0</span>', $newTest);
    }
}
