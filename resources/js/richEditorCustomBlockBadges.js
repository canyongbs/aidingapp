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

// Filament renders a rich editor custom block's header label as a plain text node, so it cannot
// show a "Required"/"Optional" badge. This adds one after Filament renders the header, only in
// editors that opt in with `data-custom-block-badges`.
const EDITOR_SELECTOR = '[data-custom-block-badges]';
const BLOCK_SELECTOR = 'div[data-type="customBlock"]';

function createBadge(label) {
    const badge = document.createElement('span');

    badge.className = 'fi-fo-rich-editor-custom-block-badge';
    badge.textContent = label;

    return badge;
}

function decorateBlock(block) {
    if (!block.closest(EDITOR_SELECTOR)) {
        return;
    }

    const heading = block.querySelector(
        ':scope > .fi-fo-rich-editor-custom-block-header > .fi-fo-rich-editor-custom-block-heading',
    );

    if (!heading) {
        return;
    }

    heading.querySelector(':scope > .fi-fo-rich-editor-custom-block-badges')?.remove();

    let config = {};

    try {
        config = JSON.parse(block.getAttribute('data-config') ?? '{}') ?? {};
    } catch {
        return;
    }

    const requiredFlags = Object.entries(config)
        .filter(([key, value]) => /required$/i.test(key) && typeof value === 'boolean')
        .map(([, value]) => value);

    if (requiredFlags.length === 0) {
        return;
    }

    const badges = document.createElement('span');

    badges.className = 'fi-fo-rich-editor-custom-block-badges';
    badges.appendChild(createBadge(requiredFlags.some(Boolean) ? 'Required' : 'Optional'));

    // Inside the heading, so the badge sits beside the label rather than at the far right.
    heading.appendChild(badges);
}

function decorateAll(root) {
    if (root.matches?.(BLOCK_SELECTOR)) {
        decorateBlock(root);
    }

    root.querySelectorAll?.(BLOCK_SELECTOR).forEach(decorateBlock);
}

function observe() {
    decorateAll(document);

    new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            // A block's config changes the `data-config` attribute in place, so refresh on that too.
            if (mutation.type === 'attributes') {
                decorateAll(mutation.target);

                continue;
            }

            for (const node of mutation.addedNodes) {
                if (node instanceof HTMLElement) {
                    decorateAll(node);
                }
            }
        }
    }).observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['data-config'],
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', observe);
} else {
    observe();
}
