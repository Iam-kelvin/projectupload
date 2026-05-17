import './bootstrap';
import { upload } from '@vercel/blob/client';

document.addEventListener('submit', (event) => {
    const form = event.target;
    const message = form.getAttribute('data-confirm');

    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});

const directUploadForms = document.querySelectorAll('form [data-direct-upload]');

directUploadForms.forEach((statusBox) => {
    const form = statusBox.closest('form');
    const fileInput = form?.querySelector('[data-direct-file]');

    if (!form || !fileInput) {
        return;
    }

    form.addEventListener('submit', async (event) => {
        const file = fileInput.files?.[0];

        if (!file || statusBox.dataset.uploaded === 'true') {
            return;
        }

        event.preventDefault();

        const submitter = form.querySelector('[type="submit"]');
        const originalLabel = submitter?.textContent;
        const maxBytes = Number(statusBox.dataset.maxBytes || 0);

        if (maxBytes && file.size > maxBytes) {
            statusBox.hidden = false;
            statusBox.textContent = `This PDF is too large for direct upload. Maximum allowed size is ${Math.round(maxBytes / 1024 / 1024)} MB.`;
            return;
        }

        try {
            statusBox.hidden = false;
            statusBox.textContent = 'Preparing PDF upload...';
            if (submitter) {
                submitter.disabled = true;
                submitter.textContent = 'Uploading PDF...';
            }

            const hash = await sha256(file);
            const pathname = `projects/${Date.now()}-${safeFileName(file.name)}`;

            statusBox.textContent = 'Uploading PDF directly to cloud storage...';

            const blob = await upload(pathname, file, {
                access: statusBox.dataset.access || 'public',
                contentType: file.type || 'application/pdf',
                handleUploadUrl: statusBox.dataset.handleUrl || '/blob/project-upload',
                clientPayload: statusBox.dataset.clientPayload || '',
            });

            setCloudField(form, 'url', blob.url);
            setCloudField(form, 'downloadUrl', blob.downloadUrl || blob.url);
            setCloudField(form, 'pathname', blob.pathname || pathname);
            setCloudField(form, 'originalName', file.name);
            setCloudField(form, 'mime', file.type || 'application/pdf');
            setCloudField(form, 'size', String(file.size));
            setCloudField(form, 'hash', hash);

            statusBox.dataset.uploaded = 'true';
            statusBox.textContent = 'PDF uploaded. Saving project details...';
            fileInput.disabled = true;
            form.requestSubmit();
        } catch (error) {
            statusBox.hidden = false;
            statusBox.textContent = error instanceof Error ? error.message : 'Cloud upload failed. Please try again.';
            if (submitter) {
                submitter.disabled = false;
                submitter.textContent = originalLabel;
            }
        }
    });
});

function setCloudField(form, name, value) {
    const input = form.querySelector(`[data-cloud-field="${name}"]`);

    if (input) {
        input.value = value || '';
    }
}

function safeFileName(name) {
    return name
        .toLowerCase()
        .replace(/[^a-z0-9._-]+/g, '-')
        .replace(/^-+|-+$/g, '') || 'project.pdf';
}

async function sha256(file) {
    const buffer = await file.arrayBuffer();
    const digest = await crypto.subtle.digest('SHA-256', buffer);

    return Array.from(new Uint8Array(digest))
        .map((byte) => byte.toString(16).padStart(2, '0'))
        .join('');
}
