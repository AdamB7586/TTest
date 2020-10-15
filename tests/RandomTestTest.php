<?php
namespace TheoryTest\Tests;

use TheoryTest\Car\RandomTest;

class RandomTestTest extends SetUp
{
    
    protected $theoryTest;
    
    protected function setUp() : void
    {
        parent::setUp();
        $this->user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        $this->theoryTest = new RandomTest($this->db, $this->config, $this->template, $this->user);
    }
    
    
    
    /**
     * @covers TheoryTest\Car\RandomTest::createNewTest
     * @covers TheoryTest\Car\RandomTest::chooseQuestions
     * @covers TheoryTest\Car\RandomTest::setTestName
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::clearSettings
     * @covers TheoryTest\Car\TheoryTest::setTest
     * @covers TheoryTest\Car\TheoryTest::setTestName
     * @covers TheoryTest\Car\TheoryTest::anyExisting
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
     * @covers TheoryTest\Car\TheoryTest::existingScript
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::addAudio
     * @covers TheoryTest\Car\TheoryTest::alert
     * @covers TheoryTest\Car\TheoryTest::audioButton
     * @covers TheoryTest\Car\TheoryTest::currentQuestion
     * @covers TheoryTest\Car\TheoryTest::dsaExplanation
     * @covers TheoryTest\Car\TheoryTest::extraContent
     * @covers TheoryTest\Car\TheoryTest::flagHintButton
     * @covers TheoryTest\Car\TheoryTest::getLastQuestion
     * @covers TheoryTest\Car\TheoryTest::getOptions
     * @covers TheoryTest\Car\TheoryTest::getScript
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::nextQuestion
     * @covers TheoryTest\Car\TheoryTest::numComplete
     * @covers TheoryTest\Car\TheoryTest::prevQuestion
     * @covers TheoryTest\Car\TheoryTest::questionFlagged
     * @covers TheoryTest\Car\TheoryTest::questionPrim
     * @covers TheoryTest\Car\TheoryTest::reviewAnswers
     * @covers TheoryTest\Car\TheoryTest::reviewButton
     * @covers TheoryTest\Car\TheoryTest::existingLayout
     * @covers TheoryTest\Car\TheoryTest::getSeconds
     * @covers TheoryTest\Car\TheoryTest::getTime
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::checkIfLast
     * @covers TheoryTest\Car\TheoryTest::getMark
     * @covers TheoryTest\Car\TheoryTest::getNextPrevButtonArray
     * @covers TheoryTest\Car\TheoryTest::createImage
     * @covers TheoryTest\Car\TheoryTest::getImagePath
     * @covers TheoryTest\Car\TheoryTest::getImageRootPath
     * @covers TheoryTest\Car\User::checkUserAccess
     * @covers TheoryTest\Car\User::setUserSettings
     * @covers TheoryTest\Car\User::getUserSettings
     */
    public function testCreateNewTest()
    {
         $newTest = $this->theoryTest->createNewTest();
         $this->assertStringStartsWith('<div class="row">', $newTest);
        //$this->markTestIncomplete();
    }
    
    /**
     * @covers TheoryTest\Car\RandomTest::createTestReport
     * @covers TheoryTest\Car\TheoryTest::createTestReport
     */
    public function testCreateReport()
    {
        $this->markTestIncomplete();
    }
}
