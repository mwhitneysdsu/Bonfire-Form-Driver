Bonfire-Form-Driver
===================

Driver system for the Bonfire form library

Implements the existing Bootstrap 2.3.2 support in the Bonfire Form library as a driver, so new drivers can be written to support other frameworks.

Currently, the library attempts to find form.driver and form.driver_location config values, and defaults to 'bootstrap_2_3_2' and 'Form', respectively.

The existing library is actually not used by the Bonfire core, so I modified the BF_form_helper to use the library. Because the library's use is so limited, it is likely that it is not completely up to date for even Bootstrap 2.3.2 support.

TODO: Make sure the driver is up to date for Bootstrap 2.3.2; add support for more of the functionality in the CodeIgniter form helper; and test with additional driver implementations.
