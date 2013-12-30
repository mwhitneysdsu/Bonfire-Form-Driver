<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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

// ------------------------------------------------------------------------

/**
 * Form Class
 *
 * @package    Bonfire
 * @subpackage Libraries
 * @category   Libraries
 * @author     Bonfire Dev Team
 * @link       http://guides.cibonfire.com/core/unit_test.html
 * @version    3.0
 *
 */
class Form
{
	/**
	 * Stores the CodeIgniter core object.
	 *
	 * @access protected
	 * @static
	 *
	 * @var object
	 */
	protected static $ci;

    /**
     * @var String Stores the name of the driver
	 *
	 * @access protected
	 * @static
     */
    protected static $driver;

    /**
     * @var Object Stores the driver object
	 *
	 * @access protected
	 * @static
     */
    protected static $form;

    /**
     * @var String Stores the path to the driver, relative to the library path
	 *
	 * @access protected
	 * @static
     */
    protected static $path;

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
     * @var String Fallback driver in case the config variable 'form.driver'
     * is not set
     *
     * @access private
     * @static
     */
    private static $fallback_driver = 'bootstrap_2_3_2';

    /**
     * @var String Fallback driver location, in case the config variable
     * 'form.driver_location' is not set
     *
     * @access private
     * @static
     */
    private static $fallback_driver_location = 'Form';

	//--------------------------------------------------------------------

	/**
	 * Constructor calls the init method
	 *
	 * @access public
	 * @uses   init()
	 *
	 * @return void
	 */
	public function __construct($params=array())
	{
		self::$ci =& get_instance();

        $defaults = array();
        $defaults['form_driver'] = self::$ci->config->item('form.driver') ? strtolower(self::$ci->config->item('form.driver')) : self::$fallback_driver;
        $defaults['form_driver_location'] = self::$ci->config->item('form.driver_location') ? ucwords(self::$ci->config->item('form.driver_location')) : self::$fallback_driver_location;

        foreach ($defaults as $key => $val) {
            // Not using ! empty() here because a 0 or false may be desirable
            if (isset($params[$key]) && $params[$key] !== '') {
                $defaults[$key] = $params[$key];
            }
        }

        self::$driver   = $defaults['form_driver'];
        self::$path     = $defaults['form_driver_location'];

        self::$ci->load->library(self::$path . '/' . self::$driver);
        self::$form =& self::$ci->{self::$driver};

        log_message('debug', 'Form Class Initialized and loaded. Driver used: ' . self::$path . '/' . self::$driver);
	}//end __construct()

	//--------------------------------------------------------------------

    public static function form_dropdown($helperArgs)
    {
        return self::$form->form_dropdown($helperArgs);
    }

	//--------------------------------------------------------------------

    public static function form_helper_common($helperArgs=array())
    {
        return self::$form->form_helper_common($helperArgs);
    }

	//--------------------------------------------------------------------

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
		return self::$form->field($name, $properties);
	}//end field()

	//--------------------------------------------------------------------

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
		return self::$form->label($value, $for);
	}//end label()

	//--------------------------------------------------------------------

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
		return self::$form->input($options);
	}//end input()

	//--------------------------------------------------------------------

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
		return self::$form->textarea($options);
	}//end textarea()

	//--------------------------------------------------------------------

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
        return self::$form->state($options);
	}//end state()

	//--------------------------------------------------------------------

	/**
	 * Prepares the value for display in the form.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $value The value to prepare.
	 *
	 * @return string
	 */
	public static function prep_value($value)
	{
		$value = htmlspecialchars($value);
		$value = str_replace(array("'", '"'), array("&#39;", "&quot;"), $value);

		return $value;
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// ! PROTECTED METHODS
	//--------------------------------------------------------------------

	/**
	 * Takes an array of attributes and turns it into a string for an input.
	 *
	 * @access private
	 * @static
	 *
	 * @param array $attr Attributes for a field
	 *
	 * @return string
	 */
	protected static function attr_to_string($attr)
	{
		$attr_str = '';

		if ( ! is_array($attr))
		{
			$attr = (array) $attr;
		}

		foreach ($attr as $property => $value)
		{
			if ($property == 'label')
			{
				continue;
			}
			if ($property == 'value')
			{
				$value = self::prep_value($value);
			}
			$attr_str .= $property . '="' . $value . '" ';
		}

		// We strip off the last space for return
		return substr($attr_str, 0, -1);

	}//end attr_to_string()

	//--------------------------------------------------------------------

}//end class