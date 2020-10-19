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
     * @covers TheoryTest\Car\Review::setTables
     * @covers TheoryTest\Car\Review::getUserID
     * @covers TheoryTest\Car\Review::getUserAnswers
     */
    public function testGetUserInfo()
    {
        $this->assertEquals(0, $this->review->getUserID());
        $this->assertEmpty($this->review->getUserAnswers());
        
        $this->user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        $this->assertEquals(1, $this->review->getUserID());
        $this->assertEmpty($this->review->getUserAnswers());
        //$this->markTestIncomplete();
    }
    
    /**
     * @covers TheoryTest\Car\Review::__construct
     * @covers TheoryTest\Car\Review::setTables
     * @covers TheoryTest\Car\Review::numberOfTests
     */
    public function testGetNoOfTests()
    {
        $this->assertEquals(15, $this->review->numberOfTests());
        $this->review->noOfTests = 'hello';
        $this->assertEquals(1, $this->review->numberOfTests());
    }
    
    /**
     * @covers TheoryTest\Car\Review::__construct
     * @covers TheoryTest\Car\Review::setTables
     * @covers TheoryTest\Car\Review::testsPassed
     * @covers TheoryTest\Car\Review::testsFailed
     */
    public function getUserTests()
    {
        $this->assertEquals(0, $this->review->testsFailed());
        $this->assertEquals(0, $this->review->testsPassed());
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
