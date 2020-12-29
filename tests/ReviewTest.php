<?php
namespace TheoryTest\Tests;

use TheoryTest\Car\Review;

class ReviewTest extends SetUp
{
    
    protected $review;
    
    protected function setUp() : void
    {
        parent::setUp();
        $this->review = new Review($this->db, $this->config, $this->template, $this->user);
    }
    
    /**
     * @covers TheoryTest\Car\Review::__construct
     * @covers TheoryTest\Car\Review::__set
     * @covers TheoryTest\Car\Review::setTables
     * @covers TheoryTest\Car\Review::getUserID
     * @covers TheoryTest\Car\Review::getUserAnswers
     */
    public function testGetUserInfo()
    {
        $this->review->useranswers = [];
        $this->assertEquals(0, $this->review->getUserID());
        $this->assertEmpty($this->review->getUserAnswers());
        
        $this->user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        $this->assertEquals(1, $this->review->getUserID());
        $this->assertNotEmpty($this->review->getUserAnswers());
    }
    
    /**
     * @covers TheoryTest\Car\Review::__construct
     * @covers TheoryTest\Car\Review::setTables
     * @covers TheoryTest\Car\Review::numberOfTests
     */
    public function testGetNoOfTests()
    {
        $this->assertEquals(14, $this->review->numberOfTests());
        $this->review->noOfTests = 'hello';
        $this->assertEquals(1, $this->review->numberOfTests());
    }
    
    /**
     * @covers TheoryTest\Car\Review::__construct
     * @covers TheoryTest\Car\Review::getUserID
     * @covers TheoryTest\Car\Review::setTables
     * @covers TheoryTest\Car\Review::testsPassed
     * @covers TheoryTest\Car\Review::testsFailed
     */
    public function testGetUserTests()
    {
        $this->assertEquals(0, $this->review->testsFailed());
        $this->assertEquals(0, $this->review->testsPassed());
    }
    
    /**
     * @covers TheoryTest\Car\Review::__construct
     * @covers TheoryTest\Car\Review::getUserID
     * @covers TheoryTest\Car\Review::numberOfTests
     * @covers TheoryTest\Car\Review::reviewTests
     * @covers TheoryTest\Car\Review::setTables
     */
    public function testReviewTests()
    {
        $review = $this->review->reviewTests();
        $this->assertArrayHasKey(1, $review);
    }
    
    /**
     * @covers TheoryTest\Car\Review::__construct
     * @covers TheoryTest\Car\Review::buildReviewTable
     * @covers TheoryTest\Car\Review::buildTables
     * @covers TheoryTest\Car\Review::getSectionTables
     * @covers TheoryTest\Car\Review::getUserAnswers
     * @covers TheoryTest\Car\Review::getUserID
     * @covers TheoryTest\Car\Review::reviewCaseStudy
     * @covers TheoryTest\Car\Review::setTables
     */
    public function testBuildTables()
    {
        $tables = $this->review->buildTables();
        $this->assertStringContainsString('<table', $tables);
    }
    
    /**
     * @covers TheoryTest\Car\Review::__construct
     * @covers TheoryTest\Car\Review::getUserAnswers
     * @covers TheoryTest\Car\Review::getUserID
     * @covers TheoryTest\Car\Review::setTables
     * @covers TheoryTest\Car\Review::userCaseInformation
     * @covers TheoryTest\Car\Review::userTestInformation
     */
    public function testGetUserCaseInfo()
    {
        $caseInfo = $this->review->userCaseInformation();
        $this->assertArrayHasKey('Correct', $caseInfo);
    }
    
    /**
     * @covers TheoryTest\Car\Review::__construct
     * @covers TheoryTest\Car\Review::getUserID
     * @covers TheoryTest\Car\Review::setTables
     */
    public function testUserClone()
    {
        session_destroy();
        $newReview = new Review($this->db, $this->config, $this->template, $this->user, 505);
        $this->assertEquals(505, $newReview->getUserID());
    }
}
