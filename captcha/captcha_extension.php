<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * iClassEngine
 *
 * THIS IS COPYRIGHTED SOFTWARE
 * PLEASE READ THE LICENSE AGREEMENT
 * http://iclassengine.com/user_guide/policies/license
 *
 * @package		iClassEngine
 * @author		ICE Dev Team
 * @copyright	Copyright (c) 2010, 68 Designs, LLC
 * @license		http://iclassengine.com/user_guide/policies/license
 * @link		http://iclassengine.com
 * @since		Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Captcha Module
 *
 *
 * @subpackage	Addons
 * @link		http://iclassengine.com/user_guide/addons/blog
 *
 */
class Captcha_extension
{

	private $_ci;

	/**
	 * Setup events
	 *
	 * @access	public
	 */
	public function __construct($modules)
	{
		$this->_ci = CI_Base::get_instance();

		$modules->register('tpl/captcha', $this, 'captcha');
		$modules->register('users_controller/edit', $this, 'validation');
		$modules->register('users_controller/field_check', $this, 'validate_captcha');

		// Contact Forms
		$modules->register('contact/captcha', $this, 'contact_captcha');
		$modules->register('contact/process', $this, 'validation');
		$modules->register('contact/field_check', $this, 'validate_captcha');
	}

	// ------------------------------------------------------------------------

	/**
	 * Generate the captcha for contact forms.
	 *
	 */
	public function contact_captcha($cap_data)
	{
		$this->_ci->load->helper('captcha');

		$vals = array(
			'img_path' => './images/captcha/',
			'img_url' => base_url().'/images/captcha/',
			'font_path' => SYSDIR .'fonts/texb.ttf',
			'img_width' => '150',
			'img_height' => 30,
			'expiration' => 7200
		);

		$cap = create_captcha($vals);

		$data = array(
			'captcha_time' => $cap['time'],
			'ip_address' => $this->_ci->input->ip_address(),
			'word' => $cap['word']
		);

		$query = $this->_ci->db->insert_string('captcha', $data);
		$this->_ci->db->query($query);

		// Make sure we have the tags. If not manually show them.
		if (strpos($cap_data, '{ice:image}') !== FALSE)
		{
			$cap_data = str_replace('{ice:image}', $cap['image'], $cap_data);
			$cap_data = str_replace('{ice:input_field}', '<input type="text" class="captcha" name="captcha" value="" />', $cap_data);
		}
		else
		{
			$output = '<p>'.$cap['image'].'</p>';
			$output .= '<label for="captcha">Submit the word you see above:</label> <input type="text" name="captcha" value="" /></p>';
			$cap_data .= $output;
		}

		return $cap_data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Generate the captcha
	 *
	 */
	public function captcha()
	{
		$this->_ci->load->helper('captcha');

		$vals = array(
			'img_path' => './images/captcha/',
			'img_url' => base_url().'/images/captcha/',
			'font_path' => SYSDIR .'fonts/texb.ttf',
			'img_width' => '150',
			'img_height' => 30,
			'expiration' => 7200
		);

		$cap = create_captcha($vals);

		$data = array(
			'captcha_time' => $cap['time'],
			'ip_address' => $this->_ci->input->ip_address(),
			'word' => $cap['word']
		);

		$query = $this->_ci->db->insert_string('captcha', $data);
		$this->_ci->db->query($query);

		$output = '<tr><td class="formleft">Security Image:</td><td>'.$cap['image'].'</td></tr>';
		$output .= '<tr><td class="formleft"><label for="captcha">Submit the word you see above:</label></td><td><input type="text" name="captcha" value="" /></td></tr>';

		echo $output;
	}

	// ------------------------------------------------------------------------

	/**
	 * Setup form validation.
	 */
	public function validation()
	{
		return $this->_ci->form_validation->set_rules('captcha', 'Captcha', 'required|callback_field_check');
	}

	// ------------------------------------------------------------------------

	/**
	 * Validate the form and clear old captchas.
	 */
	public function validate_captcha()
	{
		$table = $this->_ci->db->dbprefix('captcha');
		// First, delete old captchas
		$expiration = time()-7200; // Two hour limit
		$this->_ci->db->query("DELETE FROM ".$table." WHERE captcha_time < ".$expiration);

		// Then see if a captcha exists:
		$sql = "SELECT COUNT(*) AS count FROM ".$table." WHERE word = ? AND ip_address = ? AND captcha_time > ?";
		$binds = array($_POST['captcha'], $this->_ci->input->ip_address(), $expiration);
		$query = $this->_ci->db->query($sql, $binds);

		$row = $query->row();

		if ($row->count == 0)
		{
		    $this->_ci->form_validation->set_message('field_check', 'You must submit the word that appears in the image');
			return FALSE;
		}
		return TRUE;
	}
}
/* End of file captcha_extension.php */
/* Location: ./upload/includes/addons/captcha/captcha_extension.php */