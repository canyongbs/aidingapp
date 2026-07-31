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

import { defineColadaLoader } from 'vue-router/experimental/pinia-colada';
import { apiGet } from '../Services/api.js';

const fiveMinutes = 1000 * 60 * 5;

/**
 * Resolves auth-related failures (401/403) and missing records (404) to `null`.
 */
async function tolerant(promise, { notFound = false } = {}) {
    try {
        return await promise;
    } catch (error) {
        const status = error?.response?.status;
        const tolerated = [401, 403, ...(notFound ? [404] : [])];

        if (status && tolerated.includes(status)) {
            return null;
        }

        throw error;
    }
}

export const useCategoriesData = defineColadaLoader({
    key: () => ['knowledge-management', 'categories'],
    query: () => apiGet('/categories'),
    staleTime: fiveMinutes,
});

export const useTagsData = defineColadaLoader({
    key: () => ['knowledge-management', 'tags'],
    query: () => apiGet('/tags'),
    staleTime: fiveMinutes,
});

export const useCategoryData = defineColadaLoader({
    key: (to) => ['knowledge-management', 'category', String(to.params.categorySlug)],
    query: (to) => tolerant(apiGet(`/categories/${to.params.categorySlug}`), { notFound: true }),
    staleTime: fiveMinutes,
});

export const useArticleData = defineColadaLoader({
    key: (to) => ['knowledge-management', 'article', String(to.params.categorySlug), String(to.params.articleId)],
    query: (to) =>
        tolerant(apiGet(`/categories/${to.params.categorySlug}/articles/${to.params.articleId}`), { notFound: true }),
});

export const useServiceRequestsData = defineColadaLoader({
    key: () => ['knowledge-management', 'service-requests'],
    query: () => tolerant(apiGet('/service-requests')),
    staleTime: fiveMinutes,
});

export const useServiceRequestData = defineColadaLoader({
    key: (to) => ['knowledge-management', 'service-request', String(to.params.serviceRequestId)],
    query: (to) => tolerant(apiGet(`/service-request/${to.params.serviceRequestId}`, { page: 1 }), { notFound: true }),
});

export const useServiceMonitorData = defineColadaLoader({
    key: () => ['knowledge-management', 'service-monitors'],
    query: () => tolerant(apiGet('/status', { page: 1 })),
    staleTime: fiveMinutes,
});

export const useAdvisoriesData = defineColadaLoader({
    key: () => ['knowledge-management', 'advisories'],
    query: () => tolerant(apiGet('/advisories', { page: 1 })),
    staleTime: fiveMinutes,
});

export const useAssetsData = defineColadaLoader({
    key: () => ['knowledge-management', 'assets'],
    query: () => tolerant(apiGet('/assets', { filter: 'all', page: 1 })),
    staleTime: fiveMinutes,
});

export const useLicensesData = defineColadaLoader({
    key: () => ['knowledge-management', 'licenses'],
    query: () => tolerant(apiGet('/product-licenses')),
    staleTime: fiveMinutes,
});

export const useServiceRequestTypesData = defineColadaLoader({
    key: () => ['knowledge-management', 'service-request-types'],
    query: () => tolerant(apiGet('/service-request-type/select')),
});

export const useCreateServiceRequestData = defineColadaLoader({
    key: (to) => [
        'knowledge-management',
        'create-service-request',
        String(to.params.typeId),
        String(to.query.category ?? ''),
    ],
    query: (to) =>
        tolerant(apiGet(`/service-request/create/${to.params.typeId}`, { category: to.query.category }), {
            notFound: true,
        }),
});
