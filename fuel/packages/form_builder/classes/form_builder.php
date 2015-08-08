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

namespace FormBuilder;

class FormBuilder
{
/*    
    protected $config;
        
    public static function forge($config = array())
    {
        static $instance = null;

        // Load the FormBuilder instance
        if ($instance === null) {
            $config = array_merge(\Config::get('form_builder', array()), $config);
            $instance = new FormBuilder($config);
        }

        return $instance;
    }

    protected function __construct($config = array()) 
    {
        $this->config = $config;
    }
*/
    public static function radio($name, $label, $value, $checked = false, $attributes = null)
    {
        return self::option('radio', $name, $label, $value, $checked, $attributes);
    }
    
    public static function checkbox($name, $label, $value, $checked = false, $attributes = null)
    {
        return self::option('checkbox', $name, $label, $value, $checked, $attributes);
    }

    protected static function option($type, $name, $label, $value, $checked = false, $attributes = null)
    {
        if (empty($attributes))
            $attributes = array();
        
        $attributes['type'] = $type;
        
        if (empty($attributes['id']))
            $attributes['id'] = $name;
        
        if ($checked)
            $attributes['checked'] = 'checked';
        
        $output = \Form::input($name, $value, $attributes);
        $output .= "<label for=\"" . $attributes['id'] . "\">$label</label>\n";
        
        return $output;
    }

    public static function radio_group($name, $options, $default_value = null, $attributes = null, $columns = 0)
    {
        return self::option_group('radio', $name, $options, $default_value, $attributes, $columns);       
    }

    public static function checkbox_group($name, $options, $default_value = null, $attributes = null, $columns = 0)
    {
        return self::option_group('checkbox', $name . '[]', $options, $default_value, $attributes, $columns);       
    }
    
    protected static function option_group($type, $name, $options, $default_value = null, $attributes = null, $columns = 0)
    {
        $output = "<div class=\"option-group\"><table>\n";
        
        if ($columns == 0)
            $columns = 999999999; //0 means unlimited columns, so use a ridiculously high number so the math is easier

        if (empty($attributes))
            $attributes = array();
        
        $attributes['type'] = $type;
        $result = each($options); //get the 1st option
        $id_index = 1;
        
        while ($result)
        {
            $output .= "<tr>\n";
            
            for ($i = 0; $result && ($i < $columns); $i++, $id_index++)
            {
                list($value, $label) = $result;
                $output .= "<td>";               
                $checked = false;

                if ($type == 'checkbox')
                {
                    if (isset($default_value))
                    {
                        if (is_array($default_value))
                        {
                            if (in_array($value, $default_value))
                                $checked = true;
                        }
                        elseif ($value == $default_value)
                            $checked = true;
                    }
                }
                else
                {
                    if (isset($default_value) && ($value == $default_value))
                        $checked = true;                        
                }

                $field_attributes = $attributes;
                
                if (empty($attributes['id']))
                    $field_attributes['id'] = $name . '_' . $id_index;
                else
                    $field_attributes['id'] = $attributes['id'] . '_' . $id_index;
                
                if ($checked)
                    $field_attributes['checked'] = 'checked';

                $output .= \Form::input($name, $value, $field_attributes);
                $output .= "<label for=\"" . $field_attributes['id'] . "\">$label</label>\n";
                $output .= "</td>\n";
                $result = each($options); //get the next option
            }
            
            $output .= "</tr>\n";
        }
        
        $output .= "</table></div>\n";
        
        return $output;        
    }
    
    public static function date_of_birth($name, $default_value = null, $attributes = null, $single_field_flag = false)
    {
        $timezone = new \DateTimeZone("America/New_York");
        $date = null;
        
        if (empty($attributes))
            $attributes = array();
                
        $attributes['type'] = 'text';
        
        if (!empty($default_value))
        {
            if (is_object($default_value)) //if default_value is a datetime object
                $date = $default_value;
            elseif (is_string($default_value)) //if default_value is a date string
                $date = new \DateTime($default_value, $timezone);
            elseif (is_int($default_value)) //if default_value is a unix timestamp
            {
                $date = new \DateTime("now", $timezone);
                $date->setTimestamp($default_value);
            }
        }
                
        if ($single_field_flag)
        {
            $output = \Form::input($name, isset($date) ? $date->format('m/d/Y') : null, $attributes) . "\n";
        }
        else 
        {
            $field_attributes = array(
                'month' => array('size' => '2', 'maxlength' => '2', 'placeholder' => 'MM', 'title' => 'Enter a valid 2-digit month in the format MM'),
                'day' => array('size' => '2', 'maxlength' => '2', 'placeholder' => 'DD', 'title' => 'Enter a valid 2-digit day in the format DD'),
                'year' => array('size' => '4', 'maxlength' => '4', 'placeholder' => 'YYYY', 'title' => 'Enter a valid 4-digit year in the format YYYY'),
            );

            foreach ($field_attributes as $key=>&$field_attribute)
            {
                if (!empty($attributes['id']))
                    $field_attribute['id'] = $attributes['id'] . '_' . $key;
                
                $field_attribute['class'] = 'text';
            }
            unset($field_attribute); //free the reference
            
            $output = \Form::label('Month', $name . '_month') . "\n";
            $output .= \Form::input($name . '[month]', isset($date) ? $date->format('m') : null, $field_attributes['month']) . "\n";
            $output .= \Form::label('Day', $name . '_day') . "\n";
            $output .= \Form::input($name . '[day]', isset($date) ? $date->format('d') : null, $field_attributes['day']) . "\n";
            $output .= \Form::label('Year', $name . '_year') . "\n";
            $output .= \Form::input($name . '[year]', isset($date) ? $date->format('Y') : null, $field_attributes['year']) . "\n";
            $output .= \Form::hidden($name, isset($date) ? $date->format('m/d/Y') : null, $attributes) . "\n";
        }

        return $output;
    }
    
    public static function append_suffix_to_fieldname($name, $suffix)
    {
        $array_suffix = '';

        if (substr($name, -1) == ']')
        {
            $name = substr($name, 0, -1);
            $array_suffix = ']';
        }
        
        return $name . $suffix . $array_suffix;
    }
    
    public static function height($name, $default_value = null, $attributes = null, $units = null)
    {
        if (empty($attributes))
            $attributes = array();
                
        $attributes['type'] = 'number';
        
        if ($units == 'english')
        {
            $field_attributes = $attributes;
            
            if (!empty($attributes['id']))
                $field_attributes['id'] = $attributes['id'] . '_feet';
            
            if (!empty($attributes['min']))
                $field_attributes['min'] = (integer)((integer)$attributes['min'] / 12);

            if (!empty($attributes['max']))
                $field_attributes['max'] = (integer)((integer)$attributes['max'] / 12);

            $output = \Form::input($name . '[feet]', isset($default_value) ? (integer)((integer)$default_value / 12) : null, $field_attributes);
            $output .= " <span>&prime;</span>&nbsp;\n";
            
            if (!empty($attributes['id']))
                $field_attributes['id'] = $attributes['id'] . '_inches';

            $field_attributes['min'] = 0;
            $field_attributes['max'] = 11;
            $field_attributes['size'] = 2;
            $field_attributes['maxlength'] = 2;
            $output .= \Form::input($name . '[inches]', isset($default_value) ? (integer)((integer)$default_value % 12) : null, $field_attributes);
            $output .= "<span> &Prime;</span>\n";        
        }
        else 
        {
            $output = \Form::input($name, $default_value, $attributes);
            
            if (!empty($units))
                $output .= "<span> $units</span>\n";
        }
        
        return $output;
    }
    
    public static function number_range($name, $default_value = null, $attributes = null)
    {
        $attributes['type'] = 'number';
        $max_attributes = $attributes;
        $min_val = null;
        $max_val = null;
        
        if (!empty($default_value) && is_array($default_value))
        {
            if (!empty($default_value['min']))
                $min_val = $default_value['min'];

            if (!empty($default_value['max']))
                $max_val = $default_value['max'];
        }
        
        if (!empty($attributes['id']))
        {
            $max_attributes['id'] = $attributes['id'] . '_max';
            $attributes['id'] = $attributes['id'] . '_min';
        }
            
        $output = \Form::input($name . '[min]', $min_val, $attributes) . ' and ' . \Form::input($name . '[max]', $max_val, $max_attributes) . "\n";
        return $output;
    }
    
    public static function height_range($name, $default_value = null, $attributes = null, $units = null)
    {
        $attributes['type'] = 'number';
        $max_attributes = $attributes;
        $min_val = null;
        $max_val = null;
        
        if (!empty($default_value) && is_array($default_value))
        {
            if (!empty($default_value['min']))
                $min_val = $default_value['min'];

            if (!empty($default_value['max']))
                $max_val = $default_value['max'];
        }

        if (!empty($attributes['id']))
        {
            $max_attributes['id'] = $attributes['id'] . '_max';
            $attributes['id'] = $attributes['id'] . '_min';
        }

        $output = self::height($name . '[min]', $min_val, $attributes, $units) . ' and ' . self::height($name . '[max]', $max_val, $max_attributes, $units) . "\n";
        return $output;
    }
    
    static public function scrollbox($name, $options, $selected_values = array(), $text_select_all = null, $text_unselect_all = null)
    {
        $class = 'odd';
        $output = "<div class=\"scrollbox\">";
        
        foreach ($options as $value=>$label) { 
            $class = ($class == 'even' ? 'odd' : 'even');
            $output .= "<div class=\"$class\">";
            $output .= '<input type="checkbox" name="' . $name . '[]" value="' . $value . '"';
            
            if (!empty($selected_values))
            {
                if (in_array($value, $selected_values))
                    $output .= ' checked="checked"';
            }
            
            $output .= ' />' . $label . "</div>";
        }

        $output .= "</div>";
        
        if (!empty($text_select_all) && !empty($text_unselect_all))
        {
            $output .= "<a onclick=\"$(this).parent().find(':checkbox').attr('checked', true);\">$text_select_all</a> / <a onclick=\"$(this).parent().find(':checkbox').attr('checked', false);\">$text_unselect_all</a>";
        }
        
        return $output;
    }

}
