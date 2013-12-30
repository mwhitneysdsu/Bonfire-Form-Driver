<?php if ( ! defined('BASEPATH')) { exit('No direct script access allowed'); }
/**
 * Bonfire
 *
 * An open source project to allow developers get a jumpstart their development of CodeIgniter applications
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2013, Bonfire Dev Team
 * @license   http://guides.cibonfire.com/license.html
 * @link      http://cibonfire.com
 * @since     Version 1.0
 * @filesource
 */

/**
 * Bootstrap 2.3.2 driver for Form Class
 *
 * @package    Bonfire
 * @subpackage Libraries
 * @category   Libraries
 * @author     Bonfire Dev Team
 * @link       http://guides.cibonfire.com/core/unit_test.html
 * @version    3.0
 *
 */
class Bootstrap_2_3_2 extends Form
{
	/**
	 * Stores the custom inputs that we provide.
	 *
	 * @access protected
	 * @static
	 *
	 * @var array
	 */
	protected static $custom_inputs = array(
		'state'		=> 'state_select',
		'country'	=> 'country_select'
	);

	/**
	 * Stores the standard HTML5 inputs.
	 *
	 * @access protected
	 * @static
	 *
	 * @var string
	 */
	protected static $standard_inputs = array(
		'button', 'checkbox', 'color', 'date', 'datetime', 'datetime-local',
		'email', 'file', 'hidden', 'image', 'month', 'number', 'password',
		'radio', 'range', 'reset', 'search', 'submit', 'tel', 'text', 'time',
		'url', 'week'
	);

	/**
	 * Stores the template that inputs are wrapped in.
	 *
	 * @access protected
	 * @static
	 *
	 * @var string
	 */
	protected static $template = '
<div class="control-group{error_class}">
	{label}
	<div class="controls">
		{input}
		<span class="help-inline">{help}</span>
	</div>
</div>';

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function __construct()
	{
		self::$ci =& get_instance();

        log_message('debug', 'Bootstrap 2.3.2 class initialized');
	}

	/**
	 * Returns the HTML from the template based on the field passed in
	 *
	 * @access public
	 * @static
	 *
	 * @param string $name       Name of the field
	 * @param array  $properties Field settings
	 *
	 * @return string HTML for the required field
	 */
	public static function field($name, $properties=array())
	{
		if ( ! isset($properties['name'])) {
			$properties['name'] = $name;
		}

		$error_class = '';
		$error  = '';
		$help   = '';
		$input	= '';

        if (function_exists('form_error')) {
            if (form_error($properties['name'])) {
                $error_class = ' error';
                $help .= form_error($properties['name']) . '<br />';
            }
        }

		if (isset($properties['help'])) {
			$help .= $properties['help'];
			unset($properties['help']);
		}

		switch ($properties['type']) {
/*
			case 'hidden':
				break;

			case 'radio':
			case 'checkbox':
				break;

			case 'select':
				break;

			case 'textarea':
				break;
 */
			case 'state':
				$input = self::state($properties);
				break;

			default:
				$input = self::input($properties);
				break;
		}

        $search = array(
            '{label}',
            '{input}',
            '{help}',
            '{error_class}',
        );
        $replace = array(
            self::label($properties['label']),
            $input,
            $help,
            $error_class,
        );

		return str_replace($search, $replace, self::$template);
	}

    /**
     * Utilized by BF_form_helper's _form_common function
     *
     * @param Array $helperArgs An array containing the _form_common function's arguments, currently supports 'type', 'data', 'value', 'label', 'extra', and 'tooltip'
     *
     * @return String    The HTML for the input
     */
    public static function form_helper_common($helperArgs=array())
    {
        $type       = isset($helperArgs['type']) ? $helperArgs['type'] : 'text';
        $data       = isset($helperArgs['data']) ? $helperArgs['data'] : '';
        $value      = isset($helperArgs['value']) ? $helperArgs['value'] : '';
        $label      = isset($helperArgs['label']) ? $helperArgs['label'] : '';
        $extra      = isset($helperArgs['extra']) ? $helperArgs['extra'] : '';
        $tooltip    = isset($helperArgs['tooltip']) ? $helperArgs['tooltip'] : '';

        $defaults = array('type' => $type, 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

		// If name is empty at this point, try to grab it from the $data array
		if (empty($defaults['name']) && is_array($data) && isset($data['name'])) {
			$defaults['name'] = $data['name'];
			unset($data['name']);
		}

		// If label is empty at this point, try to grab it from the $data array
		if (empty($label) && is_array($data) && isset($data['label'])) {
			$label = $data['label'];
			unset($data['label']);
		}

		// If tooltip is empty at this point, try to grab it from the $data array
		if (empty($tooltip) && is_array($data) && isset($data['tooltip'])) {
			$tooltip = $data['tooltip'];
			unset($data['tooltip']);
		}

		$error = '';

		if (function_exists('form_error')) {
			if (form_error($defaults['name'])) {
				$error   = ' error';
				$tooltip = form_error($data['name']) . '<br />' . $tooltip;
			}
		}

		$output = _parse_form_attributes($data, $defaults);

        $search = array(
            '{label}',
            '{input}',
            '{help}',
            '{error_class}',
        );
        $replace = array(
            self::label($label, array('for' => $defaults['name'], 'class' => 'control-label')),
            "<input {$output} {$extra} />",
            $tooltip,
            $error,
        );

		return str_replace($search, $replace, self::$template);
    }

    /**
     * Utilized by BF_form_helper to create a dropdown
     *
     * @param Array $helperArgs Array of arguments from the helper's form_dropdown function, currently supports 'data', 'options', 'selected', 'label', 'extra', and 'tooltip'
     *
     * @return String    The HTML for the dropdown
     */
    public static function form_helper_dropdown($helperArgs)
    {
        $data       = isset($helperArgs['data']) ? $helperArgs['data'] : $helperArgs;
        $options    = isset($helperArgs['options']) ? $helperArgs['options'] : array();
        $selected   = isset($helperArgs['selected']) ? $helperArgs['selected'] : array();
        $label      = isset($helperArgs['label']) ? $helperArgs['label'] : '';
        $extra      = isset($helperArgs['extra']) ? $helperArgs['extra'] : '';
        $tooltip    = isset($helperArgs['tooptip']) ? $helperArgs['tooptip'] : '';

		if ( ! is_array($data)) {
			$data = array('name' => $data);
		}

		if ( ! isset($data['id'])) {
			$data['id'] = $data['name'];
		}

		$output = _parse_form_attributes($data, array());

		if ( ! is_array($selected)) {
			$selected = array($selected);
		}

		// If no selected state was submitted we will attempt to set it automatically
		if (count($selected) === 0) {
			// If the form name appears in the $_POST array we have a winner!
			if (isset($_POST[$data['name']])) {
				$selected = array($_POST[$data['name']]);
			}
		}

		$options_vals = '';
		foreach ($options as $key => $val) {
			$key = (string) $key;

			if (is_array($val) && ! empty($val)) {
				$options_vals .= "<optgroup label='{$key}'>" . PHP_EOL;

				foreach ($val as $optgroup_key => $optgroup_val) {
					$sel = (in_array($optgroup_key, $selected)) ? ' selected="selected"' : '';
					$options_vals .= "<option value='{$optgroup_key}'{$sel}>{$optgroup_val}</option>" . PHP_EOL;
				}

				$options_vals .= '</optgroup>' . PHP_EOL;
			} else {
				$sel = (in_array($key, $selected)) ? ' selected="selected"' : '';
				$options_vals .= "<option value='{$key}'{$sel}>{$val}</option>" . PHP_EOL;
			}
		}

		$error = '';

		if (function_exists('form_error')) {
			if (form_error($data['name'])) {
				$error   = ' error';
				$tooltip = form_error($data['name']) . '<br />' . $tooltip;
			}
		}

        $search = array(
            '{label}',
            '{input}',
            '{help}',
            '{error_class}',
        );
        $replace = array(
            self::label($label, array('for' => $data['id'], 'class' => 'control-label')),
            "<select {$output} {$extra}>{$options_vals}</select>",
            $tooltip,
            $error,
        );

		return str_replace($search, $replace, self::$template);
    }

	/**
	 * Generates a generic <input> tag.
	 *
	 * @access public
	 * @static
	 *
	 * @param array $options An array of options to be applied as attributes to the input. $options['type'] is required.
	 * @param bool  $extended If true, return a templated input with label, help text, etc., else return just an input.
	 *
	 * @return string HTML for the input field
	 */
	public static function input($options, $extended=false)
	{
        if ($extended) {
            return self::form_helper_common($options);
        }

		if ( ! isset($options['type'])) {
			logit('You must specify a type for the input.');
		} elseif ( ! in_array($options['type'], self::$standard_inputs)) {
			logit(sprintf('"%s" is not a valid input type.', $options['type']));
		}

		$input = '<input ' . self::attr_to_string($options) . ' />';

		return $input;
	}

    /**
	 * Generates a <label> tag.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $value   The displayed text of the label.
	 * @param mixed  $options The value to be applied to the 'for' attribute of the tag, or an array of properties.
	 *
	 * @return string HTML for the field label
	 */
	public static function label($value, $options=null)
	{
		if ($options === null) {
			return "<label>{$value}</label>";
		}

        if (is_string($options)) {
            return "<label for='{$options}'>{$value}</label>";
        }

        return '<label ' . self::attr_to_string($options) . ">{$value}</label>";
	}

	/**
	 * Address State field
	 *
	 * @access public
	 * @static
	 *
	 * @param array $options An array of options to be applied as attributes.
	 * @param bool  $extended If true, return a templated state dropdown with label, help text, etc., else return just a state dropdown.
	 *
	 * @return string HTML for the State dropdown field
	 */
	public static function state($options, $extended=false)
	{
		if ( ! function_exists('state_select')) {
			self::$ci->load->helper('address');
		}

		$selected	= isset($options['value']) ? $options['value'] : '';
		$default	= isset($options['default']) ? $options['default'] : '';
		$country	= 'US';
		$name		= isset($options['name']) ? $options['name'] : '';
		$class		= isset($options['class']) ? $options['class'] : '';

		$input = state_select($selected, $default, $country, $name, $class);

        if ($extended !== true) {
            return $input;
        }

        $tooltip = isset($options['tooltip']) ? $options['tooltip'] : '';
        $label = isset($options['label']) ? $options['label'] : '';
        $error = '';

        if ( ! empty($name) && function_exists('form_error')) {
            if (form_error($name)) {
                $error = ' error';
                $tooltip = form_error($name) . '<br />' . $tooltip;
            }
        }

        $search = array(
            '{label}',
            '{input}',
            '{help}',
            '{error_class}',
        );
        $replace = array(
            self::label($label, array('for' => $name, 'class' => 'control-label')),
            $input,
            $tooltip,
            $error,
        );

		return str_replace($search, $replace, self::$template);
	}

	/**
	 * Generates a <textarea> tag.
	 *
	 * @access public
	 * @static
	 *
	 * @param array $options An array of options to be applied as attributes.
	 * @param bool  $extended If true, return a templated textarea with label, help text, etc., else return just a textarea.
	 *
	 * @return string HTML for the textarea field
	 */
	public static function textarea($options, $extended=false)
	{
        $label      = '';
        $tooltip    = '';
		$value      = '';

		if (isset($options['value'])) {
			$value = $options['value'];
			unset($options['value']);
		}

        // We want to unset label and tooltip before creating the textarea,
        // though they should only be set if $extended is true
        if (isset($options['label'])) {
            $label = $options['label'];
            unset($options['label']);
        }

        if (isset($options['tooltip'])) {
            $tooltip = $options['tooltip'];
            unset($options['tooltip']);
        }

		$input = '<textarea ' . self::attr_to_string($options) . '>';
		$input .= self::prep_value($value);
		$input .= '</textarea>';

        if ($extended !== true) {
            return $input;
        }

        $error      = '';
        $name       = '';

        if (isset($options['name'])) {
            $name = $options['name'];
        } elseif (isset($options['id'])) {
            $name = $options['id'];
        }

        if ( ! empty($name) && function_exists('form_error')) {
            if (form_error($name)) {
                $error = ' error';
                $tooltip = form_error($name) . '<br />' . $tooltip;
            }
        }

        $search = array(
            '{label}',
            '{input}',
            '{help}',
            '{error_class}',
        );
        $replace = array(
            self::label($label, array('for' => $name, 'class' => 'control-label')),
            $input,
            $tooltip,
            $error,
        );

		return str_replace($search, $replace, self::$template);
	}
}
/* End of file: /bonfire/libraries/Form/Bootstrap_2_3_2.php */