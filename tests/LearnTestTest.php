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
        $this->learnTest->addAnswer('B', 335);
        $this->learnTest->addAnswer('B', 800);
    }
    
    /**
     * @covers TheoryTest\Car\LearnTest::__construct
     * @covers TheoryTest\Car\LearnTest::addAnswer
     * @covers TheoryTest\Car\LearnTest::checkAnswer
     * @covers TheoryTest\Car\LearnTest::replaceAnswer
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
     * @covers TheoryTest\Car\LearnTest::updateLearningProgress
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
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::sortAnswers
     * @covers TheoryTest\Car\User::checkUserAccess
     * @covers TheoryTest\Car\User::getUserSettings
     * @covers TheoryTest\Car\User::setUserSettings
     */
    public function testCreateNewTest()
    {
        $learnTest = $this->learnTest->createNewTest();
        $this->assertStringStartsWith('<div class="row">', $learnTest);
    }
    
    /**
     * @covers TheoryTest\Car\LearnTest::__construct
     * @covers TheoryTest\Car\LearnTest::addAnswer
     * @covers TheoryTest\Car\LearnTest::alert
     * @covers TheoryTest\Car\LearnTest::answerSelected
     * @covers TheoryTest\Car\LearnTest::checkAnswer
     * @covers TheoryTest\Car\LearnTest::currentQuestion
     * @covers TheoryTest\Car\LearnTest::dsaExplanation
     * @covers TheoryTest\Car\LearnTest::extraContent
     * @covers TheoryTest\Car\LearnTest::flagHintButton
     * @covers TheoryTest\Car\LearnTest::getIncomplete
     * @covers TheoryTest\Car\LearnTest::getOptions
     * @covers TheoryTest\Car\LearnTest::getScript
     * @covers TheoryTest\Car\LearnTest::getTestInfo
     * @covers TheoryTest\Car\LearnTest::getUserAnswers
     * @covers TheoryTest\Car\LearnTest::hcImage
     * @covers TheoryTest\Car\LearnTest::highwayCodePlus
     * @covers TheoryTest\Car\LearnTest::nextQuestion
     * @covers TheoryTest\Car\LearnTest::prevQuestion
     * @covers TheoryTest\Car\LearnTest::questionNo
     * @covers TheoryTest\Car\LearnTest::questionStatus
     * @covers TheoryTest\Car\LearnTest::replaceAnswer
     * @covers TheoryTest\Car\LearnTest::reviewAnswers
     * @covers TheoryTest\Car\LearnTest::reviewButton
     * @covers TheoryTest\Car\LearnTest::setTables
     * @covers TheoryTest\Car\LearnTest::updateLearningProgress
     * @covers TheoryTest\Car\LearnTest::updateTestProgress
     * @covers TheoryTest\Car\LearnTest::getFirstQuestion
     * @covers TheoryTest\Car\LearnTest::getLastQuestion
     * @covers TheoryTest\Car\LearnTest::numQuestions
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::addAudio
     * @covers TheoryTest\Car\TheoryTest::audioButton
     * @covers TheoryTest\Car\TheoryTest::checkSettings
     * @covers TheoryTest\Car\TheoryTest::createQuestionHTML
     * @covers TheoryTest\Car\TheoryTest::getJavascriptLocation
     * @covers TheoryTest\Car\TheoryTest::getMark
     * @covers TheoryTest\Car\TheoryTest::getQuestionData
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::sortAnswers
     * @covers TheoryTest\Car\User::getUserSettings
     */
    public function testCreateQuestionHTML()
    {
        $learnTest = $this->learnTest->createQuestionHTML(615);
        $this->assertJson($learnTest);
    }
    
    /**
     * @covers TheoryTest\Car\LearnTest::addAnswer
     * @covers TheoryTest\Car\LearnTest::removeAnswer
     * @covers TheoryTest\Car\LearnTest::replaceAnswer
     * @covers TheoryTest\Car\LearnTest::sortAnswers
     * @covers TheoryTest\Car\LearnTest::checkAnswer
     * @covers TheoryTest\Car\LearnTest::updateLearningProgress
     * @covers TheoryTest\Car\LearnTest::__construct
     * @covers TheoryTest\Car\LearnTest::getTestInfo
     * @covers TheoryTest\Car\LearnTest::getUserAnswers
     * @covers TheoryTest\Car\LearnTest::setTables
     * @covers TheoryTest\Car\TheoryTest::getQuestionData
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     */
    public function testChangeAnswers()
    {
        $prim = 182;
        $addAnswer = $this->learnTest->addAnswer('A', $prim);
        $this->assertJson($addAnswer);
        $this->assertJsonStringEqualsJsonString('"CORRECT"', $addAnswer);
        $replaceAnswer = $this->learnTest->replaceAnswer('C', $prim);
        $this->assertJson($replaceAnswer);
        $this->assertJsonStringEqualsJsonString('"INCORRECT"', $replaceAnswer);
        $removeAnswer = $this->learnTest->removeAnswer('C', $prim);
        $this->assertJson($removeAnswer);
        $this->assertJsonStringEqualsJsonString('"INCOMPLETE"', $removeAnswer);
    }
    
    /**
     * @covers TheoryTest\Car\LearnTest::__construct
     * @covers TheoryTest\Car\LearnTest::addAnswer
     * @covers TheoryTest\Car\LearnTest::alert
     * @covers TheoryTest\Car\LearnTest::answerSelected
     * @covers TheoryTest\Car\LearnTest::checkAnswer
     * @covers TheoryTest\Car\LearnTest::currentQuestion
     * @covers TheoryTest\Car\LearnTest::dsaExplanation
     * @covers TheoryTest\Car\LearnTest::extraContent
     * @covers TheoryTest\Car\LearnTest::flagHintButton
     * @covers TheoryTest\Car\LearnTest::getFirstQuestion
     * @covers TheoryTest\Car\LearnTest::getIncomplete
     * @covers TheoryTest\Car\LearnTest::getLastQuestion
     * @covers TheoryTest\Car\LearnTest::getOptions
     * @covers TheoryTest\Car\LearnTest::getScript
     * @covers TheoryTest\Car\LearnTest::getTestInfo
     * @covers TheoryTest\Car\LearnTest::getUserAnswers
     * @covers TheoryTest\Car\LearnTest::hcImage
     * @covers TheoryTest\Car\LearnTest::highwayCodePlus
     * @covers TheoryTest\Car\LearnTest::nextQuestion
     * @covers TheoryTest\Car\LearnTest::numQuestions
     * @covers TheoryTest\Car\LearnTest::prevQuestion
     * @covers TheoryTest\Car\LearnTest::questionNo
     * @covers TheoryTest\Car\LearnTest::questionStatus
     * @covers TheoryTest\Car\LearnTest::replaceAnswer
     * @covers TheoryTest\Car\LearnTest::reviewAnswers
     * @covers TheoryTest\Car\LearnTest::reviewButton
     * @covers TheoryTest\Car\LearnTest::setTables
     * @covers TheoryTest\Car\LearnTest::updateLearningProgress
     * @covers TheoryTest\Car\LearnTest::updateTestProgress
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::addAudio
     * @covers TheoryTest\Car\TheoryTest::audioButton
     * @covers TheoryTest\Car\TheoryTest::checkSettings
     * @covers TheoryTest\Car\TheoryTest::createQuestionHTML
     * @covers TheoryTest\Car\TheoryTest::getJavascriptLocation
     * @covers TheoryTest\Car\TheoryTest::getMark
     * @covers TheoryTest\Car\TheoryTest::getQuestionData
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::sortAnswers
     * @covers TheoryTest\Car\User::getUserSettings
     */
    public function testReviewOnlyIncomplete()
    {
        $_COOKIE['skipCorrect'] = 1;
        $learnTest = $this->learnTest->createQuestionHTML(615);
        $this->assertJson($learnTest);
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
