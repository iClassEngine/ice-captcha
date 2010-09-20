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
 * Captcha Installer
 *
 *
 * @subpackage	Addons
 * @link		http://iclassengine.com/user_guide/
 *
 */
class Captcha_init
{
	private $_ci;

	public function __construct()
	{
		$this->_ci = CI_Base::get_instance();
		$this->_ci->load->dbforge();
	}

	public function install()
	{
		if ( ! $this->_ci->db->table_exists('captcha'))
		{
			$fields = array(
				'captcha_id' => array('type' => 'bigint','constraint' => 13,'unsigned' => TRUE,'auto_increment' => TRUE),
			);
			$this->_ci->dbforge->add_field($fields);
			$this->_ci->dbforge->add_field("captcha_time int(11) NOT NULL default '0'");
			$this->_ci->dbforge->add_field("ip_address varchar(16) NOT NULL default '0'");
			$this->_ci->dbforge->add_field("word varchar(20) NOT NULL default ''");
			$this->_ci->dbforge->add_key('captcha_id', TRUE);
			$this->_ci->dbforge->add_key('word');
			if($this->_ci->dbforge->create_table('captcha'))
			{
				return 'Captcah table installed...<br />';
			}
		}
	}

	public function upgrade($version = '')
	{
		return;
	}

	public function uninstall()
	{
		$this->_ci->dbforge->drop_table('captcha');
		return TRUE;
	}
}
/* End of file captcha_init.php */
/* Location: ./upload/includes/addons/captcha/captcha_init.php */