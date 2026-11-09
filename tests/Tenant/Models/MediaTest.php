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

use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Models\Media;
use Illuminate\Http\UploadedFile;

describe('attachmentContentDisposition', function (): void {
    it('builds an ISO-8859-1 safe disposition for a file name that is not Latin-1 representable', function (): void {
        // macOS screenshots use a narrow no-break space (U+202F) before AM/PM, which S3 rejects in the header.
        $media = new Media();
        $media->file_name = "Screenshot-2026-09-08-at-7.48.39\u{202F}PM.jpg";

        $disposition = $media->attachmentContentDisposition();

        expect(mb_check_encoding($disposition, 'ISO-8859-1'))->toBeTrue()
            ->and($disposition)->toStartWith('attachment;')
            ->and($disposition)->toContain("filename*=utf-8''")
            ->and(rawurldecode($disposition))->toContain($media->file_name);
    });

    it('builds a simple disposition for an ASCII file name', function (): void {
        $media = new Media();
        $media->file_name = 'report.png';

        $disposition = $media->attachmentContentDisposition();

        expect(mb_check_encoding($disposition, 'ISO-8859-1'))->toBeTrue()
            ->and($disposition)->toStartWith('attachment;')
            ->and($disposition)->toContain('report.png')
            ->and($disposition)->not->toContain('filename*');
    });
});

describe('attachmentContentDisposition names', function (): void {
    it('names the download after the uploaded name rather than the stored file name', function (): void {
        $media = new Media();
        $media->name = 'Quarterly Report';
        $media->file_name = 'x9f3k.pdf';

        $disposition = $media->attachmentContentDisposition();

        expect($disposition)->toContain('Quarterly Report.pdf')
            ->and($disposition)->not->toContain('x9f3k');
    });
});

describe('getDownloadFilename', function (): void {
    it('returns the uploaded name with the stored extension', function (): void {
        $media = new Media();
        $media->name = 'Quarterly Report';
        $media->file_name = 'x9f3k.pdf';

        expect($media->getDownloadFilename())->toBe('Quarterly Report.pdf');
    });

    it('does not repeat an extension the name already ends with', function (): void {
        $media = new Media();
        $media->name = 'report.PDF';
        $media->file_name = 'x9f3k.pdf';

        expect($media->getDownloadFilename())->toBe('report.PDF');
    });

    it('falls back to the stored file name when the name is blank', function (): void {
        $media = new Media();
        $media->name = '';
        $media->file_name = 'x9f3k.pdf';

        expect($media->getDownloadFilename())->toBe('x9f3k.pdf');
    });

    it('returns the bare name when the stored file has no extension', function (): void {
        $media = new Media();
        $media->name = 'notes';
        $media->file_name = 'x9f3k';

        expect($media->getDownloadFilename())->toBe('notes');
    });
});

describe('getTemporaryUrl', function (): void {
    $makeMedia = function (string $name): Media {
        $update = ServiceRequestUpdate::factory()->create();

        $update
            ->addMedia(UploadedFile::fake()->image('photo.png'))
            ->usingName($name)
            ->toMediaCollection('uploads');

        return $update->getFirstMedia('uploads');
    };

    $dispositionOf = function (string $url): ?string {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        return $query['response-content-disposition'] ?? null;
    };

    it('serves inline under the uploaded name when no disposition is given', function () use ($makeMedia, $dispositionOf): void {
        $disposition = $dispositionOf($makeMedia('Holiday Photo')->getTemporaryUrl(now()->addMinute()));

        expect($disposition)->toStartWith('inline;')
            ->and($disposition)->toContain('Holiday Photo.png');
    });

    it('adds the uploaded name to a bare attachment disposition', function () use ($makeMedia, $dispositionOf): void {
        $disposition = $dispositionOf($makeMedia('Holiday Photo')->getTemporaryUrl(
            now()->addMinute(),
            options: ['ResponseContentDisposition' => 'attachment'],
        ));

        expect($disposition)->toStartWith('attachment;')
            ->and($disposition)->toContain('Holiday Photo.png');
    });

    it('leaves a full disposition chosen by the caller unchanged', function () use ($makeMedia, $dispositionOf): void {
        $disposition = $dispositionOf($makeMedia('Holiday Photo')->getTemporaryUrl(
            now()->addMinute(),
            options: ['ResponseContentDisposition' => 'attachment; filename="custom.txt"'],
        ));

        expect($disposition)->toBe('attachment; filename="custom.txt"');
    });

    it('keeps the header ISO-8859-1 safe for a name that is not Latin-1 representable', function () use ($makeMedia, $dispositionOf): void {
        $disposition = $dispositionOf($makeMedia("Screenshot 7.48\u{202F}PM")->getTemporaryUrl(now()->addMinute()));

        expect(mb_check_encoding($disposition, 'ISO-8859-1'))->toBeTrue()
            ->and(rawurldecode($disposition))->toContain("Screenshot 7.48\u{202F}PM.png");
    });
});
