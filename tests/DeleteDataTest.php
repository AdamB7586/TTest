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
     * @covers TheoryTest\Car\DeleteData::__construct
     * @covers TheoryTest\Car\DeleteData::setTables
     * @covers TheoryTest\Car\DeleteData::deleteOnlyLearningProgress
     */
    public function testDeleteLearning()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers TheoryTest\Car\DeleteData::__construct
     * @covers TheoryTest\Car\DeleteData::setTables
     * @covers TheoryTest\Car\DeleteData::deleteOnlyTestData
     */
    public function testDeleteTests()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers TheoryTest\Car\DeleteData::__construct
     * @covers TheoryTest\Car\DeleteData::setTables
     * @covers TheoryTest\Car\DeleteData::deleteData
     * @covers TheoryTest\Car\DeleteData::deleteOnlyLearningProgress
     * @covers TheoryTest\Car\DeleteData::deleteOnlyTestData
     */
    public function testDeleteAllData()
    {
        $this->assertTrue($this->delete->deleteData());
        $this->assertFalse($this->delete->deleteData());
        //$this->markTestIncomplete();
    }
}
