import crypto from 'node:crypto';
import { handleUpload } from '@vercel/blob/client';

export default async function handler(request, response) {
    if (request.method !== 'POST') {
        response.statusCode = 405;
        response.setHeader('Allow', 'POST');
        response.end('Method not allowed');
        return;
    }

    try {
        const body = await readJson(request);
        const jsonResponse = await handleUpload({
            body,
            request,
            onBeforeGenerateToken: async (_pathname, clientPayload) => {
                const intent = verifyIntent(clientPayload);

                return {
                    allowedContentTypes: intent.content_types || ['application/pdf'],
                    maximumSizeInBytes: intent.max_bytes || 104857600,
                    addRandomSuffix: true,
                    tokenPayload: JSON.stringify({
                        user_id: intent.user_id,
                        exp: intent.exp,
                    }),
                };
            },
            onUploadCompleted: async ({ blob, tokenPayload }) => {
                console.log('Project PDF uploaded to Blob', {
                    pathname: blob.pathname,
                    tokenPayload,
                });
            },
        });

        response.statusCode = 200;
        response.setHeader('Content-Type', 'application/json');
        response.end(JSON.stringify(jsonResponse));
    } catch (error) {
        response.statusCode = 400;
        response.setHeader('Content-Type', 'application/json');
        response.end(JSON.stringify({
            message: error instanceof Error ? error.message : 'Blob upload failed.',
        }));
    }
}

async function readJson(request) {
    if (request.body && typeof request.body === 'object') {
        return request.body;
    }

    const chunks = [];

    for await (const chunk of request) {
        chunks.push(Buffer.from(chunk));
    }

    const rawBody = Buffer.concat(chunks).toString('utf8');

    return rawBody ? JSON.parse(rawBody) : {};
}

function verifyIntent(token) {
    if (!token || !token.includes('.')) {
        throw new Error('Missing upload intent.');
    }

    const [payload, signature] = token.split('.');
    const expected = base64Url(crypto.createHmac('sha256', secret()).update(payload).digest());

    if (!timingSafeEqual(signature, expected)) {
        throw new Error('Invalid upload intent.');
    }

    const intent = JSON.parse(Buffer.from(base64UrlToBase64(payload), 'base64').toString('utf8'));

    if (!intent.exp || Date.now() / 1000 > Number(intent.exp)) {
        throw new Error('Upload intent expired.');
    }

    return intent;
}

function secret() {
    const value = process.env.BLOB_UPLOAD_SECRET || process.env.APP_KEY || '';

    if (!value) {
        throw new Error('Upload secret is not configured.');
    }

    return value;
}

function timingSafeEqual(left, right) {
    const leftBuffer = Buffer.from(left);
    const rightBuffer = Buffer.from(right);

    return leftBuffer.length === rightBuffer.length && crypto.timingSafeEqual(leftBuffer, rightBuffer);
}

function base64Url(buffer) {
    return buffer.toString('base64').replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
}

function base64UrlToBase64(value) {
    const padding = '='.repeat((4 - (value.length % 4)) % 4);

    return `${value}${padding}`.replace(/-/g, '+').replace(/_/g, '/');
}
