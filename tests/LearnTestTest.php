<?php
namespace TheoryTest\Tests;

use TheoryTest\Car\LearnTest;

class LearnTestTest extends SetUp
{
    
    protected $learnTest;
    
    public function setUp() : void
    {
        parent::setUp();
        $this->user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        $this->learnTest = new LearnTest($this->db, $this->config, $this->template, $this->user);
    }
    
    /**
     * @covers TheoryTest\Car\LearnTest::__construct
     * @covers TheoryTest\Car\LearnTest::setTables
     * @covers TheoryTest\Car\LearnTest::getSectionInfo
     * @covers TheoryTest\Car\LearnTest::getTestInfo
     * @covers TheoryTest\Car\LearnTest::chooseStudyQuestions
     * @covers TheoryTest\Car\LearnTest::createNewTest
     * @covers TheoryTest\Car\LearnTest::currentQuestion
     * @covers TheoryTest\Car\LearnTest::getFirstQuestion
     * @covers TheoryTest\Car\LearnTest::getQuestions
     * @covers TheoryTest\Car\LearnTest::getTestName
     * @covers TheoryTest\Car\LearnTest::getUserAnswers
     * @covers TheoryTest\Car\LearnTest::numQuestions
     * @covers TheoryTest\Car\LearnTest::questionNo
     * @covers TheoryTest\Car\LearnTest::updateTestProgress
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::clearSettings
     * @covers TheoryTest\Car\TheoryTest::setTest
     * @covers TheoryTest\Car\TheoryTest::setTestName
     * @covers TheoryTest\Car\TheoryTest::buildTest
     * @covers TheoryTest\Car\TheoryTest::checkSettings
     * @covers TheoryTest\Car\TheoryTest::createQuestionHTML
     * @covers TheoryTest\Car\TheoryTest::getJavascriptLocation
     * @covers TheoryTest\Car\TheoryTest::getQuestionData
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getTestType
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\User::checkUserAccess
     * @covers TheoryTest\Car\User::getUserSettings
     * @covers TheoryTest\Car\User::setUserSettings
     */
    public function testCreateNewTest()
    {
        $learnTest = $this->learnTest->createNewTest();
        $this->assertStringStartsWith('<div class="row">', $learnTest);
        //$this->markTestIncomplete();
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::createQuestionHTML
     */
    public function testCreateQuestionHTML()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers TheoryTest\Car\LearnTest::addAnswer
     * @covers TheoryTest\Car\LearnTest::removeAnswer
     * @covers TheoryTest\Car\LearnTest::replaceAnswer
     * @covers TheoryTest\Car\LearnTest::sortAnswers
     * @covers TheoryTest\Car\LearnTest::checkAnswer
     * @covers TheoryTest\Car\LearnTest::updateLearningProgress
     * @covers TheoryTest\Car\TheoryTest::getQuestionData
     * @covers TheoryTest\Car\TheoryTest::getUserID
     */
    public function testChangeAnswers()
    {
//        $this->learnTest->addAnswer('A', $prim);
//        $this->learnTest->removeAnswer('A', $prim);
//        $this->learnTest->replaceAnswer('C', $prim);
        $this->markTestIncomplete();
    }
    
    /**
     * @covers TheoryTest\Car\LearnTest::checkAnswer
     * @covers TheoryTest\Car\LearnTest::updateLearningProgress
     * @covers TheoryTest\Car\TheoryTest::getUserID
     */
    public function testCheckAnswer()
    {
//        $this->learnTest->checkAnswer($prim);
        $this->markTestIncomplete();
    }
    
    /**
     * @covers TheoryTest\Car\LearnTest::updateLearningProgress
     * @covers TheoryTest\Car\TheoryTest::getUserID
     */
    public function testUpdateLearningProgress()
    {
        //$this->learnTest->updateLearningProgress();
        $this->markTestIncomplete();
    }
}
