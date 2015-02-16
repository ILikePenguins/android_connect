<?php

class BaseController
{
	protected $required_fields = array();

	public function validate($method)
	{
		$excluded = array('retrieve', 'retrieveCustomers', 'retrieveBottles', 'retrievePints');
		if (in_array($method, $excluded))
		{
			return NULL;
		}
		foreach ($this->required_fields as $field)
		 {
			if (!isset($_POST[$field])) 
			{
				return generate_response(STATUS_ERROR, 'Parameter ' . $field .
				 ' was not passed in the request.');
			}
		}
		return NULL;
	}
}