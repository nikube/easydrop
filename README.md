# EasyDrop

Dolibarr module (id 194057) that turns every file upload field into a drag & drop zone, like the EasyModelePdf setup page.

- Drop files on the zone, or click it to open the file picker.
- On the standard "Attach a new file" form (Linked files tabs, ECM...), the upload starts as soon as files are dropped. Tick "overwrite" first if needed.
- Other upload fields (imports, email attachments, logos...) just receive the files; submit as usual.
- On Dolibarr 23+, the attach form hidden behind the "+" button is shown directly.
- Pure client-side: one script injected on every page (`printCommonFooter` hook). No setup, no tables.

Requires Dolibarr 19+, PHP 7.4+. GPL-3.0-or-later, © Anatole Conseil.
