<?php
/**
 * Menu: 
 *
 * @package    Menu
 * @subpackage 
 * @version    1.0
 * @author     Jordan Kapelner
 * @license    MIT License
 * @copyright  (c) 2013 Jordan Kapelner
 */

/*
 * Make sure the dependency packages are loaded.
 */

Autoloader::add_core_namespace('Menu');

Autoloader::add_classes(array(
  // Base Menu classes
  'Menu\\Menu'              => __DIR__.'/classes/menu.php',
  'Menu\\Driver'            => __DIR__.'/classes/menu/driver.php',
));
Config::load('menu', true);
