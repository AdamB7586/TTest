<?php
namespace TheoryTest\Tests;

use TheoryTest\Car\TheoryTest;

class TheoryTestTest extends SetUp
{
    
    protected $theoryTest;
    
    protected function setUp() : void
    {
        parent::setUp();
        $this->user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        $this->theoryTest = new TheoryTest($this->db, $this->config, $this->template, $this->user);
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::setImagePath
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
     * @covers TheoryTest\Car\TheoryTest::startNewTest
     * @covers TheoryTest\Car\User::checkUserAccess
     * @covers TheoryTest\Car\User::setUserSettings
     * @covers TheoryTest\Car\User::getUserSettings
     */
    public function testCreateNewTest()
    {
        $this->theoryTest->startNewTest();
        $newTest = $this->theoryTest->createNewTest();
        $this->assertStringStartsWith('<div class="row">', $newTest);
        $this->assertStringNotContainsString('<span id="qnum">1</span> of <span id="totalq">0</span>', $newTest);
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::flagQuestion
     * @covers TheoryTest\Car\TheoryTest::getQuestions
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::questionNo
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     */
    public function testFlagQuestion()
    {
        $prim = 510;
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->flagQuestion($prim)); // Add Flag
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->flagQuestion($prim)); // Remove flag
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->flagQuestion($prim)); // Add Flag
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->flagQuestion(1574));
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::addAudio
     * @covers TheoryTest\Car\TheoryTest::alert
     * @covers TheoryTest\Car\TheoryTest::answerSelected
     * @covers TheoryTest\Car\TheoryTest::audioButton
     * @covers TheoryTest\Car\TheoryTest::checkSettings
     * @covers TheoryTest\Car\TheoryTest::checkIfLast
     * @covers TheoryTest\Car\TheoryTest::createQuestionHTML
     * @covers TheoryTest\Car\TheoryTest::currentQuestion
     * @covers TheoryTest\Car\TheoryTest::dsaExplanation
     * @covers TheoryTest\Car\TheoryTest::extraContent
     * @covers TheoryTest\Car\TheoryTest::flagHintButton
     * @covers TheoryTest\Car\TheoryTest::getFirstQuestion
     * @covers TheoryTest\Car\TheoryTest::getJavascriptLocation
     * @covers TheoryTest\Car\TheoryTest::getLastQuestion
     * @covers TheoryTest\Car\TheoryTest::getMark
     * @covers TheoryTest\Car\TheoryTest::getNextIncomplete
     * @covers TheoryTest\Car\TheoryTest::getNextPrevButtonArray
     * @covers TheoryTest\Car\TheoryTest::getNextFlagged
     * @covers TheoryTest\Car\TheoryTest::getOptions
     * @covers TheoryTest\Car\TheoryTest::getQuestionData
     * @covers TheoryTest\Car\TheoryTest::getQuestions
     * @covers TheoryTest\Car\TheoryTest::getScript
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::reviewOnly
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::nextQuestion
     * @covers TheoryTest\Car\TheoryTest::numComplete
     * @covers TheoryTest\Car\TheoryTest::numFlagged
     * @covers TheoryTest\Car\TheoryTest::numIncomplete
     * @covers TheoryTest\Car\TheoryTest::numQuestions
     * @covers TheoryTest\Car\TheoryTest::prevQuestion
     * @covers TheoryTest\Car\TheoryTest::questionFlagged
     * @covers TheoryTest\Car\TheoryTest::questionNo
     * @covers TheoryTest\Car\TheoryTest::questionPrim
     * @covers TheoryTest\Car\TheoryTest::reviewAnswers
     * @covers TheoryTest\Car\TheoryTest::reviewButton
     * @covers TheoryTest\Car\TheoryTest::updateTestProgress
     * @covers TheoryTest\Car\User::getUserSettings
     * @covers TheoryTest\Car\User::setUserSettings
     */
    public function testChangeReview()
    {
        $flagged = $this->theoryTest->reviewOnly('flagged');
        $this->assertJsonStringEqualsJsonString("true", $flagged);
        $flaggedJson = json_decode($this->theoryTest->createQuestionHTML(800));
        $this->assertStringContainsString('Reviewing flagged questions only', $flaggedJson->html);
        $incomplete = $this->theoryTest->reviewOnly('incomplete');
        $this->assertJsonStringEqualsJsonString("true", $incomplete);
        $incompleteJson = json_decode($this->theoryTest->createQuestionHTML(800));
        $this->assertStringContainsString('Reviewing incomplete questions only', $incompleteJson->html);
        $all = $this->theoryTest->reviewOnly();
        $this->assertJsonStringEqualsJsonString("true", $all);
    }
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::addAnswer
     * @covers TheoryTest\Car\TheoryTest::answerSelectedCorrect
     * @covers TheoryTest\Car\TheoryTest::currentQuestion
     * @covers TheoryTest\Car\TheoryTest::getQuestionData
     * @covers TheoryTest\Car\TheoryTest::getQuestions
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::questionNo
     * @covers TheoryTest\Car\TheoryTest::removeAnswer
     * @covers TheoryTest\Car\TheoryTest::replaceAnswer
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::sortAnswers
     * @covers TheoryTest\Car\TheoryTest::updateTestProgress
     * @covers TheoryTest\Car\TheoryTest::addAudio
     * @covers TheoryTest\Car\TheoryTest::alert
     * @covers TheoryTest\Car\TheoryTest::answerSelected
     * @covers TheoryTest\Car\TheoryTest::audioButton
     * @covers TheoryTest\Car\TheoryTest::checkIfLast
     * @covers TheoryTest\Car\TheoryTest::checkSettings
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
     * @covers TheoryTest\Car\TheoryTest::getScript
     * @covers TheoryTest\Car\TheoryTest::nextQuestion
     * @covers TheoryTest\Car\TheoryTest::numQuestions
     * @covers TheoryTest\Car\TheoryTest::prevQuestion
     * @covers TheoryTest\Car\TheoryTest::questionFlagged
     * @covers TheoryTest\Car\TheoryTest::questionPrim
     * @covers TheoryTest\Car\TheoryTest::reviewAnswers
     * @covers TheoryTest\Car\TheoryTest::reviewButton
     * @covers TheoryTest\Car\TheoryTest::saveProgress
     * @covers TheoryTest\Car\TheoryTest::updateAnswers
     * @covers TheoryTest\Car\TheoryTest::createImage
     * @covers TheoryTest\Car\TheoryTest::getImagePath
     * @covers TheoryTest\Car\TheoryTest::getImageRootPath
     * @covers TheoryTest\Car\User::getUserSettings
     */
    public function testAddAnswers()
    {
        $prim = 118;
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->addAnswer('B', $prim));
        $this->theoryTest->saveProgress();
        $this->assertJson($this->theoryTest->createQuestionHTML($prim));
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->addAnswer('C', $prim));
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->replaceAnswer('A', $prim));
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->replaceAnswer('', $prim));
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->removeAnswer('A', $prim));
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->removeAnswer('A', $prim));
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->removeAnswer('', $prim));
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::addAudio
     * @covers TheoryTest\Car\TheoryTest::alert
     * @covers TheoryTest\Car\TheoryTest::answerSelected
     * @covers TheoryTest\Car\TheoryTest::audioButton
     * @covers TheoryTest\Car\TheoryTest::audioEnable
     * @covers TheoryTest\Car\TheoryTest::checkIfLast
     * @covers TheoryTest\Car\TheoryTest::checkSettings
     * @covers TheoryTest\Car\TheoryTest::createQuestionHTML
     * @covers TheoryTest\Car\TheoryTest::currentQuestion
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
     * @covers TheoryTest\Car\TheoryTest::getQuestions
     * @covers TheoryTest\Car\TheoryTest::getScript
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
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
     * @covers TheoryTest\Car\TheoryTest::updateTestProgress
     * @covers TheoryTest\Car\User::getUserSettings
     * @covers TheoryTest\Car\User::setUserSettings
     */
    public function testEnableAudio()
    {
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->audioEnable());
        $question = json_decode($this->theoryTest->createQuestionHTML(800));
        $this->assertStringContainsString('data-audio-id', $question->html);
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->audioEnable('off'));
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::checkSettings
     * @covers TheoryTest\Car\TheoryTest::createTestReport
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getTestResults
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::setTest
     * @covers TheoryTest\Car\User::getUserSettings
     * @covers TheoryTest\Car\User::setUserSettings
     */
    public function testCreateReport()
    {
        $newTest = $this->theoryTest->createTestReport();
        $this->assertStringStartsWith('<div class="row">', $newTest);
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setPassmark
     * @covers TheoryTest\Car\TheoryTest::getPassmark
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\User::getUserSettings
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
     * @covers TheoryTest\Car\TheoryTest::getStartSeconds
     * @covers TheoryTest\Car\TheoryTest::setSeconds
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     */
    public function testSetSeconds()
    {
        $originalSecs = $this->theoryTest->getStartSeconds();
        $this->theoryTest->setSeconds(60);
        $this->assertEquals(60, $this->theoryTest->getStartSeconds());
        $this->assertNotEquals($originalSecs, $this->theoryTest->getStartSeconds());
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::getStartSeconds
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::setTime
     */
    public function testSetTime()
    {
        $this->assertObjectHasAttribute('passmark', $this->theoryTest->setTime('46:07', 'remaining'));
        $this->assertObjectHasAttribute('passmark', $this->theoryTest->setTime('07:56'));
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::setJavascriptLocation
     * @covers TheoryTest\Car\TheoryTest::getJavascriptLocation
     * @covers TheoryTest\Car\TheoryTest::setVidLocation
     * @covers TheoryTest\Car\TheoryTest::getVidLocation
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::getImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::getImageRootPath
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::setTables
     */
    public function testSetLocations()
    {
        $origJS = $this->theoryTest->getJavascriptLocation();
        $this->theoryTest->setJavascriptLocation('/js/');
        $this->assertNotEquals($origJS, $this->theoryTest->getJavascriptLocation());
        $this->assertEquals('/js/', $this->theoryTest->getJavascriptLocation());
        
        $origVid = $this->theoryTest->getVidLocation();
        $this->theoryTest->setVidLocation('/vids/');
        $this->assertNotEquals($origVid, $this->theoryTest->getVidLocation());
        $this->assertEquals('/vids/', $this->theoryTest->getVidLocation());
        
        $origImagePath = $this->theoryTest->getImagePath();
        $this->theoryTest->setImagePath('/images/');
        $this->assertNotEquals($origImagePath, $this->theoryTest->getImagePath());
        $this->assertEquals('/images/', $this->theoryTest->getImagePath());
        
        $origRootPath = $this->theoryTest->getImageRootPath();
        $this->theoryTest->setImageRootPath('/root/');
        $this->assertNotEquals($origRootPath, $this->theoryTest->getImageRootPath());
        $this->assertEquals('/root/', $this->theoryTest->getImageRootPath());
        
        $this->assertObjectHasAttribute('passmark', $this->theoryTest->setJavascriptLocation($origJS)->setVidLocation($origVid)->setImagePath($origImagePath)->setImageRootPath($origRootPath));
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::currentQuestion
     * @covers TheoryTest\Car\TheoryTest::getFirstQuestion
     * @covers TheoryTest\Car\TheoryTest::getFlaggedQuestion
     * @covers TheoryTest\Car\TheoryTest::getIncompleteQuestion
     * @covers TheoryTest\Car\TheoryTest::getJavascriptLocation
     * @covers TheoryTest\Car\TheoryTest::getQuestions
     * @covers TheoryTest\Car\TheoryTest::getScript
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::numComplete
     * @covers TheoryTest\Car\TheoryTest::numFlagged
     * @covers TheoryTest\Car\TheoryTest::numIncomplete
     * @covers TheoryTest\Car\TheoryTest::numQuestions
     * @covers TheoryTest\Car\TheoryTest::questionPrim
     * @covers TheoryTest\Car\TheoryTest::reviewSection
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::updateAnswers
     */
    public function testGenerateReview()
    {
        $review = $this->theoryTest->reviewSection();
        $this->assertJson($review);
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::anyExisting
     * @covers TheoryTest\Car\TheoryTest::buildTest
     * @covers TheoryTest\Car\TheoryTest::checkSettings
     * @covers TheoryTest\Car\TheoryTest::clearSettings
     * @covers TheoryTest\Car\TheoryTest::createNewTest
     * @covers TheoryTest\Car\TheoryTest::currentQuestion
     * @covers TheoryTest\Car\TheoryTest::existingLayout
     * @covers TheoryTest\Car\TheoryTest::existingScript
     * @covers TheoryTest\Car\TheoryTest::getJavascriptLocation
     * @covers TheoryTest\Car\TheoryTest::getQuestions
     * @covers TheoryTest\Car\TheoryTest::getSeconds
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getTestName
     * @covers TheoryTest\Car\TheoryTest::getTime
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::numQuestions
     * @covers TheoryTest\Car\TheoryTest::questionPrim
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::setTest
     * @covers TheoryTest\Car\TheoryTest::setTestName
     * @covers TheoryTest\Car\User::checkUserAccess
     * @covers TheoryTest\Car\User::getUserSettings
     * @covers TheoryTest\Car\User::setUserSettings
     */
    public function testStartTestWhenExisting()
    {
        $newTest = $this->theoryTest->createNewTest();
        $this->assertStringStartsWith('<div class="row">', $newTest);
        $this->assertStringContainsString('You have already started this test', $newTest);
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::buildReport
     * @covers TheoryTest\Car\TheoryTest::createOverviewResults
     * @covers TheoryTest\Car\TheoryTest::endTest
     * @covers TheoryTest\Car\TheoryTest::getCategories
     * @covers TheoryTest\Car\TheoryTest::getDSACat
     * @covers TheoryTest\Car\TheoryTest::getFirstQuestion
     * @covers TheoryTest\Car\TheoryTest::getJavascriptLocation
     * @covers TheoryTest\Car\TheoryTest::getPassmark
     * @covers TheoryTest\Car\TheoryTest::getQuestions
     * @covers TheoryTest\Car\TheoryTest::getScript
     * @covers TheoryTest\Car\TheoryTest::getStartSeconds
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getTestName
     * @covers TheoryTest\Car\TheoryTest::getTestResults
     * @covers TheoryTest\Car\TheoryTest::getTime
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::hintEnable
     * @covers TheoryTest\Car\TheoryTest::markTest
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
     * @covers TheoryTest\Car\TheoryTest::setTestName
     * @covers TheoryTest\Car\TheoryTest::testPercentages
     * @covers TheoryTest\Car\TheoryTest::testReport
     * @covers TheoryTest\Car\TheoryTest::testStatus
     * @covers TheoryTest\Car\TheoryTest::checkSettings
     * @covers TheoryTest\Car\TheoryTest::addAudio
     * @covers TheoryTest\Car\TheoryTest::alert
     * @covers TheoryTest\Car\TheoryTest::answerSelected
     * @covers TheoryTest\Car\TheoryTest::answerSelectedCorrect
     * @covers TheoryTest\Car\TheoryTest::audioButton
     * @covers TheoryTest\Car\TheoryTest::checkIfLast
     * @covers TheoryTest\Car\TheoryTest::createQuestionHTML
     * @covers TheoryTest\Car\TheoryTest::currentQuestion
     * @covers TheoryTest\Car\TheoryTest::dsaExplanation
     * @covers TheoryTest\Car\TheoryTest::extraContent
     * @covers TheoryTest\Car\TheoryTest::flagHintButton
     * @covers TheoryTest\Car\TheoryTest::getLastQuestion
     * @covers TheoryTest\Car\TheoryTest::getMark
     * @covers TheoryTest\Car\TheoryTest::getNextPrevButtonArray
     * @covers TheoryTest\Car\TheoryTest::getOptions
     * @covers TheoryTest\Car\TheoryTest::getQuestionData
     * @covers TheoryTest\Car\TheoryTest::nextQuestion
     * @covers TheoryTest\Car\TheoryTest::prevQuestion
     * @covers TheoryTest\Car\TheoryTest::questionPrim
     * @covers TheoryTest\Car\TheoryTest::reviewAnswers
     * @covers TheoryTest\Car\TheoryTest::reviewButton
     * @covers TheoryTest\Car\TheoryTest::reviewOnly
     * @covers TheoryTest\Car\TheoryTest::updateTestProgress
     * @covers TheoryTest\Car\User::getUserSettings
     * @covers TheoryTest\Car\User::setUserSettings
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
        $this->assertJsonStringEqualsJsonString("true", $this->theoryTest->hintEnable());
        $answers = $this->theoryTest->reviewOnly('answers');
        $this->assertJsonStringEqualsJsonString("true", $answers);
        $question = json_decode($this->theoryTest->createQuestionHTML(800));
        $this->assertStringContainsString('numreviewq', $question->html);
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::getQuestions
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::questionNo
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::updateLearningSection
     */
    public function testUpdateLearningSection()
    {
        //$this->assertTrue($this->theoryTest->updateLearningSection());
        $this->markTestIncomplete();
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\User::getUserSettings
     */
    public function testUserClone()
    {
        session_destroy();
        $newTheory = new TheoryTest($this->db, $this->config, $this->template, $this->user, 500);
        $this->assertEquals(500, $newTheory->getUserID());
    }
}
