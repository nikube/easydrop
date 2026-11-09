<?php
/* Copyright (C) 2026 Anatole Conseil
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * \file    admin/setup.php
 * \ingroup easydrop
 * \brief   EasyDrop setup page (feature switches)
 */

$res = 0;
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res && file_exists("../../../../main.inc.php")) {
	$res = @include "../../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/ajax.lib.php';

$langs->loadLangs(array('easydrop@easydrop', 'admin'));

if (!$user->admin) {
	accessforbidden();
}

llxHeader('', $langs->trans('EasyDropSetup'));

$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans('EasyDropSetup'), $linkback, 'fa-file-upload');

print '<table class="noborder centpercent">';
print '<tr class="liste_titre"><td>'.$langs->trans('Parameter').'</td><td class="center">'.$langs->trans('Value').'</td></tr>';
foreach (array('EASYDROP_DROPZONE' => 'EasyDropOptDropzone', 'EASYDROP_LINKED_FILES' => 'EasyDropOptLinkedFiles') as $const => $langkey) {
	print '<tr class="oddeven">';
	print '<td>'.$langs->trans($langkey).'<br><span class="opacitymedium small">'.$const.'</span></td>';
	print '<td class="center">'.ajax_constantonoff($const).'</td>';
	print '</tr>';
}
print '</table>';

llxFooter();
$db->close();
