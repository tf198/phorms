<?php
abstract class FieldTest extends PHPUnit_Framework_TestCase {
	
	protected $field = null;
	
	protected $defaults = array();
	
	protected $blank_input = null;
	
	public $require = array('validators' => array('required'));
	
	/**
	 * @return array ($input, $result, [$constructor_args])
	 */
	abstract function validation_data();
	
	function getField($args, $input) {
		// use field defaults
		if( $args === null ) $args = $this->defaults;
		
		// every field has label as its first arg
		array_unshift($args, 'My Field');
		
		$rc = new ReflectionClass($this->field);
		$field = $rc->newInstanceArgs($args); /** @var Phorm_Field */
		$field->set_value($input);
		
		return $field;
	}
	
	/**
	 * @dataProvider validation_data
	 * @param string $input
	 * @param mixed $result
	 * @param array $args
	 */
	function testInputs($input, $result, $args=null) {
		$field = $this->getField($args, $input);
		
		if($field->is_valid()) {
			$this->assertSame($result, $field->get_value());
		} else {
			$this->assertEquals($result, $field->errors(false));
		}
	}
	
	/**
	 * @return array ($input, $html, [$constructor_args])
	 */
	abstract function html_data();
	
	/**
	 * @dataProvider html_data
	 * @param string $input
	 * @param string $html
	 * @param array $args
	 */
	function testHTML($input, $html, $args=null) {
		$field = $this->getField($args, $input);
		
		$this->assertEquals($html, $field->html());
	}
	
	/**
	 * For most fields blank input should result in null.
	 * Set $blank_input in test case if different.
	 */
	function testBlankInput() {
		// check what happens with blank input
		$this->testInputs('', $this->blank_input, $this->defaults);
	}
	
	/**
	 * Check the required validator works on this field
	 */
	function testRequired() {
		$this->testInputs('', 'This field is required.', $this->defaults + array('validators' => array('required')));
	}
}