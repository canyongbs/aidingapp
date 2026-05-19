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

declare(strict_types=1);

namespace App\Rector;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class AddInteractsWithMediaUseTagRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add @use InteractsWithMedia<\App\Models\Media> annotation to classes using InteractsWithMedia trait',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
                        use Spatie\MediaLibrary\InteractsWithMedia;

                        class MyModel extends Model
                        {
                            use InteractsWithMedia;
                        }
                        CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                        use Spatie\MediaLibrary\InteractsWithMedia;

                        class MyModel extends Model
                        {
                            /** @use InteractsWithMedia<\App\Models\Media> */
                            use InteractsWithMedia;
                        }
                        CODE_SAMPLE,
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        /** @var Class_ $node */
        foreach ($node->stmts as $stmt) {
            if (! $stmt instanceof TraitUse) {
                continue;
            }

            if (! $this->hasInteractsWithMediaTrait($stmt)) {
                continue;
            }

            if ($this->hasUseTag($stmt)) {
                return null;
            }

            $stmt->setDocComment(new Doc('/** @use InteractsWithMedia<\App\Models\Media> */'));

            return $node;
        }

        return null;
    }

    private function hasInteractsWithMediaTrait(TraitUse $traitUse): bool
    {
        foreach ($traitUse->traits as $trait) {
            if ($this->isName($trait, 'Spatie\MediaLibrary\InteractsWithMedia')) {
                return true;
            }
        }

        return false;
    }

    private function hasUseTag(TraitUse $traitUse): bool
    {
        $docComment = $traitUse->getDocComment();

        if ($docComment === null) {
            return false;
        }

        return str_contains($docComment->getText(), '@use InteractsWithMedia<');
    }
}
