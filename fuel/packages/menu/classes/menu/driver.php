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

namespace Menu;

/**
 * Menu\Driver
 *
 * @package    Menu
 * @subpackage Menu
 */
class Driver
{
    protected $config;
    protected $selection;
        
    public function __construct($config = array()) 
    {
        $this->config = $config;
        $this->selection = array();
    }
    
    public function get($menu_name)
    {
        return isset($this->config[$menu_name]) ? $this->config[$menu_name] : array();
    }
    
    public function clear($menu_name)
    {
        $this->config[$menu_name] = array();
    }
    
    public function add_item($menu_name, $item_name, $label, $route = null)
    {
        if (!isset($this->config[$menu_name]))
            $this->config[$menu_name] = array();
        
        $this->config[$menu_name][] = array('name' => $item_name, 'label' => $label, 'route' => $route);
    }
    
    public function select_item($menu_name, $item_name)
    {
        $this->selection[$menu_name] = $item_name;
    }
    
    public function is_item_selected($menu_name, $item_name)
    {
        return (isset($this->selection[$menu_name]) && ($this->selection[$menu_name] == $item_name));
    }
    
    public function get_item_count($menu_name)
    {
        return isset($this->config[$menu_name]) ? count($this->config[$menu_name]) : 0;
    }
}
