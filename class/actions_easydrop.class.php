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

		$langs->load('easydrop@easydrop');

		if (getDolGlobalInt('EASYDROP_LINKED_FILES')) {
			print $this->linkedFilesBlock();
		}

		if (empty($conf->use_javascript_ajax) || !getDolGlobalInt('EASYDROP_DROPZONE')) {
			return 0;
		}

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

	/**
	 * On a "Linked files" tab, list the files of the objects linked to the current one
	 * (links and links of links: invoice <- order <- proposal), of its third party and project.
	 *
	 * @return string HTML block, '' if nothing to show
	 */
	private function linkedFilesBlock()
	{
		global $conf, $langs, $object, $user;

		// every core "Linked files" tab is a document.php; getMultidirOutput($obj, '', 1, ...) needs v20+
		if (basename($_SERVER['PHP_SELF']) !== 'document.php' || (int) DOL_VERSION < 20
			|| !($object instanceof CommonObject) || empty($object->id)) {
			return '';
		}
		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

		$seen = array($object->element.'_'.$object->id => 1);
		$found = array();
		$todo = array($object);
		for ($depth = 0; $depth < 2; $depth++) {
			$next = array();
			foreach ($todo as $o) {
				$candidates = array();
				$o->fetchObjectLinked();
				foreach ((array) $o->linkedObjects as $objs) {
					foreach ($objs as $lo) {
						$candidates[] = $lo;
					}
				}
				if (!empty($o->socid) && $o->element != 'societe' && $o->fetch_thirdparty() > 0) {
					$candidates[] = $o->thirdparty;
				}
				if (!empty($o->fk_project) && $o->fetch_project() > 0) {
					$candidates[] = $o->project;
				}
				foreach ($candidates as $lo) {
					if (!is_object($lo) || empty($lo->id) || isset($seen[$lo->element.'_'.$lo->id])) {
						continue;
					}
					$seen[$lo->element.'_'.$lo->id] = 1;
					$found[] = $lo;
					$next[] = $lo;
				}
			}
			$todo = $next;
		}

		$out = '';
		foreach ($found as $lo) {
			$dir = getMultidirOutput($lo, '', 1);
			if (empty($dir) || strpos($dir, 'error') === 0 || !is_dir($dir)) {
				continue;
			}
			$rel = getMultidirOutput($lo, '', 1, 'outputrel');
			$entity = empty($lo->entity) ? $conf->entity : $lo->entity;
			$rows = '';
			foreach (dol_dir_list($dir, 'files', 0, '', '(\.meta|_preview.*\.png)$', 'name', SORT_ASC, 1) as $f) {
				$check = dol_check_secure_access_document($lo->element, $rel.$f['name'], $entity, $user);
				// skip what the user may not read, and paths document.php would resolve elsewhere
				if (empty($check['accessallowed']) || realpath($check['original_file']) !== realpath($f['fullname'])) {
					continue;
				}
				$url = DOL_URL_ROOT.'/document.php?modulepart='.urlencode($lo->element).'&file='.urlencode($rel.$f['name']).'&entity='.((int) $entity);
				$rows .= '<tr class="oddeven"><td>'.img_mime($f['name']).' <a href="'.$url.'" target="_blank" rel="noopener">'.dol_escape_htmltag($f['name']).'</a></td>';
				$rows .= '<td class="right nowraponall">'.dol_print_size($f['size']).'</td>';
				$rows .= '<td class="center nowraponall">'.dol_print_date($f['date'], 'dayhour', 'tzuser').'</td></tr>';
			}
			if ($rows) {
				$out .= '<tr class="liste_titre"><td colspan="3">'.$lo->getNomUrl(1).'</td></tr>'.$rows;
			}
		}
		if (!$out) {
			return '';
		}

		return "\n<!-- EasyDrop linked files -->\n".'<div class="fichecenter easydrop-linked"><br>'
			.load_fiche_titre($langs->trans('EasyDropLinkedFiles'), '', 'file')
			.'<div class="div-table-responsive-no-min"><table class="noborder centpercent">'.$out.'</table></div></div>'."\n"
			// printCommonFooter runs after div.fiche is closed: move the block back at the end of the page content
			.'<script nonce="'.getNonce().'">(function(){var b=document.currentScript.previousElementSibling,f=document.querySelector("#id-right .fiche");if(f)f.appendChild(b);})();</script>'."\n";
	}
}
