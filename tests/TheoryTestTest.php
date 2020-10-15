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
     * @covers TheoryTest\Car\User::checkUserAccess
     * @covers TheoryTest\Car\User::setUserSettings
     * @covers TheoryTest\Car\User::getUserSettings
     */
    public function testCreateNewTest()
    {
        $newTest = $this->theoryTest->createNewTest();
        $this->assertStringStartsWith('<div class="row">', $newTest);
        $this->assertStringNotContainsString('<span id="qnum">1</span> of <span id="totalq">0</span>', $newTest);
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setPassmark
     * @covers TheoryTest\Car\TheoryTest::getPassmark
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getTestType
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
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setTestType
     * @covers TheoryTest\Car\TheoryTest::getTestType
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\User::getUserSettings
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
     * @covers TheoryTest\Car\TheoryTest::getStartSeconds
     * @covers TheoryTest\Car\TheoryTest::setSeconds
     * @covers TheoryTest\Car\TheoryTest::__construct
     * @covers TheoryTest\Car\TheoryTest::getTest
     * @covers TheoryTest\Car\TheoryTest::getTestType
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
     * @covers TheoryTest\Car\TheoryTest::getTestType
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
}
