/* modal.js — confirmação customizada para TechPortal */
(function () {
  // Cria o HTML do modal uma única vez
  const overlay = document.createElement('div');
  overlay.className = 'modal-overlay';
  overlay.innerHTML = `
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modal-title">
      <div class="modal-icon" id="modal-icon">⚠️</div>
      <div class="modal-title" id="modal-title">Confirmar ação</div>
      <div class="modal-msg"  id="modal-msg"></div>
      <div class="modal-actions">
        <button class="btn btn-outline" id="modal-cancel">Cancelar</button>
        <button class="btn btn-danger"  id="modal-confirm">Confirmar</button>
      </div>
    </div>`;
  document.body.appendChild(overlay);

  const cancelBtn  = overlay.querySelector('#modal-cancel');
  const confirmBtn = overlay.querySelector('#modal-confirm');
  let resolveModal = null;

  function openModal({ title, msg, icon, confirmLabel, confirmClass }) {
    overlay.querySelector('#modal-title').textContent = title       || 'Confirmar ação';
    overlay.querySelector('#modal-msg').textContent   = msg         || 'Tem certeza?';
    overlay.querySelector('#modal-icon').textContent  = icon        || '⚠️';
    confirmBtn.textContent  = confirmLabel  || 'Confirmar';
    confirmBtn.className    = 'btn btn-sm ' + (confirmClass || 'btn-danger');
    overlay.classList.add('active');
    confirmBtn.focus();
    return new Promise(res => { resolveModal = res; });
  }

  function closeModal(result) {
    overlay.classList.remove('active');
    if (resolveModal) resolveModal(result);
    resolveModal = null;
  }

  cancelBtn.addEventListener('click',  () => closeModal(false));
  overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(false); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(false); });
  confirmBtn.addEventListener('click', () => closeModal(true));

  // Intercepta todos os links com data-confirm
  document.addEventListener('click', async function (e) {
    const el = e.target.closest('[data-confirm]');
    if (!el) return;
    e.preventDefault();

    const ok = await openModal({
      title:        el.dataset.confirmTitle  || 'Confirmar ação',
      msg:          el.dataset.confirm,
      icon:         el.dataset.confirmIcon   || '⚠️',
      confirmLabel: el.dataset.confirmLabel  || 'Confirmar',
      confirmClass: el.dataset.confirmClass  || 'btn-danger',
    });

    if (ok) {
      // Se for um <a>, navega para o href
      if (el.tagName === 'A' && el.href) {
        window.location.href = el.href;
      }
      // Se for um <button> dentro de um form, submete
      if (el.tagName === 'BUTTON' && el.form) {
        el.form.submit();
      }
    }
  });
})();
