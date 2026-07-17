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

import { enhanceCodeBlocks } from './utils/enhanceCodeBlocks';

// Line numbers and a copy button are only added to rendered knowledge base
// *article* content (the article view page), never to the editor or other rich
// content such as chat messages or AI answers.
const OUTPUT_SELECTOR = '.knowledge-base-article';

function enhanceDocument() {
    document.querySelectorAll(OUTPUT_SELECTOR).forEach((container) => enhanceCodeBlocks(container));
}

let scheduled = false;

function scheduleEnhance() {
    if (scheduled) {
        return;
    }

    scheduled = true;

    window.requestAnimationFrame(() => {
        scheduled = false;
        enhanceDocument();
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', enhanceDocument);
} else {
    enhanceDocument();
}

const observer = new MutationObserver((mutations) => {
    for (const mutation of mutations) {
        for (const node of mutation.addedNodes) {
            if (node.nodeType !== Node.ELEMENT_NODE) {
                continue;
            }

            if (node.matches?.(OUTPUT_SELECTOR) || node.querySelector?.(OUTPUT_SELECTOR)) {
                scheduleEnhance();

                return;
            }
        }
    }
});

if (document.body) {
    observer.observe(document.body, { childList: true, subtree: true });
} else {
    document.addEventListener('DOMContentLoaded', () => {
        observer.observe(document.body, { childList: true, subtree: true });
    });
}
