import { createIcons, Bold, Italic, Heading2, Link, ImagePlus, Save } from 'lucide';

const editor = document.querySelector('[data-post-editor]');
if (editor) {
    createIcons({ icons: { Bold, Italic, Heading2, Link, ImagePlus, Save } });
    const content = editor.querySelector('#content');
    const form = editor.querySelector('form');
    const input = editor.querySelector('[data-image-input]');
    const feedback = editor.querySelector('[data-feedback]');
    const preview = editor.querySelector('[data-preview]');
    const csrf = form.querySelector('[name="_token"]').value;
    let selection = [content.value.length, content.value.length];
    let uploading = false;
    let dirty = false;
    let previewController;
    const count = () => {
        const words = content.value.trim().split(/\s+/).filter(Boolean).length;
        editor.querySelector('[data-word-count]').textContent = `${words} ${words === 1 ? 'word' : 'words'}`;
    };
    const message = (text, error = false) => {
        feedback.textContent = text;
        feedback.classList.toggle('is-error', error);
    };
    const write = () => {
        previewController?.abort();
        content.hidden = false;
        preview.hidden = true;
        editor.querySelectorAll('[data-mode]').forEach(button => button.setAttribute('aria-pressed', String(button.dataset.mode === 'write')));
    };
    const insert = text => {
        write();
        content.setRangeText(text, ...selection, 'end');
        selection = [content.selectionStart, content.selectionEnd];
        content.dispatchEvent(new Event('input', { bubbles: true }));
        content.focus();
    };
    ['select', 'keyup', 'click', 'blur'].forEach(event => content.addEventListener(event, () => {
        selection = [content.selectionStart, content.selectionEnd];
    }));
    content.addEventListener('input', () => {
        selection = [content.selectionStart, content.selectionEnd];
        count();
    });
    content.addEventListener('invalid', () => { write(); content.focus(); });
    form.addEventListener('input', () => { dirty = true; });
    form.addEventListener('change', () => { dirty = true; });
    window.addEventListener('beforeunload', event => {
        if (dirty || uploading) { event.preventDefault(); event.returnValue = ''; }
    });
    form.addEventListener('submit', event => {
        if (uploading) { event.preventDefault(); message('Please wait for pictures to finish uploading.', true); return; }
        dirty = false;
    });
    editor.querySelectorAll('[data-format]').forEach(button => button.addEventListener('click', () => {
        const selected = content.value.slice(...selection);
        const formats = {
            bold: `**${selected || 'bold text'}**`, italic: `*${selected || 'italic text'}*`,
            heading: `\n\n## ${selected || 'Heading'}\n\n`, link: `[${selected || 'link text'}](https://)`,
        };
        insert(formats[button.dataset.format]);
    }));
    editor.querySelectorAll('[data-mode]').forEach(button => button.addEventListener('click', async () => {
        if (button.dataset.mode === 'write') { write(); return; }
        previewController?.abort();
        const controller = new AbortController();
        previewController = controller;
        content.hidden = true;
        preview.hidden = false;
        preview.textContent = 'Loading preview...';
        editor.querySelectorAll('[data-mode]').forEach(item => item.setAttribute('aria-pressed', String(item === button)));
        try {
            const response = await fetch(editor.dataset.previewUrl, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ content: content.value }), signal: controller.signal,
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Preview could not be loaded.');
            preview.innerHTML = result.html || '<p>No content yet.</p>';
        } catch (error) {
            if (error.name !== 'AbortError') preview.textContent = error.message;
        }
    }));
    const markdownAlt = name => name.replace(/\.[^.]+$/, '').replace(/[\[\]\\\r\n]/g, '').slice(0, 120);
    async function upload(files) {
        if (uploading || !files.length) return;
        uploading = true;
        const buttons = editor.querySelectorAll('[data-pick-image], .editor-save');
        buttons.forEach(button => { button.disabled = true; });
        let uploaded = 0;
        const errors = [];
        try {
            for (const file of files) {
                if (!['image/jpeg', 'image/png', 'image/webp', 'image/gif'].includes(file.type) || file.size > 5 * 1024 * 1024) {
                    errors.push(`${file.name}: choose a supported image up to 5 MB.`); continue;
                }
                message(`Uploading ${file.name}...`);
                try {
                    const data = new FormData(); data.append('image', file);
                    const response = await fetch(editor.dataset.uploadUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, body: data });
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.errors?.image?.[0] || result.message || 'Upload failed.');
                    insert(`\n\n![${markdownAlt(file.name)}](${result.url})\n\n`);
                    const image = document.createElement('img'); image.src = result.url; image.alt = file.name;
                    const button = document.createElement('button'); button.type = 'button'; button.title = `Insert ${file.name} again`;
                    button.setAttribute('aria-label', `Insert ${file.name} again`); button.append(image);
                    button.addEventListener('click', () => insert(`\n\n![${markdownAlt(file.name)}](${result.url})\n\n`));
                    editor.querySelector('[data-image-list]').append(button);
                    uploaded++;
                } catch (error) { errors.push(`${file.name}: ${error.message}`); }
            }
        } finally {
            uploading = false; buttons.forEach(button => { button.disabled = false; }); input.value = '';
            message(errors.length ? errors.join(' ') : `${uploaded} ${uploaded === 1 ? 'picture' : 'pictures'} added.`, errors.length > 0);
        }
    }
    editor.querySelectorAll('[data-pick-image]').forEach(button => button.addEventListener('click', () => input.click()));
    input.addEventListener('change', () => upload([...input.files]));
    content.addEventListener('dragover', event => { event.preventDefault(); });
    content.addEventListener('drop', event => {
        if (event.dataTransfer.files.length) { event.preventDefault(); upload([...event.dataTransfer.files]); }
    });
    count();
}
