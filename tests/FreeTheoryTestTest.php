<?php
namespace TheoryTest\Tests;

use TheoryTest\Car\FreeTheoryTest;

class FreeTheoryTestTest extends TheoryTestTest
{
  
    protected $theoryTest;
    
    protected function setUp() : void
    {
        if (!session_id()) {
            session_start();
        }
        $_SESSION['current_test'] = 1;
        $_SESSION['test'.$_SESSION['current_test']] = false;
        $this->theoryTest = new FreeTheoryTest(self::$db, self::$config, self::$template, self::$user);
    }
    
    protected function tearDown() : void
    {
        $this->theoryTest = null;
    }
    
    /**
     * @covers TheoryTest\Car\FreeTheoryTest::buildTest
     * @covers TheoryTest\Car\FreeTheoryTest::chooseQuestions
     * @covers TheoryTest\Car\FreeTheoryTest::clearCookies
     * @covers TheoryTest\Car\FreeTheoryTest::clearSettings
     * @covers TheoryTest\Car\FreeTheoryTest::createNewTest
     * @covers TheoryTest\Car\FreeTheoryTest::getQuestions
     * @covers TheoryTest\Car\FreeTheoryTest::getTest
     * @covers TheoryTest\Car\FreeTheoryTest::getUserAnswers
     * @covers TheoryTest\Car\FreeTheoryTest::setTest
     * @covers TheoryTest\Car\FreeTheoryTest::setTestName
     * @covers TheoryTest\Car\FreeTheoryTest::startTest
     * @covers TheoryTest\Car\FreeTheoryTest::testStarted
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::existingScript
     * @covers TheoryTest\Car\TheoryTest::getJavascriptLocation
     * @covers TheoryTest\Car\TheoryTest::getTestName
     * @covers TheoryTest\Car\TheoryTest::numQuestions
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\User::checkUserAccess
     * @covers TheoryTest\Car\User::setUserSettings
     * @covers TheoryTest\Car\User::getUserSettings
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     */
    public function testCreateNewTest()
    {
        parent::testCreateNewTest();
    }
    
    /**
     * @covers TheoryTest\Car\FreeTheoryTest::getTest
     * @covers TheoryTest\Car\FreeTheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setPassmark
     * @covers TheoryTest\Car\TheoryTest::getPassmark
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     */
    public function testSetPassmark()
    {
        parent::testSetPassmark();
    }

    /**
     * @covers TheoryTest\Car\FreeTheoryTest::getTest
     * @covers TheoryTest\Car\FreeTheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setTestType
     * @covers TheoryTest\Car\TheoryTest::getTestType
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     */
    public function testSetTestType()
    {
        parent::testSetTestType();
    }
}
