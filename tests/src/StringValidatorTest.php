<?php

namespace Phramework\Validate;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015 - 2016-10-05 at 22:11:07.
 */
class StringTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var String
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new StringValidator(
            4,
            10,
            '/^[a-z][a-z0-9]{3,8}[0-9]$/'
        );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function validateSuccessProvider()
    {
        //input, expected
        return [
            ['abx34scd3', 'abx34scd3'],
            ['abcd0', 'abcd0'],
            ['a2cx2', 'a2cx2'],
        ];
    }

    public function validateFailureProvider()
    {
        //input
        return [
            [''],
            [new \stdClass()],
            [['x', 'array']], //because of type
            [1], //because of type
            [2],
            [-10],
            ['az9'], //because of minLength
            ['abc'], //because of pattern
            ['9abc4'], //because of pattern
            ['asssssssssssssssssssbc9'], //because of maxLength
        ];
    }

    /**
     * @covers Phramework\Validate\StringValidator::__construct
     */
    public function testConstruct()
    {
        $validator = new StringValidator();
    }

    /**
     * @covers Phramework\Validate\StringValidator::__construct
     * @expectedException Exception
     */
    public function testConstructFailure()
    {
        $validator = new StringValidator(-1);
    }

    /**
     * @covers Phramework\Validate\StringValidator::__construct
     * @expectedException Exception
     */
    public function testConstructFailure2()
    {
        $validator = new StringValidator(3, 2);
    }

    /**
     * @dataProvider validateSuccessProvider
     * @covers Phramework\Validate\StringValidator::validate
     */
    public function testValidateSuccess($input, $expected)
    {
        $return = $this->object->validate($input);

        $this->assertInternalType('string', $return->value);
        $this->assertEquals($expected, $return->value);
        $this->assertTrue($return->status);
    }

    /**
     * @covers Phramework\Validate\StringValidator::validate
     */
    public function testValidateSuccessRaw()
    {
        $this->object->raw = true;
        $return = $this->object->validate('abx34scd3');

        $this->assertInternalType('string', $return->value);
        $this->assertEquals('abx34scd3', $return->value);
        $this->assertTrue($return->status);
    }

    /**
     * @dataProvider validateFailureProvider
     * @covers Phramework\Validate\StringValidator::validate
     */
    public function testValidateFailure($input)
    {
        $return = $this->object->validate($input);

        $this->assertFalse($return->status);
    }

    /**
     * Validate against common enum keyword
     * @covers Phramework\Validate\StringValidator::validateEnum
     */
    public function testValidateCommon()
    {
        $validator = (new StringValidator(0, 10));

        $validator->enum = ['aa', 'bb'];

        $return = $validator->validate('aa');
        $this->assertTrue(
            $return->status,
            'Expect true since "aa" is in enum array'
        );

        $return = $validator->validate('cc');
        $this->assertFalse(
            $return->status,
            'Expect false since "cc" is not in enum array'
        );
    }

    /**
     * @covers Phramework\Validate\StringValidator::getType
     */
    public function testGetType()
    {
        $this->assertEquals('string', $this->object->getType());
    }
}
