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
	let modalRoot = null;
	let modalTitle = null;
	let modalMessage = null;
	let modalCancelBtn = null;
	let modalConfirmBtn = null;
	let modalResolver = null;
	let previousFocus = null;

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

	const ensureModal = () => {
		if (modalRoot) {
			return;
		}

		modalRoot = document.createElement('div');
		modalRoot.className = 'hidden fixed inset-0 z-[80]';
		modalRoot.innerHTML = `
			<div class="absolute inset-0 bg-slate-900/40" data-confirm-close="1"></div>
			<div class="relative z-10 min-h-full flex items-center justify-center p-4">
				<div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-5 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="app-confirm-title">
					<h3 id="app-confirm-title" class="text-base font-semibold text-slate-900"></h3>
					<p id="app-confirm-message" class="mt-2 text-sm text-slate-600"></p>
					<div class="mt-5 flex items-center justify-end gap-2">
						<button type="button" id="app-confirm-cancel" class="btn-secondary text-xs"></button>
						<button type="button" id="app-confirm-ok" class="btn-primary text-xs"></button>
					</div>
				</div>
			</div>
		`;

		document.body.appendChild(modalRoot);

		modalTitle = modalRoot.querySelector('#app-confirm-title');
		modalMessage = modalRoot.querySelector('#app-confirm-message');
		modalCancelBtn = modalRoot.querySelector('#app-confirm-cancel');
		modalConfirmBtn = modalRoot.querySelector('#app-confirm-ok');

		modalRoot.addEventListener('click', (event) => {
			const closer = event.target.closest('[data-confirm-close="1"]');
			if (closer) {
				resolveModal(false);
			}
		});

		modalCancelBtn?.addEventListener('click', () => resolveModal(false));
		modalConfirmBtn?.addEventListener('click', () => resolveModal(true));

		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && modalRoot && !modalRoot.classList.contains('hidden')) {
				event.preventDefault();
				resolveModal(false);
			}
		});
	};

	const resolveModal = (confirmed) => {
		if (!modalRoot || !modalResolver) {
			return;
		}

		const resolver = modalResolver;
		modalResolver = null;
		modalRoot.classList.add('hidden');
		previousFocus?.focus?.();
		resolver(confirmed);
	};

	const openConfirmModal = (actionType, message) => {
		ensureModal();

		const title = isEnglish
			? (actionType === 'save' ? 'Confirm save' : 'Confirm action')
			: (actionType === 'save' ? 'Xác nhận lưu' : 'Xác nhận thao tác');

		const cancelText = isEnglish ? 'Cancel' : 'Huỷ';
		const confirmText = actionType === 'save'
			? (isEnglish ? 'Save' : 'Lưu')
			: (isEnglish ? 'Continue' : 'Tiếp tục');

		modalTitle.textContent = title;
		modalMessage.textContent = message;
		modalCancelBtn.textContent = cancelText;
		modalConfirmBtn.textContent = confirmText;

		modalConfirmBtn.classList.remove('text-red-600', 'border-red-200', 'hover:border-red-300');
		if (actionType === 'destructive') {
			modalConfirmBtn.classList.add('text-red-600', 'border-red-200', 'hover:border-red-300');
		}

		previousFocus = document.activeElement;
		modalRoot.classList.remove('hidden');

		return new Promise((resolve) => {
			modalResolver = resolve;
			setTimeout(() => modalConfirmBtn?.focus(), 0);
		});
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
		async (event) => {
			const form = event.target;

			if (!(form instanceof HTMLFormElement) || form.dataset.skipConfirm === 'true') {
				return;
			}

			if (form.dataset.confirmBypass === 'true') {
				delete form.dataset.confirmBypass;
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

			event.preventDefault();

			if (form.dataset.confirmPending === 'true') {
				return;
			}

			form.dataset.confirmPending = 'true';
			const message = submitter.dataset.confirmMessage || getMessage(actionType);
			const confirmed = await openConfirmModal(actionType, message);
			delete form.dataset.confirmPending;

			if (!confirmed) {
				return;
			}

			form.dataset.confirmBypass = 'true';
			if (typeof form.requestSubmit === 'function') {
				form.requestSubmit(submitter || undefined);
				return;
			}

			form.submit();
		},
		true
	);
})();
