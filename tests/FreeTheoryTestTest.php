<?php
namespace TheoryTest\Tests;

use TheoryTest\Car\FreeTheoryTest;

class FreeTheoryTestTest extends SetUp
{
  
    protected $theoryTest;
    
    protected function setUp() : void
    {
        parent::setUp();
        if (!session_id()) {
            session_start();
        }
        $_SESSION['current_test'] = 1;
        $_SESSION['test'.$_SESSION['current_test']] = false;
        $this->theoryTest = new FreeTheoryTest($this->db, $this->config, $this->template, $this->user);
    }
    
    protected function tearDown() : void
    {
        parent::tearDown();
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
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getTestType
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     */
    public function testCreateNewTest()
    {
        $this->markTestIncomplete();
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
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getTestType
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     */
    public function testSetPassmark()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers TheoryTest\Car\FreeTheoryTest::getTest
     * @covers TheoryTest\Car\FreeTheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setTestType
     * @covers TheoryTest\Car\TheoryTest::getTestType
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     */
    public function testSetTestType()
    {
        $this->markTestIncomplete();
    }
}
