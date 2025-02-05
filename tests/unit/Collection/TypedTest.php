<?php
/**
 * Test class for phpMorphy_Util_Collection_Typed.
 * Generated by PHPUnit on 2010-12-06 at 14:24:41.
 */
class test_Collection_Typed extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $type
     * @return phpMorphy_Util_Collection_Typed
     */
    protected function fixture($type, $allowNull = false) {
        return new phpMorphy_Util_Collection_Typed(
            new phpMorphy_Util_Collection_ArrayBased(),
            $type,
            $allowNull
        );
    }

    function testPodTypeAlias() {
        $obj = $this->fixture('int');
        $obj->append(1);

        $obj = $this->fixture('integer');
        $obj->append(1);

        $obj = $this->fixture('bool');
        $obj->append(true);

        $obj = $this->fixture('boolean');
        $obj->append(true);

        $obj = $this->fixture('float');
        $obj->append(0.1);

        $obj = $this->fixture('double');
        $obj->append(0.1);
    }

    function testPodType() {
        $obj = $this->fixture('int');

        $obj->append(100);
        
        try {
            $obj->append('a');
            $this->assertTrue(false);
        } catch (Exception $e) { }

        try {
            $obj->append(new stdClass);
            $this->assertTrue(false);
        } catch (Exception $e) { }

        try {
            $obj->append(true);
            $this->assertTrue(false);
        } catch (Exception $e) { }

        try {
            $obj->append(array());
            $this->assertTrue(false);
        } catch (Exception $e) { }
    }

    function testClassTypes() {
        $obj = $this->fixture('STDCLASS');
        $obj->append(new stdClass());

        try {
            $obj->append(1);
            $this->assertTrue(false);
        } catch (Exception $e) { }
    }

    function testNullTypes() {
        $obj = $this->fixture('int');
        try {
            $obj->append(null);
            $this->assertTrue(false);
        } catch (Exception $e) { }

        $obj = $this->fixture('int', true);
        $obj->append(null);
        $this->assertEquals(array(null), iterator_to_array($obj));
    }
}
