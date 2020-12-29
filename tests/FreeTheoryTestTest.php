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
        $newTest = $this->theoryTest->createNewTest();
        $this->assertStringStartsWith('<div class="row">', $newTest);
        $this->assertStringNotContainsString('<span id="qnum">1</span> of <span id="totalq">0</span>', $newTest);
    }
    
    /**
     * @covers TheoryTest\Car\FreeTheoryTest::checkSettings
     * @covers TheoryTest\Car\FreeTheoryTest::getTest
     * @covers TheoryTest\Car\FreeTheoryTest::getUserAnswers
     * @covers TheoryTest\Car\FreeTheoryTest::reviewOnly
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     */
    public function testChangeReview()
    {
        $flagged = $this->theoryTest->reviewOnly('flagged');
        $this->assertJsonStringEqualsJsonString("true", $flagged);
        $incomplete = $this->theoryTest->reviewOnly('incomplete');
        $this->assertJsonStringEqualsJsonString("true", $incomplete);
        $all = $this->theoryTest->reviewOnly();
        $this->assertJsonStringEqualsJsonString("true", $all);
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\FreeTheoryTest::audioEnable
     * @covers TheoryTest\Car\FreeTheoryTest::checkSettings
     * @covers TheoryTest\Car\FreeTheoryTest::currentQuestion
     * @covers TheoryTest\Car\FreeTheoryTest::getQuestions
     * @covers TheoryTest\Car\FreeTheoryTest::getTest
     * @covers TheoryTest\Car\FreeTheoryTest::getUserAnswers
     * @covers TheoryTest\Car\FreeTheoryTest::updateTestProgress
     * @covers TheoryTest\Car\TheoryTest::addAudio
     * @covers TheoryTest\Car\TheoryTest::alert
     * @covers TheoryTest\Car\TheoryTest::answerSelected
     * @covers TheoryTest\Car\TheoryTest::audioButton
     * @covers TheoryTest\Car\TheoryTest::checkIfLast
     * @covers TheoryTest\Car\TheoryTest::createQuestionHTML
     * @covers TheoryTest\Car\TheoryTest::dsaExplanation
     * @covers TheoryTest\Car\TheoryTest::extraContent
     * @covers TheoryTest\Car\TheoryTest::flagHintButton
     * @covers TheoryTest\Car\TheoryTest::getFirstQuestion
     * @covers TheoryTest\Car\TheoryTest::getJavascriptLocation
     * @covers TheoryTest\Car\TheoryTest::getLastQuestion
     * @covers TheoryTest\Car\TheoryTest::getMark
     * @covers TheoryTest\Car\TheoryTest::getNextPrevButtonArray
     * @covers TheoryTest\Car\TheoryTest::getOptions
     * @covers TheoryTest\Car\TheoryTest::getQuestionData
     * @covers TheoryTest\Car\TheoryTest::getScript
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::nextQuestion
     * @covers TheoryTest\Car\TheoryTest::numQuestions
     * @covers TheoryTest\Car\TheoryTest::prevQuestion
     * @covers TheoryTest\Car\TheoryTest::questionFlagged
     * @covers TheoryTest\Car\TheoryTest::questionNo
     * @covers TheoryTest\Car\TheoryTest::questionPrim
     * @covers TheoryTest\Car\TheoryTest::reviewAnswers
     * @covers TheoryTest\Car\TheoryTest::reviewButton
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     */
    public function testEnableAudio()
    {
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->audioEnable());
        $question = json_decode($this->theoryTest->createQuestionHTML(800));
        $this->assertStringContainsString('data-audio-id', $question->html);
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->audioEnable('off'));
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
        $this->assertEquals(43, $this->theoryTest->getPassmark());
        $this->assertObjectHasAttribute('passmark', $this->theoryTest->setPassmark('hello'));
        $this->assertEquals(43, $this->theoryTest->getPassmark());
        $this->assertObjectHasAttribute('passmark', $this->theoryTest->setPassmark(45));
        $this->assertEquals(45, $this->theoryTest->getPassmark());
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\FreeTheoryTest::getTest
     * @covers TheoryTest\Car\FreeTheoryTest::getUserAnswers
     * @covers TheoryTest\Car\FreeTheoryTest::setTime
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     */
    public function testSetTime()
    {
        $this->assertObjectHasAttribute('passmark', $this->theoryTest->setTime('46:07', 'remaining'));
        $this->assertObjectHasAttribute('passmark', $this->theoryTest->setTime('07:56'));
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
        $this->assertEquals('CAR', $this->theoryTest->getTestType());
        $this->assertObjectHasAttribute('passmark', $this->theoryTest->setTestType(45));
        $this->assertEquals('CAR', $this->theoryTest->getTestType());
        $this->assertObjectHasAttribute('passmark', $this->theoryTest->setTestType('bike'));
        $this->assertEquals('BIKE', $this->theoryTest->getTestType());
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::saveProgress
     * @covers TheoryTest\Car\FreeTheoryTest::getTest
     * @covers TheoryTest\Car\FreeTheoryTest::updateAnswers
     * @covers TheoryTest\Car\FreeTheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     */
    public function testUpdateAnswers()
    {
        $this->assertEquals('true', $this->theoryTest->saveProgress());
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\FreeTheoryTest::getQuestions
     * @covers TheoryTest\Car\FreeTheoryTest::getTest
     * @covers TheoryTest\Car\FreeTheoryTest::getTestResults
     * @covers TheoryTest\Car\FreeTheoryTest::getTime
     * @covers TheoryTest\Car\FreeTheoryTest::getUserAnswers
     * @covers TheoryTest\Car\FreeTheoryTest::markTest
     * @covers TheoryTest\Car\FreeTheoryTest::printCertif
     * @covers TheoryTest\Car\FreeTheoryTest::setTestName
     * @covers TheoryTest\Car\FreeTheoryTest::testReport
     * @covers TheoryTest\Car\TheoryTest::buildReport
     * @covers TheoryTest\Car\TheoryTest::createOverviewResults
     * @covers TheoryTest\Car\TheoryTest::endTest
     * @covers TheoryTest\Car\TheoryTest::getCategories
     * @covers TheoryTest\Car\TheoryTest::getDSACat
     * @covers TheoryTest\Car\TheoryTest::getFirstQuestion
     * @covers TheoryTest\Car\TheoryTest::getJavascriptLocation
     * @covers TheoryTest\Car\TheoryTest::getPassmark
     * @covers TheoryTest\Car\TheoryTest::getScript
     * @covers TheoryTest\Car\TheoryTest::getTestName
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::numComplete
     * @covers TheoryTest\Car\TheoryTest::numCorrect
     * @covers TheoryTest\Car\TheoryTest::numFlagged
     * @covers TheoryTest\Car\TheoryTest::numIncomplete
     * @covers TheoryTest\Car\TheoryTest::numQuestions
     * @covers TheoryTest\Car\TheoryTest::printCertif
     * @covers TheoryTest\Car\TheoryTest::questionNo
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::testPercentages
     * @covers TheoryTest\Car\TheoryTest::testReport
     * @covers TheoryTest\Car\TheoryTest::testStatus
     * @covers TheoryTest\Car\User::getFirstname
     * @covers TheoryTest\Car\User::getLastname
     * @covers TheoryTest\Car\User::getUserField
     */
    public function testEndTest()
    {
        $end = $this->theoryTest->endTest('47:06', true);
        $this->assertJson($end);
        
        $report = $this->theoryTest->buildReport(false);
        $this->assertStringStartsWith('<div class="row">', $report);
    }
}
