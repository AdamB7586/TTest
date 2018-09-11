<?php
namespace TheoryTest\Tests;

use TheoryTest\Car\TheoryTest;

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

class TheoryTestTest extends SetUp {
    
    protected $theoryTest;
    
    protected function setUp() {
        self::$user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        $this->theoryTest = new TheoryTest(self::$db, self::$config, self::$template, self::$user);
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
     * @covers TheoryTest\Car\TheoryTest::getMarkText
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
     * @covers TheoryTest\Car\User::checkUserAccess
     * @covers TheoryTest\Car\User::setUserSettings
     * @covers TheoryTest\Car\User::getUserSettings
     */
    public function testCreateNewTest() {
        $newTest = $this->theoryTest->createNewTest();
        $this->assertStringStartsWith('<div class="row">', $newTest);
        $this->assertNotContains('<span id="qnum">1</span> of <span id="totalq">0</span>', $newTest);
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
     * @covers TheoryTest\Car\User::getUserSettings
     */
    public function testSetPassmark(){
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
     * @covers TheoryTest\Car\User::getUserSettings
     */
    public function testSetTestType(){
        $this->assertEquals('CAR', $this->theoryTest->getTestType());
        $this->assertObjectHasAttribute('passmark', $this->theoryTest->setTestType(45));
        $this->assertEquals('CAR', $this->theoryTest->getTestType());
        $this->assertObjectHasAttribute('passmark', $this->theoryTest->setTestType('bike'));
        $this->assertEquals('BIKE', $this->theoryTest->getTestType());
    }
}
