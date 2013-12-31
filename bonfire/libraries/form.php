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
	protected static $template;

	/**
	 * Stores the standard HTML5 inputs.
	 *
	 * @access protected
	 * @static
	 *
	 * @var array
	 */
	protected static $standard_inputs;

	/**
	 * Stores the custom inputs that we provide.
	 *
	 * @access protected
	 * @static
	 *
	 * @var array
	 */
	protected static $custom_inputs;

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
	 * Constructor
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function __construct($params=array())
	{
		self::$ci =& get_instance();

        self::init($params);
	}//end __construct()

	//--------------------------------------------------------------------

    /**
     * Load the config items, if available, and load the driver
     *
     * @access public
     * @static
     *
     * @return void
     */
    public static function init($params=array())
    {
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

        log_message('debug', 'Form library loaded. Driver used: ' . self::$path . '/' . self::$driver);
    }

	//--------------------------------------------------------------------

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

	//--------------------------------------------------------------------

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
		if ( ! isset($properties['name'])) {
			$properties['name'] = $name;
		}
        if (isset($properties['help'])) {
            $properties['tooltip'] = $properties['help'];
        }

        return self::buildControl($properties);
	}//end field()

	//--------------------------------------------------------------------

	/**
	 * Generates a <label> tag.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $value The displayed text of the label.
	 * @param mixed  $options The value to be applied to the 'for' attribute of the tag, or an array of properties.
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
	}//end label()

	//--------------------------------------------------------------------

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
	}//end input()

	//--------------------------------------------------------------------

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
	}//end textarea()

	//--------------------------------------------------------------------

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
		if ( ! is_array($attr)) {
			return '';
		}

        $attributes = array();
		foreach ($attr as $property => $value) {
			if ($property == 'label') {
				continue;
			}
			if ($property == 'value') {
				$value = self::prep_value($value);
			}
            $attributes[] = "{$property}='{$value}'";
		}

		return implode(' ', $attributes);

	}//end attr_to_string()

	//--------------------------------------------------------------------

    /**
     * Build a form control, complete with label, help/error text, etc.
     *
     * @todo Formatting of errors should be handled by the driver
     * @todo $label['attributes'] should be handled by the driver
     * @todo Templates should probably be more flexible
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

		return str_replace($search, $replace, self::getTemplate());
    }

	//--------------------------------------------------------------------

    /**
     * @internal Driver methods
     *
     * The methods below (build*, except for buildControl, above, and
     * getTemplate) are required to be implemented in any fully-functional
     * driver
     *
     */

	//--------------------------------------------------------------------

    /**
     * Build an HTML input control
     *
     * @param Array $settings The settings used to build the control
     *
     * @return String    The HTML input
     */
    protected static function buildInput($settings)
    {
        return self::$form->buildInput($settings);
    }

	//--------------------------------------------------------------------

    /**
     * Build an HTML label
     *
     * @param Array $settings The settings used to build the label (String 'value' and Array 'attributes')
     *
     * @return String    The HTML label
     */
    protected static function buildLabel($settings)
    {
        return self::$form->buildLabel($settings);
    }

	//--------------------------------------------------------------------

    /**
     * Build an HTML select (dropdown) control
     *
     * @param Array $settings The settings used to build the control
     *
     * @return String    The HTML select
     */
    protected static function buildSelect($settings)
    {
        return self::$form->buildSelect($settings);
    }

	//--------------------------------------------------------------------

    /**
     * Build a State Select control
     *
     * @param Array $settings The settings used to build the control
     *
     * @return String    The HTML for the state select
     */
    protected static function buildStateSelect($settings)
    {
        return self::$form->buildStateSelect($settings);
    }

	//--------------------------------------------------------------------

    /**
     * Build an HTML textarea control
     *
     * @param Array $settings The settings used to build the control
     *
     * @return String    The HTML for the textarea
     */
    protected static function buildTextarea($settings)
    {
        return self::$form->buildTextarea($settings);
    }

	//--------------------------------------------------------------------

    /**
     * Get the template (because we have static interference)
     *
     * @return String    The template
     */
    protected static function getTemplate()
    {
        return self::$form->getTemplate();
    }

	//--------------------------------------------------------------------

}//end class