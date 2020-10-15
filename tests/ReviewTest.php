<?php
namespace TheoryTest\Tests;

use TheoryTest\Car\Review;

class ReviewTest extends SetUp
{
    
    protected $review;
    
    protected function setUp() : void
    {
        parent::setUp();
        $this->user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        $this->review = new Review($this->db, $this->config, $this->template, $this->user);
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
