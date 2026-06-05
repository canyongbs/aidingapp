<?php

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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\Portal\Settings\PortalSettings;
use App\Settings\LicenseSettings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

beforeEach(function () {
    Storage::fake('s3');

    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->save();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->knowledgeManagement = true;
    $settings->save();
});

test('a user can download an attachment from a public knowledge base article', function () {
    asSuperAdmin();

    $article = KnowledgeBaseItem::factory()->create(['public' => true]);
    $article
        ->addMedia(UploadedFile::fake()->create('document.pdf', 100))
        ->toMediaCollection('article_attachments');

    $media = $article->getFirstMedia('article_attachments');

    get(route('api.portal.knowledge-base-article.media.download', ['media' => $media->getKey()]))
        ->assertRedirect();
});

test('a user cannot download an attachment from a private knowledge base article', function () {
    asSuperAdmin();

    $article = KnowledgeBaseItem::factory()->create(['public' => false]);
    $article
        ->addMedia(UploadedFile::fake()->create('document.pdf', 100))
        ->toMediaCollection('article_attachments');

    $media = $article->getFirstMedia('article_attachments');

    get(route('api.portal.knowledge-base-article.media.download', ['media' => $media->getKey()]))
        ->assertForbidden();
});

test('a user cannot download media that is not in the article_attachments collection', function () {
    asSuperAdmin();

    $article = KnowledgeBaseItem::factory()->create(['public' => true]);
    $article
        ->addMedia(UploadedFile::fake()->image('image.png'))
        ->toMediaCollection('article_details');

    $media = $article->getFirstMedia('article_details');

    get(route('api.portal.knowledge-base-article.media.download', ['media' => $media->getKey()]))
        ->assertForbidden();
});

test('a user cannot download media that does not belong to a knowledge base article', function () {
    asSuperAdmin();

    $article = KnowledgeBaseItem::factory()->create(['public' => true]);
    $article
        ->addMedia(UploadedFile::fake()->create('document.pdf', 100))
        ->toMediaCollection('article_attachments');

    $media = $article->getFirstMedia('article_attachments');

    $media->update(['model_type' => 'some_other_model']);

    get(route('api.portal.knowledge-base-article.media.download', ['media' => $media->getKey()]))
        ->assertForbidden();
});

test('the download redirect url contains a temporary url with the file name', function () {
    asSuperAdmin();

    $article = KnowledgeBaseItem::factory()->create(['public' => true]);
    $article
        ->addMedia(UploadedFile::fake()->create('test-document.pdf', 100))
        ->toMediaCollection('article_attachments');

    $media = $article->getFirstMedia('article_attachments');

    $response = get(route('api.portal.knowledge-base-article.media.download', ['media' => $media->getKey()]));

    $response->assertRedirect();
    $redirectUrl = $response->headers->get('Location');
    expect($redirectUrl)->toContain($media->file_name);
});

test('a previously public article that becomes private denies download access', function () {
    asSuperAdmin();

    $article = KnowledgeBaseItem::factory()->create(['public' => true]);
    $article
        ->addMedia(UploadedFile::fake()->create('document.pdf', 100))
        ->toMediaCollection('article_attachments');

    $media = $article->getFirstMedia('article_attachments');

    get(route('api.portal.knowledge-base-article.media.download', ['media' => $media->getKey()]))
        ->assertRedirect();

    $article->update(['public' => false]);

    get(route('api.portal.knowledge-base-article.media.download', ['media' => $media->getKey()]))
        ->assertForbidden();
});
