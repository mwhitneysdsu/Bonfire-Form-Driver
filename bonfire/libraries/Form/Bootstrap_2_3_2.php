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

    /**
     * Get the template
     *
     * @return String    The template
     */
    protected static function getTemplate()
    {
        return self::$template;
    }
}
/* End of file: /bonfire/libraries/Form/Bootstrap_2_3_2.php */