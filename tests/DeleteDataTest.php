<?php

namespace TheoryTest\Tests;

use TheoryTest\Car\DeleteData;

class DeleteDataTest extends SetUp
{
    protected $delete;
    
    public function setUp() : void
    {
        parent::setUp();
        $this->user->login($GLOBALS['LOGIN_EMAIL'], $GLOBALS['LOGIN_PASSWORD']);
        $this->delete = new DeleteData($this->db, $this->config, $this->user);
    }
    
    /**
     * @covers TheoryTest\Car\DeleteData::deleteData
     */
    public function testDeleteData()
    {
        $this->markTestIncomplete();
    }
}
