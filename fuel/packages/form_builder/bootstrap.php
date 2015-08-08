<?php
/**
 * FormBuilder: 
 *
 * @package    FormBuilder
 * @subpackage 
 * @version    1.0
 * @author     Jordan Kapelner
 * @license    MIT License
 * @copyright  (c) 2013 Jordan Kapelner
 */

/*
 * Make sure the dependency packages are loaded.
 */

Autoloader::add_core_namespace('FormBuilder');

Autoloader::add_classes(array(
  // Base FormBuilder classes
  'FormBuilder\FormBuilder'              => __DIR__.'/classes/form_builder.php',
));
Config::load('form_builder', true);
