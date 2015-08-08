<?php
/**
 * Math: 
 *
 * @package    Math
 * @subpackage 
 * @version    1.0
 * @author     Jordan Kapelner
 * @license    MIT License
 * @copyright  (c) 2013 Jordan Kapelner
 */

namespace Math;

define('EPSILON', 0.000001);
define('ARRAY_COUNT_RANDOM', -1);

class Math
{
    public static function compare_doubles($value1, $value2, $epsilon = EPSILON)
    {
        $result = $value1 - $value2;

        if ($result > $epsilon)
            return 1;
        elseif ($result < -$epsilon)
            return -1;

        return 0;
    }

    public static function compare_usd_prices($value1, $value2)
    {
        return compare_doubles($value1, $value2, 0.001);
    }

    public static function mt_rand_normal($mean = 0.0, $stdev = 1.0, $min = null, $max = null)
    {
        $out_of_bounds = true;
        $value = $mean;

        while ($out_of_bounds)
        {
            $x = (double)mt_rand()/(double)mt_getrandmax();
            $y = (double)mt_rand()/(double)mt_getrandmax();
            $u = sqrt(-2*log($x))*cos(2*pi()*$y);
            $value = ($u * (double)$stdev) + (double)$mean;

            $out_of_bounds = ( (isset($min) && ((double)$min < (double)$mean) && ($value < (double)$min)) || (isset($max) && ((double)$max > (double)$mean) && ($value > (double)$max)) );
        }

        return $value;
    }

    public static function mt_rand_weighted($input)
    {
        $values = array();
        $length = 0;

        foreach ($input as $value=>$weight)
        {
            $values += array_fill($length, $weight, $value);
            $length += $weight;
        }

        $index = mt_rand(0, $length - 1);

        return $values[$index];
    }
    
    public static function array_mt_rand($data, $count = 1, $unique = false, $result_is_array = false)
    {
        $keys = array_keys($data);
        $length = count($keys);
        $index = mt_rand(0, $length - 1);
        $result = array();
        
        if ($count == ARRAY_COUNT_RANDOM)
            $count = mt_rand(0, $length);
        
        if ($unique && ($count > $length))
            $count = $length; //if the result is unique, then the number of values in the result cant be greater than the number of values available
        
        if ($count > 0)
        {
            $result = $keys[$index];
        
            if ($count > 1)
            {
                $result = array($result);

                do
                {
                    $index = mt_rand(0, $length - 1);
                    $result[] = $keys[$index];
                    
                    if ($unique)
                    {
                        //for unique results, remove the chosen value from the selection sample
                        unset($keys[$index]);
                        $keys = array_values($keys);
                        $length--;
                    }
                    
                } while (--$count > 1);
            }
            elseif ($result_is_array)
            {
                $result = array($result);
            }
        }
        
        return $result;
    }

    public static function array_value_mt_rand($data, $count = 1, $unique = false, $result_is_array = false)
    {
        $result = array();
        $key = self::array_mt_rand($data, $count, $unique, $result_is_array);
        
        if (is_array($key))
        {
            foreach ($key as $index)
            {
                $result[] = $data[$index];
            }
        }
        else
        {        
            $result = $data[$key];
        }
        
        return $result;
    }
    
}
