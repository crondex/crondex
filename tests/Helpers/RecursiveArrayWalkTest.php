<?php

use Crondex\Helpers\RecursiveArrayWalk;

class RecursiveArrayWalkTest extends PHPUnit_Framework_TestCase
{
    public function testRecursiveArrayWalk()
    {
        //setup
        $a = array(
            'apple', 
            'pear',
            'banana',
            'citrus' => array ( 
                'orange',
                'lemon',
                'grapefruit',
                'tangerine'
            )
        );
        $b = array(
            'APPLE',
            'PEAR',
            'BANANA',
            'CITRUS' => array (
                'ORANGE',
                'LEMON',
                'GRAPEFRUIT',
                'TANGERINE'
            )
        );

        //act
        $c = new RecursiveArrayWalk;
        $c->setValue($b, strtolower);

        //debugging
        echo "\n------------------------------";
        echo "\n Array '\$a':\n";
        echo "------------------------------\n";
        var_dump($a);
        echo "\n------------------------------";
        echo "\n Array '\$b' before function:\n";
        echo "------------------------------\n";
        var_dump($b);
        echo "\n------------------------------";
        echo "\n Array '\$b' after function:\n";
        echo "------------------------------\n";
        var_dump($c->getValue());

        //assert
        $this->assertEquals($a, $c->getValue());
    }
}
