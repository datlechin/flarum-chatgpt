app.initializers.add('kubano09-chatgpt-writer', () => {
  // Mantener la insignia original si quieres
  const PostUser = flarum.core.compat['forum/components/PostUser'].default;
  const extend = flarum.core.compat['common/extend'].extend;
  extend(PostUser.prototype, 'view', function (vdom) {
    const user = this.attrs.post.user();
    if (user && app.forum.attribute('chatGptUserPromptId') === user.id()) {
      vdom.children.push(
        m('div', { className: 'UserPromo-badge' },
          m('div', { className: 'badge' }, app.forum.attribute('chatGptBadgeText'))
        )
      );
    }
  });

  // Nuevo: botón IA en el editor
  function addButton() {
    document.querySelectorAll('.TextEditor-controls').forEach((bar) => {
      if (bar.querySelector('.ChatGPTWriter-btn')) return;
      const btn = document.createElement('button');
      btn.className = 'Button Button--icon ChatGPTWriter-btn';
      btn.type = 'button';
      btn.title = 'Redactar con IA';
      btn.innerHTML = '<i class="fas fa-wand-magic-sparkles"></i>';
      btn.addEventListener('click', async () => {
        const prompt = window.prompt('¿Sobre qué quieres que escriba?');
        if (!prompt) return;
        try {
          const res = await fetch(app.forum.attribute('apiUrl') + '/chatgpt-writer/generate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ prompt })
          });
          const data = await res.json();
          if (data.text) {
            (app.composer && app.composer.editor)
              ? app.composer.editor.insertAtCursor(data.text)
              : alert(data.text);
          } else {
            alert(data.error || 'No se recibió texto');
          }
        } catch (e) {
          alert('Error llamando a la IA: ' + e.message);
        }
      });
      bar.appendChild(btn);
    });
  }

  addButton();
  const mo = new MutationObserver(addButton);
  mo.observe(document.body, { childList: true, subtree: true });
});
