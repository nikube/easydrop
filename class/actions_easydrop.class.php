<?php
/* Copyright (C) 2026 Anatole Conseil
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * \file    class/actions_easydrop.class.php
 * \ingroup easydrop
 * \brief   Hook handlers for the EasyDrop module
 */

/**
 * Class ActionsEasyDrop
 */
class ActionsEasyDrop
{
	/** @var DoliDB */
	public $db;
	/** @var string */
	public $error = '';
	/** @var string[] */
	public $errors = array();
	/** @var string */
	public $resprints = '';

	/**
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * printCommonFooter hook — injects the drop zone script on every page.
	 *
	 * @param array       $parameters  Hook metadata
	 * @param object      $object      Current object
	 * @param string      $action      Current action
	 * @param HookManager $hookmanager Hook manager
	 * @return int 0
	 */
	public function printCommonFooter($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $langs;

		if (empty($conf->use_javascript_ajax)) {
			return 0;
		}
		$langs->load('easydrop@easydrop');

		$jsfile = dol_buildpath('/easydrop/js/easydrop.js', 0);
		$jsurl = dol_buildpath('/easydrop/js/easydrop.js', 1).'?v='.(file_exists($jsfile) ? filemtime($jsfile) : 0);

		print "\n".'<!-- EasyDrop -->'."\n";
		print '<script nonce="'.getNonce().'">window.easydropConfig = '.json_encode(array(
			'text' => $langs->transnoentities('EasyDropText'),
			'icon' => img_picto('', 'download', 'class="pictofixedwidth"'),
		), JSON_HEX_TAG).';</script>'."\n";
		print '<script nonce="'.getNonce().'" src="'.$jsurl.'"></script>'."\n";

		return 0;
	}
}
