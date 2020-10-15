<?php

namespace TheoryTest\Tests;

require 'includes/functions.php';

use TheoryTest\Car\TheoryTest;
use TheoryTest\Car\TheoryTestCertificate;

class TheoryTestCertificateTest extends SetUp
{
    protected $theoryTest;
    protected $certificate;

    public function setUp(): void
    {
        parent::setUp();
        $this->theoryTest = new TheoryTest($this->db, $this->config, $this->template, $this->user);
        $this->certificate = new TheoryTestCertificate($this->user, $this->theoryTest);
        $this->user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
    }
    
    /**
     * @covers TheoryTest\Car\TheoryTestCertificate::generateCertificate
     * @covers TheoryTest\Car\FPDFProtection::basicTable
     * @covers TheoryTest\Car\TheoryTest::__construct
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
     * @covers TheoryTest\Car\TheoryTest::getTestType
     * @covers TheoryTest\Car\TheoryTest::getTime
     * @covers TheoryTest\Car\TheoryTest::getUserAnswers
     * @covers TheoryTest\Car\TheoryTest::getUserID
     * @covers TheoryTest\Car\TheoryTest::getUserProgress
     * @covers TheoryTest\Car\TheoryTest::getUserTestInfo
     * @covers TheoryTest\Car\TheoryTest::markTest
     * @covers TheoryTest\Car\TheoryTest::numComplete
     * @covers TheoryTest\Car\TheoryTest::numCorrect
     * @covers TheoryTest\Car\TheoryTest::numFlagged
     * @covers TheoryTest\Car\TheoryTest::numIncomplete
     * @covers TheoryTest\Car\TheoryTest::numQuestions
     * @covers TheoryTest\Car\TheoryTest::printCertif
     * @covers TheoryTest\Car\TheoryTest::questionInfo
     * @covers TheoryTest\Car\TheoryTest::questionNo
     * @covers TheoryTest\Car\TheoryTest::setImagePath
     * @covers TheoryTest\Car\TheoryTest::setImageRootPath
     * @covers TheoryTest\Car\TheoryTest::setTables
     * @covers TheoryTest\Car\TheoryTest::setTestName
     * @covers TheoryTest\Car\TheoryTest::testPercentages
     * @covers TheoryTest\Car\TheoryTest::testReport
     * @covers TheoryTest\Car\TheoryTest::testStatus
     * @covers TheoryTest\Car\User::getFirstname
     * @covers TheoryTest\Car\User::getLastname
     * @covers TheoryTest\Car\User::getUserSettings
     */
    public function testGenerateCertificate()
    {
        $this->theoryTest->endTest('57:00', true);
        $this->assertNull($this->certificate->generateCertificate());
    }
}
