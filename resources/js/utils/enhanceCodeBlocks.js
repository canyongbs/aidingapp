/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

// Shared, dependency-free helper that enhances rendered code blocks (`<pre><code>`)
// with a line-number gutter and a copy-to-clipboard button. It is used only for
// rich content *output*, never inside the rich editor, so the editing experience
// stays untouched.

// Icons are copied exactly from https://heroicons.com using the 16px "Micro"
// solid variant, which matches the button's rendered size (0.875rem ≈ 14px).
// Copy: `document-duplicate`. Copied: `check`.
const COPY_ICON =
    '<svg class="code-block-copy-icon code-block-copy-icon-copy" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon"><path d="M5.5 3.5A1.5 1.5 0 0 1 7 2h2.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 1 .439 1.061V9.5A1.5 1.5 0 0 1 12 11V8.621a3 3 0 0 0-.879-2.121L9 4.379A3 3 0 0 0 6.879 3.5H5.5Z"/><path d="M4 5a1.5 1.5 0 0 0-1.5 1.5v6A1.5 1.5 0 0 0 4 14h5a1.5 1.5 0 0 0 1.5-1.5V8.621a1.5 1.5 0 0 0-.44-1.06L7.94 5.439A1.5 1.5 0 0 0 6.878 5H4Z"/></svg>';

const CHECK_ICON =
    '<svg class="code-block-copy-icon code-block-copy-icon-check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" data-slot="icon"><path fill-rule="evenodd" d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd"/></svg>';

// Do not enhance code blocks that live inside an editor / editable region.
const SKIP_SELECTOR = '.fi-fo-rich-editor, .tiptap, .ProseMirror, [contenteditable="true"], [contenteditable=""]';

function copyToClipboard(value, onSuccess) {
    if (navigator.clipboard?.writeText) {
        navigator.clipboard
            .writeText(value)
            .then(onSuccess)
            .catch(() => fallbackCopy(value, onSuccess));

        return;
    }

    fallbackCopy(value, onSuccess);
}

function fallbackCopy(value, onSuccess) {
    const textarea = document.createElement('textarea');
    textarea.value = value;
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'absolute';
    textarea.style.left = '-9999px';
    document.body.appendChild(textarea);
    textarea.select();

    try {
        document.execCommand('copy');
        onSuccess();
    } catch (error) {
        // Ignore: clipboard access is unavailable.
    }

    document.body.removeChild(textarea);
}

function enhanceCodeBlock(pre) {
    if (!pre || pre.dataset.codeBlockEnhanced === 'true') {
        return;
    }

    if (pre.closest(SKIP_SELECTOR)) {
        return;
    }

    const code = pre.querySelector('code');

    if (!code) {
        return;
    }

    pre.dataset.codeBlockEnhanced = 'true';

    const lineCount = code.textContent.replace(/\n$/, '').split('\n').length;
    const numbers = [];

    for (let line = 1; line <= lineCount; line++) {
        numbers.push(line);
    }

    const gutter = document.createElement('span');
    gutter.className = 'code-block-line-numbers';
    gutter.setAttribute('aria-hidden', 'true');
    gutter.textContent = numbers.join('\n');
    pre.insertBefore(gutter, code);

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'code-block-copy-button';
    button.setAttribute('aria-label', 'Copy code to clipboard');
    button.innerHTML = COPY_ICON + CHECK_ICON;
    button.addEventListener('click', () => {
        copyToClipboard(code.textContent, () => {
            button.classList.add('copied');
            window.setTimeout(() => button.classList.remove('copied'), 2000);
        });
    });
    pre.appendChild(button);
}

/**
 * Enhance every rendered code block found within the given root element.
 *
 * @param {ParentNode} [root=document] The element (or document) to search within.
 */
export function enhanceCodeBlocks(root = document) {
    if (!root || typeof root.querySelectorAll !== 'function') {
        return;
    }

    root.querySelectorAll('pre').forEach((pre) => enhanceCodeBlock(pre));

    if (root.nodeType === Node.ELEMENT_NODE && root.matches?.('pre')) {
        enhanceCodeBlock(root);
    }
}

/**
 * Vue directive that enhances rendered code blocks within the bound element.
 * Re-runs on update so it works alongside `v-html`. Shared with the Vue portal.
 */
export const codeBlocksDirective = {
    mounted(element) {
        enhanceCodeBlocks(element);
    },
    updated(element) {
        enhanceCodeBlocks(element);
    },
};
