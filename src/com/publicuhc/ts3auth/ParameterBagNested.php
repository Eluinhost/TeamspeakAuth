<?php
namespace com\publicuhc\ts3auth;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ParameterBagNested extends ParameterBag
{

    /**
     * sets all levels of nested array parameters with dot notation
     * - first_level[second_level: value] will be translated this way:
     *  - first_level: [second_level: value] - standard array parameter will be left as is
     *  - first_level.second_level: value - nested variables are translated with dot notation as bonus so you can access them even directly by first_level.second_level
     *
     * @param string $name
     * @param mixed $value
     */
    public function set( $name, $value )
    {
        parent::set( $name, $value );
        if ( is_array( $value ) ) {
            foreach ( $value as $k => $v ) {
                $this->set( $name . '.' . $k, $v );
            }
        }
    }

}