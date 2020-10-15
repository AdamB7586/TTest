<?php

namespace TheoryTest\Tests;

use TheoryTest\Car\DeleteData;

class DeleteDataTest extends SetUp
{
    protected $delete;
    
    public function setUp() : void
    {
        parent::setUp();
        $this->delete = new DeleteData($this->db, $this->config, $this->user);
    }
    
    /**
     * @covers TheoryTest\Car\DeleteData::__construct
     * @covers TheoryTest\Car\DeleteData::setTables
     * @covers TheoryTest\Car\DeleteData::deleteOnlyLearningProgress
     */
    public function testDeleteLearning()
    {
        if (!$this->db->select($this->delete->learningProgressTable, ['user_id' => 1])) {
            $this->db->insert($this->delete->learningProgressTable, ['user_id' => 1, 'progress' => serialize([])]);
        }
        $this->assertArrayHasKey('user_id', $this->db->select($this->delete->learningProgressTable, ['user_id' => 1]));
        $this->assertTrue($this->delete->deleteOnlyLearningProgress(1));
        $this->assertFalse($this->db->select($this->delete->learningProgressTable, ['user_id' => 1]));
        $this->assertFalse($this->delete->deleteOnlyLearningProgress());
        $this->assertFalse($this->delete->deleteOnlyLearningProgress('mytest'));
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
        if (!$this->db->select($this->delete->learningProgressTable, ['user_id' => 1])) {
            $this->db->insert($this->delete->learningProgressTable, ['user_id' => 1, 'progress' => serialize([])]);
        }
        $this->assertArrayHasKey('user_id', $this->db->select($this->delete->learningProgressTable, ['user_id' => 1]));
        $this->assertTrue($this->delete->deleteData(1));
        $this->assertFalse($this->db->select($this->delete->learningProgressTable, ['user_id' => 1]));
        $this->assertFalse($this->delete->deleteData());
        $this->assertFalse($this->delete->deleteData('this should be an int'));
        //$this->markTestIncomplete();
    }
}
