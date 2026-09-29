/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

</COPYRIGHT>
*/

const publicIdPattern = /-[0-9A-Za-z]{8}$/;

export function hasPublicIdLocator(locator) {
    return publicIdPattern.test(locator ?? '');
}

export function categoryLocator(category) {
    return category.publicId ? `${category.slug}-${category.publicId}` : category.slug;
}

export function categoryRoute(category) {
    if (category.publicId && category.parentCategory?.publicId) {
        return {
            name: 'view-subcategory',
            params: {
                parentCategorySlug: categoryLocator(category.parentCategory),
                categorySlug: categoryLocator(category),
            },
        };
    }

    return {
        name: 'view-category',
        params: { categorySlug: categoryLocator(category) },
    };
}

export function articleRoute(article, category = article.category) {
    if (!article.publicId || !category?.publicId) {
        return {
            name: 'view-article',
            params: { categorySlug: article.categorySlug, articleId: article.id },
        };
    }

    const params = {
        categorySlug: categoryLocator(category),
        articleId: `${article.slug}-${article.publicId}`,
    };

    if (category.parentCategory?.publicId) {
        return {
            name: 'view-subcategory-article',
            params: {
                ...params,
                parentCategorySlug: categoryLocator(category.parentCategory),
            },
        };
    }

    return { name: 'view-article', params };
}
