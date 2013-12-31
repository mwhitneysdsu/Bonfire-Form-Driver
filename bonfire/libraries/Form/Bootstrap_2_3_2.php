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

        log_message('debug', 'Form Driver (Bootstrap 2.3.2) initialized');
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
        if (isset($properties['help'])) {
            $properties['tooltip'] = $properties['help'];
        }

        return self::buildControl($properties);
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
        return self::buildControl($helperArgs);
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
        $helperArgs['type'] = 'select';

        return self::buildControl($helperArgs);
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
            return self::buildControl($options);
        }

		if ( ! isset($options['type'])) {
			logit('You must specify a type for the input.');
		} elseif ( ! in_array($options['type'], self::$standard_inputs)) {
			logit(sprintf('"%s" is not a valid input type.', $options['type']));
		}

        $settings = array(
            'data'  => $options,
            'extra' => '',
        );

        if (isset($options['extra'])) {
            $settings['extra'] = $options['extra'];
            unset($settings['data']['extra']);
        }

        return self::buildInput($settings);
	}

    /**
	 * Generates a <label> tag.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $value   The displayed text of the label.
	 * @param mixed  $options An array of attributes, or the value to be applied to the 'for' attribute of the label.
	 *
	 * @return string HTML for the field label
	 */
	public static function label($value, $options=null)
	{
        if (is_string($options)) {
            $options = array('for' => $options);
        }

        // This check is after is_string() because some string values are
        // considered empty, but may be valid labels
        if (empty($options)) {
            $options = array();
        }

        $settings = array(
            'value'         => $value,
            'attributes'    => $options,
        );

        return self::buildLabel($settings);
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
        if ($extended) {
            $options['type'] = 'state';

            return self::buildControl($options);
        }

		$options['name']    = isset($options['name']) ? $options['name'] : '';
		$options['value']   = isset($options['value']) ? $options['value'] : '';

        return self::buildStateSelect($options);
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
        if ($extended) {
            $options['type'] = 'textarea';

            return self::buildControl($options);
        }

        $settings = array(
            'data'  => $options,
            'extra' => '',
        );

        if (isset($options['extra'])) {
            $settings['extra'] = $options['extra'];
            unset($settings['data']['extra']);
        }

        return self::buildTextarea($settings);
	}

    /**
     * Build a form control, complete with label, help/error text, etc.
     *
     * @param Array $options The settings used to build the control, generally handles the inputs passed to BF_form_helper's functions
     *
     * @return String    The HTML to display the control
     */
    protected static function buildControl($options)
    {
        $settings = array(
            'data'      => '',
            'extra'     => '',
            'label'     => '',
            'name'      => '',
            'tooltip'   => '',
            'type'      => 'text',
            'value'     => '',
        );

        // Make sure we don't overwrite a default value with empty data
        foreach ($settings as $key => $val) {
            if (isset($options[$key]) && empty($options[$key])) {
                unset($options[$key]);
            }
        }

        $settings = array_merge($settings, $options);

        /*
         * If $settings['data'] is not an array, it should be a string
         * containing the name of the control, so we turn it into an array and
         * assign its value to the 'name' key.
         */
        if ( ! is_array($settings['data'])) {
            $settings['data'] = array('name' => $settings['data']);
        }

        /*
         * If $settings['extra'], $settings['label'], $settings['name'], or
         * $settings['tooltip'] is empty, try to retrieve the value(s) from
         * $settings['data']
         */
        if (empty($settings['extra']) && isset($settings['data']['extra'])) {
            $settings['extra'] = $settings['data']['extra'];
            unset($settings['data']['extra']);
        }

        if (empty($settings['label']) && isset($settings['data']['label'])) {
            $settings['label'] = $settings['data']['label'];
            unset($settings['data']['label']);
        }

        if (empty($settings['name']) && isset($settings['data']['name'])) {
            $settings['name'] = $settings['data']['name'];
            // We don't unset the name, because it is required for form fields
        }

        if (empty($settings['tooltip']) && isset($settings['data']['tooltip'])) {
            $settings['tooltip'] = $settings['data']['tooltip'];
            unset($settings['data']['tooltip']);
        }

        if ($settings['name'] != $settings['data']['name']) {
            $settings['data']['name'] = $settings['name'];
        }

        /*
         * If there is an error message, add it to the tooltip
         */
        $error = '';
        if (function_exists('form_error')) {
            if (form_error($settings['name'])) {
                $error = ' error';
                $settings['tooltip'] = form_error($settings['name']) . '<br />' . $settings['tooltip'];
            }
        }

        /*
         * We may want to change the label attributes (or value) for some
         * controls
         */
        $label = array(
            'value' => $settings['label'],
            'attributes' => array(
                'class' => 'control-label',
            ),
        );

        // Build the control itself based on the value of $settings['type']
        $input = '';
        switch ($settings['type']) {
            case 'dropdown':
            case 'select':
                $input = self::buildSelect($settings);
                break;

            case 'textarea':
                $input = self::buildTextarea($settings);
                break;

            case 'state':
                $input = self::buildStateSelect($settings);
                break;

            default:
                $input = self::buildInput($settings);
                break;
        }

        /*
         * A label's for attribute should always be set to the id value of the
         * control, so if we don't have an id, we don't set the for attribute
         */
        if ( ! empty($settings['id'])) {
            $label['attributes']['for'] = $settings['id'];
        }
        if (empty($label['attributes']['for']) && ! empty($settings['data']['id'])) {
            $label['attributes']['for'] = $settings['data']['id'];
        }

        /*
         * Finally, build the label and replace the placeholders in the template
         */
        $search = array('{label}', '{input}', '{help}', '{error_class}');
        $replace = array(
            self::buildLabel($label),
            $input,
            $settings['tooltip'],
            $error,
        );

		return str_replace($search, $replace, self::$template);
    }

    /**
     * Build an HTML input control
     *
     * @param Array $settings The settings used to build the control
     *
     * @return String    The HTML input
     */
    protected static function buildInput($settings)
    {
        $input = '<input ' . self::attr_to_string($settings['data']) . " {$settings['extra']} />";

        return $input;
    }

    /**
     * Build an HTML label
     *
     * @param Array $settings The settings used to build the label (String 'value' and Array 'attributes')
     *
     * @return String    The HTML label
     */
    protected static function buildLabel($settings)
    {
        $value = isset($settings['value']) ? $settings['value'] : '';

        if (empty($settings['attributes'])) {
            return "<label>{$value}</label>";
        }

        return '<label ' . self::attr_to_string($settings['attributes']) . ">{$value}</label>";
    }

    /**
     * Build an HTML select (dropdown) control
     *
     * @param Array $settings The settings used to build the control
     *
     * @return String    The HTML select
     */
    protected static function buildSelect($settings)
    {
        $selected = isset($settings['selected']) ? $settings['selected'] : array();
        $options = isset($settings['options']) ? $settings['options'] : array();

        if ( ! is_array($selected)) {
            $selected = array($selected);
        }
        // If no selection was submitted, attempt to find it
        if (count($selected) === 0) {
            // If the field name appears in the $_POST array we have it
            if (isset($_POST[$settings['name']])) {
                $selected = array($_POST[$settings['name']]);
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

        $input = '<select ' . self::attr_to_string($settings['data']) . " {$settings['extra']}>{$options_vals}</select>";

        return $input;
    }

    /**
     * Build a State Select control
     *
     * @param Array $settings The settings used to build the control
     *
     * @return String    The HTML for the state select
     */
    protected static function buildStateSelect($settings)
    {
        if ( ! function_exists('state_select')) {
            self::$ci->load->helper('address');
        }

        $defaultCountry = 'US';
        $defaultState   = isset($settings['default']) ? $settings['default'] : '';
        $stateClass     = isset($settings['class']) ? $settings['class'] : '';

        return state_select($settings['value'], $defaultState, $defaultCountry, $settings['name'], $stateClass);
    }

    /**
     * Build an HTML textarea control
     *
     * @param Array $settings The settings used to build the control
     *
     * @return String    The HTML for the textarea
     */
    protected static function buildTextarea($settings)
    {
        $value = $settings['value'] || $settings['data']['value'];
        unset($settings['value'], $settings['data']['value']);

        $input = '<textarea ' . self::attr_to_string($settings['data']) . " {$settings['extra']}>";
        $input .= self::prep_value($value);
        $input .= '</textarea>';

        return $input;
    }
}
/* End of file: /bonfire/libraries/Form/Bootstrap_2_3_2.php */