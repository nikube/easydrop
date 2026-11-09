# EasyDrop

Dolibarr module (id 194057): drag & drop on every file upload field, like the EasyModelePdf setup page, and the files of linked objects on Linked files tabs.

- Drop files on the zone, or click it to open the file picker.
- On the standard "Attach a new file" form (Linked files tabs, ECM...), the upload starts as soon as files are dropped. Tick "overwrite" first if needed.
- Other upload fields (imports, email attachments, logos...) just receive the files; submit as usual.
- On Dolibarr 23+, the attach form hidden behind the "+" button is shown directly.
- **Files of linked objects**: on every Linked files tab (`document.php`), a block lists the files of the linked objects with download links: on an invoice, the files of its order, of the proposal behind that order (links of links), of its third party and project. Files the user may not download are not listed (same check as `document.php`). Dolibarr 20+.
- Both features can be switched off on the setup page (`EASYDROP_DROPZONE`, `EASYDROP_LINKED_FILES`).
- No tables: everything goes through the `printCommonFooter` hook.

Requires Dolibarr 19+ (linked files: 20+), PHP 7.4+. GPL-3.0-or-later, © Anatole Conseil.
