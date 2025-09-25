/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

(function () {
    // Get the portal embed element
    const portalEmbedElement = document.querySelector('knowledge-management-portal-embed');
    if (!portalEmbedElement) return;

    // Get the resources URL from the element
    const resourcesUrl = portalEmbedElement.getAttribute('resources-url');
    if (!resourcesUrl) return;

    // Fetch the latest resource URLs
    fetch(resourcesUrl)
        .then(response => response.json())
        .then(resources => {
            // Apply the CSS URL as an attribute to the portal embed
            if (resources.css) {
                portalEmbedElement.setAttribute('css-url', resources.css);
            }

            // Load the JS
            if (resources.js) {
                const scriptElement = document.createElement('script');
                scriptElement.src = resources.js;
                document.body.appendChild(scriptElement);
            }
        })
        .catch(error => {
            console.error('Failed to load portal resources:', error);
        });
})();