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

/*
 * Make sure the dependency packages are loaded.
 */
   
Autoloader::add_core_namespace('Math');

Autoloader::add_classes(array(
    // Base Math classes
    'Math\\Math'	=> __DIR__.'/classes/math.php',
));
