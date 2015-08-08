<?php
/**
 * Access: role based permissions library for FuelPHP - depends on Warden.
 *
 * @package    Access
 * @subpackage Access
 * @version    2.0
 * @author     Jordan Kapelner
 * @license    MIT License
 * @copyright  (c) 2013 Jordan Kapelner
 */

/*
 * Make sure the dependency packages are loaded.
 */
Package::load(array('warden'));

Autoloader::add_core_namespace('Access');

Autoloader::add_classes(array(
  // Base Access classes
  'Access\\Access'              => __DIR__.'/classes/access.php',
));

Config::load('access', true);
