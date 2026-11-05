<?php
/* Copyright (C) 2026 Anatole Conseil
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * \file    core/modules/modEasyDrop.class.php
 * \ingroup easydrop
 * \brief   Module descriptor for EasyDrop
 */

require_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 * Class modEasyDrop
 *
 * Turns every file upload field into a drag & drop zone (click to browse,
 * drop to send). Pure client-side: one script injected by printCommonFooter.
 */
class modEasyDrop extends DolibarrModules
{
	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $conf;

		$this->db = $db;

		// Reserved range 194051–194060 (Anatole Conseil)
		$this->numero = 194057;
		$this->rights_class = 'easydrop';
		$this->family = 'interface';
		$this->module_position = '90';
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		$this->description = "EasyDrop - Drag & drop on every file upload field";
		$this->descriptionlong = "Replaces the native file inputs (Linked files tab, ECM, imports, email attachments...) with a drop zone: drop files on it or click to browse. On the standard 'Attach a new file' form, the upload starts as soon as files are dropped.";
		$this->editor_name = 'Anatole Conseil';
		$this->editor_url = '';
		$this->version = '0.1.0';
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->picto = 'fa-file-upload';

		$this->module_parts = array(
			'hooks' => array('all'),
		);

		$this->dirs = array();
		$this->config_page_url = array();
		$this->hidden = false;
		$this->depends = array();
		$this->requiredby = array();
		$this->conflictwith = array();
		$this->langfiles = array('easydrop@easydrop');
		$this->phpmin = array(7, 4);
		$this->need_dolibarr_version = array(19, 0);
		$this->const = array();

		if (!isset($conf->easydrop)) {
			$conf->easydrop = new stdClass();
			$conf->easydrop->enabled = 0;
		}
		$this->tabs = array();
		$this->dictionaries = array();
		$this->boxes = array();
		$this->cronjobs = array();
		$this->rights = array();
		$this->menu = array();
	}

	/**
	 * Function called when module is enabled.
	 *
	 * @param string $options Options when enabling module ('', 'noboxes')
	 * @return int 1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		return $this->_init(array(), $options);
	}

	/**
	 * Function called when module is disabled.
	 *
	 * @param string $options Options when disabling module ('', 'noboxes')
	 * @return int 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		return $this->_remove(array(), $options);
	}
}
