import './bootstrap';

import Alpine from 'alpinejs';
import RealtimeClient from './realtime-client';

window.Alpine = Alpine;
window.realtime = new RealtimeClient();

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

	window.notify = (message, type = 'success') => {
		window.dispatchEvent(new CustomEvent('notify', {
			detail: { message, type }
		}));
	};

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
		modalRoot.className = 'hidden fixed inset-0 z-[80] overflow-y-auto';
		modalRoot.setAttribute('aria-labelledby', 'modal-title');
		modalRoot.setAttribute('role', 'dialog');
		modalRoot.setAttribute('aria-modal', 'true');
		
		modalRoot.innerHTML = `
			<div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
				<!-- Backdrop -->
				<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" data-confirm-close="1"></div>

				<!-- Modal Panel -->
				<div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
					<div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
						<div class="sm:flex sm:items-start">
							<div id="app-confirm-icon-wrapper" class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
								<svg id="app-confirm-icon" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />
								</svg>
							</div>
							<div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
								<h3 class="text-base font-semibold leading-6 text-gray-900" id="app-confirm-title"></h3>
								<div class="mt-2">
									<p class="text-sm text-gray-500" id="app-confirm-message"></p>
								</div>
							</div>
						</div>
					</div>
					<div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
						<button type="button" id="app-confirm-ok" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"></button>
						<button type="button" id="app-confirm-cancel" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"></button>
					</div>
				</div>
			</div>
		`;

		document.body.appendChild(modalRoot);

		modalTitle = modalRoot.querySelector('#app-confirm-title');
		modalMessage = modalRoot.querySelector('#app-confirm-message');
		modalCancelBtn = modalRoot.querySelector('#app-confirm-cancel');
		modalConfirmBtn = modalRoot.querySelector('#app-confirm-ok');
		// We'll grab the icon wrapper to change colors if needed
		const iconWrapper = modalRoot.querySelector('#app-confirm-icon-wrapper');
		const icon = modalRoot.querySelector('#app-confirm-icon');

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

		// Default styles (Save/Action)
		const iconWrapper = modalRoot.querySelector('#app-confirm-icon-wrapper');
		const icon = modalRoot.querySelector('#app-confirm-icon');

		// Reset classes
		modalConfirmBtn.className = 'inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto h-9';
		iconWrapper.className = 'mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-[20px] sm:mx-0 sm:h-10 sm:w-10';
		
		if (actionType === 'destructive') {
			modalConfirmBtn.classList.add('bg-red-600', 'hover:bg-red-500');
			iconWrapper.classList.add('bg-red-100');
			// Warning icon for destructive
			icon.classList.remove('text-indigo-600');
			icon.classList.add('text-red-600');
			icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />';
			modalConfirmBtn.textContent = isEnglish ? 'Yes, delete it' : 'Xoá ngay';
		} else {
			// Save or other action
			modalConfirmBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-500');
			iconWrapper.classList.add('bg-indigo-100');
			// Check icon for save
			icon.classList.remove('text-red-600');
			icon.classList.add('text-indigo-600');
			icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />';
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

	window.notify = (message, type = 'success') => {
		window.dispatchEvent(new CustomEvent('notify', { detail: { message, type } }));
	};

})();
