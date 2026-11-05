/* EasyDrop — Copyright (C) 2026 Anatole Conseil — GPL-3.0-or-later
 *
 * Every visible, enabled <input type="file"> becomes a drop zone:
 * click = native file picker, drop = files put into the input.
 * On the standard "attach a new file" form (submit button name="sendit"),
 * the upload is sent right away, like the EasyModelePdf drop zone.
 */
(function () {
	var cfg = window.easydropConfig || {};

	function stop(e) { e.preventDefault(); e.stopPropagation(); }

	function setup(input) {
		input.dataset.easydrop = '1';
		var form = input.form;
		// only the standard attach form auto-submits (imports also use name=sendit)
		var sendit = form && input.closest('.formattachnewfile') && form.querySelector('[type=submit][name=sendit]:not([disabled])');

		var zone = document.createElement('button'); // button: focusable, Enter/Space open the picker
		zone.type = 'button';
		zone.className = 'easydrop-zone';
		zone.innerHTML = (cfg.icon || '') + '<span class="opacitymedium easydrop-label"></span>';
		var label = zone.querySelector('.easydrop-label');
		label.textContent = cfg.text || 'Drop files here or click to browse';
		input.parentNode.insertBefore(zone, input);
		input.style.display = 'none';
		// v23+ hides the attach form behind a "+" button: a drop zone is only useful if visible
		var wrap = input.closest('.divattachnewfile.hidden');
		if (wrap) {
			wrap.classList.remove('hidden');
			// its "+" button would now toggle the form away: redundant, hide it
			document.querySelectorAll('a[href*="divattachnewfile"]').forEach(function (a) { a.style.display = 'none'; });
		}

		zone.addEventListener('click', function () { input.click(); });
		input.addEventListener('change', function () {
			if (!input.files.length) return;
			label.textContent = Array.prototype.map.call(input.files, function (f) { return f.name; }).join(', ');
			if (sendit) sendit.click(); // click (not form.submit) so "sendit" is posted
		});

		['dragenter', 'dragover'].forEach(function (ev) {
			zone.addEventListener(ev, function (e) { stop(e); zone.classList.add('easydrop-over'); });
		});
		zone.addEventListener('dragleave', function (e) { stop(e); zone.classList.remove('easydrop-over'); });
		// stopPropagation: Dolibarr's own tab bar drop zone must not upload the same files again
		zone.addEventListener('drop', function (e) {
			stop(e);
			zone.classList.remove('easydrop-over');
			var files = e.dataTransfer && e.dataTransfer.files;
			if (!files || !files.length) return;
			if (!input.multiple && files.length > 1) {
				var dt = new DataTransfer();
				dt.items.add(files[0]);
				files = dt.files;
			}
			input.files = files;
			input.dispatchEvent(new Event('change', { bubbles: true }));
		});
	}

	function scan() {
		var inputs = document.querySelectorAll('input[type=file]:not([disabled]):not([data-easydrop])');
		for (var i = 0; i < inputs.length; i++) {
			// skip inputs hidden on purpose (custom uploaders, other drop zones); a hidden
			// ancestor is fine: v23+ hides the attach form behind a "+" button
			if (getComputedStyle(inputs[i]).display !== 'none') setup(inputs[i]);
		}
	}

	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', scan);
	else scan();
})();
