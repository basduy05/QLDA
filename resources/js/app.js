import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

(() => {
	const normalize = (value) =>
		(value || '')
			.toLowerCase()
			.normalize('NFD')
			.replace(/[\u0300-\u036f]/g, '')
			.replace(/\s+/g, ' ')
			.trim();

	const isEnglish = (document.documentElement.lang || '').toLowerCase().startsWith('en');
	const destructiveKeywords = ['xoa', 'delete', 'remove', 'huy', 'cancel', 'discard'];
	const saveKeywords = ['luu', 'save', 'update', 'cap nhat'];

	let lastSubmitter = null;

	const getSubmitterLabel = (submitter) =>
		normalize(
			submitter?.dataset.confirmLabel ||
			submitter?.getAttribute('aria-label') ||
			submitter?.textContent ||
			submitter?.value ||
			''
		);

	const detectAction = (submitter) => {
		const explicitAction = normalize(submitter?.dataset.confirmAction || '');
		if (explicitAction) {
			return explicitAction;
		}

		const label = getSubmitterLabel(submitter);

		if (destructiveKeywords.some((keyword) => label.includes(keyword))) {
			return 'destructive';
		}

		if (saveKeywords.some((keyword) => label.includes(keyword))) {
			return 'save';
		}

		return null;
	};

	const getMessage = (actionType) => {
		if (actionType === 'save') {
			return isEnglish
				? 'Do you want to save these changes?'
				: 'Bạn có chắc muốn lưu thay đổi này không?';
		}

		return isEnglish
			? 'Are you sure you want to continue this action?'
			: 'Bạn có chắc muốn tiếp tục thao tác này không?';
	};

	document.addEventListener(
		'click',
		(event) => {
			const submitter = event.target.closest('button[type="submit"], input[type="submit"]');
			if (submitter) {
				lastSubmitter = submitter;
			}
		},
		true
	);

	document.addEventListener(
		'submit',
		(event) => {
			const form = event.target;

			if (!(form instanceof HTMLFormElement) || form.dataset.skipConfirm === 'true') {
				return;
			}

			const submitter =
				event.submitter || (lastSubmitter && form.contains(lastSubmitter) ? lastSubmitter : null);

			if (!submitter || submitter.dataset.skipConfirm === 'true') {
				return;
			}

			const method = normalize(
				submitter.getAttribute('formmethod') || form.getAttribute('method') || 'get'
			);

			if (method === 'get' && form.dataset.confirmGet !== 'true') {
				return;
			}

			const actionType = detectAction(submitter);
			if (!actionType) {
				return;
			}

			if (!window.confirm(getMessage(actionType))) {
				event.preventDefault();
			}
		},
		true
	);
})();
