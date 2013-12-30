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
	 * Stores the template that inputs are wrapped in.
	 *
	 * @access protected
	 * @static
	 *
	 * @var string
	 */
	protected static $template = '<div class="clearfix">
	{label}
	<div class="input {error_class}">
		{input}
		<span class="inline-help">{help}</span>
		<span class="inline-help error">{error}</span>
	</div>
</div>';

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
	 * Constructor calls the init method
	 *
	 * @access public
	 * @uses   init()
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

		if (isset($properties['help'])) {
			$help = $properties['help'];
			unset($properties['help']);
		}

		switch ($properties['type']) {
			case 'hidden':
				break;

			case 'radio':
			case 'checkbox':
				break;

			case 'select':
				break;

			case 'textarea':
				break;

			case 'state':
				$input = self::state($properties);
				break;

			default:
				$input = self::input($properties);
				break;
		}

		$return = str_replace('{label}', self::label($properties['label']), self::$template);
		$return = str_replace('{input}', $input, $return);
		$return = str_replace('{help}', $help, $return);
		$return = str_replace('{error_class}', $error_class, $return);
		$return = str_replace('{error}', $error, $return);

		return $return;

	}

    public static function form_dropdown($helperArgs)
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
					$options_vals .= "<option value='{$optgroup_key}'{$sel}>{$optgroup_val}</option>\n";
				}

				$options_vals .= '</optgroup>' . PHP_EOL;
			} else {
				$sel = (in_array($key, $selected)) ? ' selected="selected"' : '';
				$options_vals .= "<option value='{$key}'{$sel}>{$val}</option>\n";
			}
		}

		$error = '';

		if (function_exists('form_error')) {
			if (form_error($data['name'])) {
				$error   = ' error';
				$tooltip = '<span class="help-inline">' . form_error($data['name']) . '</span>' . PHP_EOL;
			}
		}

		$output = <<<EOL
<div class="control-group {$error}">
	<label class="control-label" for="{$data['id']}">{$label}</label>
	<div class="controls">
		 <select {$output} {$extra}>
			{$options_vals}
		</select>
		{$tooltip}
	</div>
</div>
EOL;

		return $output;
    }

    public static function form_helper_common($helperArgs=array())
    {
        $type = isset($helperArgs['type']) ? $helperArgs['type'] : 'text';
        $data = isset($helperArgs['data']) ? $helperArgs['data'] : '';
        $value = isset($helperArgs['value']) ? $helperArgs['value'] : '';
        $label = isset($helperArgs['label']) ? $helperArgs['label'] : '';
        $extra = isset($helperArgs['extra']) ? $helperArgs['extra'] : '';
        $tooltip = isset($helperArgs['tooltip']) ? $helperArgs['tooltip'] : '';

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
				$tooltip = '<span class="help-inline">' . form_error($defaults['name']) . '</span>' . PHP_EOL;
			}
		}

		$output = _parse_form_attributes($data, $defaults);

		$output = <<<EOL
<div class="control-group {$error}">
	<label class="control-label" for="{$defaults['name']}">{$label}</label>
	<div class="controls">
		 <input {$output} {$extra} />
		{$tooltip}
	</div>
</div>
EOL;

		return $output;
    }

    /**
	 * Generates a <label> tag.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $value The displayed text of the label.
	 * @param string $for   The tag to be applied to the 'for' part of the tag.
	 *
	 * @return string HTML for the field label
	 */
	public static function label($value, $for = NULL)
	{
		if ($for === NULL) {
			return "<label>{$value}</label>";
		}

		return "<label for='{$for}'>{$value}</label>";
	}

	/**
	 * Generates a generic <input> tag.
	 *
	 * @access public
	 * @static
	 *
	 * @param array $options An array of options to be applied as attributes to the input. $options['type'] is required.
	 *
	 * @return string HTML for the input field
	 */
	public static function input($options)
	{
		if ( ! isset($options['type'])) {
			logit('You must specify a type for the input.');
		} elseif ( ! in_array($options['type'], self::$standard_inputs)) {
			logit(sprintf('"%s" is not a valid input type.', $options['type']));
		}

		$input = '<input ' . self::attr_to_string($options) . ' />';

		return $input;
	}

	/**
	 * Generates a <textarea> tag.
	 *
	 * @access public
	 * @static
	 *
	 * @param array $options An array of options to be applied as attributes.
	 *
	 * @return string HTML for the textarea field
	 */
	public static function textarea($options)
	{
		$value = '';
		if (isset($options['value'])) {
			$value = $options['value'];
			unset($options['value']);
		}

		$input = '<textarea ' . self::attr_to_string($options) . '>';
		$input .= self::prep_value($value);
		$input .= '</textarea>';

		return $input;
	}

	/**
	 * Address State field
	 *
	 * @access public
	 * @static
	 *
	 * @param array $options An array of options to be applied as attributes.
	 *
	 * @return string HTML for the State dropdown field
	 */
	public static function state($options)
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

		/*
		 * @TODO Is this required?  Is this file even used anymore?
		 */
		print_r($options);

		return $input;
	}
}
/* End of file: /bonfire/libraries/Form/Bootstrap_2_3_2.php */