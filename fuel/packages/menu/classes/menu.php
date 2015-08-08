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

class Menu
{
    /**
     * Menu driver
     *
     * @var \Menu\Driver
     */
    public $driver;
    
    /* Prevent instantiation */ 
    final private function __construct() {}

    /**
     * Return a static instance of Menu.
     *
     * @return Menu
     */
    public static function forge($config = array())
    {
        static $instance = null;

        // Load the Menu instance
        if ($instance === null) {
            $config = array_merge(\Config::get('menu', array()), $config);
            $instance = new static;
            $instance->driver = new Driver($config);
        }

        return $instance;
    }
    
    /**
     * Fetches the Menu driver instance
     *
     * @return \Menu\Driver
     */
    protected static function driver()
    {
        return static::forge()->driver;
    }    

    public static function get($menu_name)
    {
        return static::driver()->get($menu_name);
    }
    
    public static function clear($menu_name)
    {
        static::driver()->clear($menu_name);
    }
    
    public static function add_item($menu_name, $item_name, $label, $route = null)
    {
        static::driver()->add_item($menu_name, $item_name, $label, $route);
    }
    
    public static function select_item($menu_name, $item_name)
    {
        static::driver()->select_item($menu_name, $item_name);
    }
    
    public static function is_item_selected($menu_name, $item_name)
    {
        return static::driver()->is_item_selected($menu_name, $item_name);
    }
    
    public static function get_item_count($menu_name)
    {
        return static::driver()->get_item_count($menu_name);
    }
    
    public static function build($menu_name, $use_ui_tabs_style = false)
    {
        $menu = static::get($menu_name);
        $result = '';
        
        if (!empty($menu))
        {
            if ($use_ui_tabs_style)
                $result .= "<div class=\"ui-tabs ui-widget ui-corner-all\">\n";
            
            $result .= "<ul id=\"${menu_name}-menu\"";
            
            if ($use_ui_tabs_style)
                $result .= " class=\"ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all\"";
            
            $result .= ">\n";

            foreach ($menu as $menu_item)
            {
                if (!empty($menu_item['name']) && !empty($menu_item['label']))
                {
                    $result .= "<li";
                    $class = $use_ui_tabs_style ? array('ui-state-default ui-corner-top') : array();
                    
                    if (static::is_item_selected($menu_name, $menu_item['name']))
                        $class[] = $use_ui_tabs_style ? "ui-tabs-active ui-state-active" : "selected";
                    elseif (empty($menu_item['route']))
                        $class[] = $use_ui_tabs_style ? "ui-state-disabled" : "disabled";
                    
                    if (!empty($class))
                        $result .= ' class="' . implode(' ', $class) . '"';
                    
                    $result .= "><a";

                    if (!empty($menu_item['route']))
                    {
                        $result .= ' href="' . \Uri::create($menu_item['route']);
                        
                        if ($use_ui_tabs_style)
                            $result .= '" class="ui-tabs-anchor';
                            
                        $result .= '"';
                    }

                    $result .= '>';
                    $result .= $menu_item['label'];
                    $result .= "</a></li>\n";
                }
            }

            $result .= "</ul>\n";
            
            if ($use_ui_tabs_style)
                $result .= "</div>\n";
        }
        
        return $result;
    }
}
