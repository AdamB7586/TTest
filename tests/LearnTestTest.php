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
    
    public function testConnection()
    {
        $this->markTestIncomplete();
    }
}
