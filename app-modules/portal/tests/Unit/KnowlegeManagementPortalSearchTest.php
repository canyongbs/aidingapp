<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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

use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\Portal\Settings\PortalSettings;
use App\Models\Tag;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\post;

test('search will not work if Knowledge Management Portal is not enabled.', function () {
    $url = URL::signedRoute(name: 'api.portal.search', absolute: false);
    $response = post($url);
    $response->assertStatus(403);
    $response->assertSee('Knowledge Management Portal is not enabled.');
});

test('search will work if Knowledge Management Portal is enabled.', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;

    $settings->save();

    $url = URL::signedRoute(name: 'api.portal.search', absolute: false);
    $response = post($url);

    $response->assertStatus(201);
});

test('categories and items are returned without filtering', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $knowledgeBaseCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Educational Trends and Insights',
        'slug' => 'educational-trends-and-insights',
        'description' => 'Focus on the latest developments, research, and innovative ideas shaping the future of education across the globe.',
    ]);

    KnowledgeBaseItem::factory()->state([
        'id' => '9dbe944d-330e-40c1-94b2-3312b07a7588',
        'public' => true,
        'title' => 'Gamification in Education: Transforming Classrooms into Playgrounds',
        'article_details' => ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'This article delves into the concept of gamification in education']]]]],
        'notes' => 'make classrooms more interactive.',
    ])
        ->for($knowledgeBaseCategory, 'category')
        ->create();

    $url = URL::signedRoute(name: 'api.portal.search', absolute: false);
    $response = post($url);

    expect($response)->toMatchSnapshot();
});

test('category should present in response', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    KnowledgeBaseCategory::factory()->create([
        'name' => 'Innovative Learning Approaches',
        'slug' => 'innovative-learning-approaches',
        'description' => 'Explore new methods and tools that are revolutionizing the way we teach and learn.',
    ]);

    $url = URL::signedRoute(name: 'api.portal.search', absolute: false);
    $response = post($url);

    $response = $response->json();

    expect($response)->toMatchSnapshot();
});

test('filter featured articles', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $knowledgeBaseCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Tech-Driven Education',
        'slug' => 'tech-driven-education',
        'description' => 'Explore how technology is shaping education, from virtual classrooms to AI-driven learning tools.',
    ])->create();

    KnowledgeBaseItem::factory()->state([
        'id' => '9dbe944d-330e-40c1-94b2-3312b07a7590',
        'is_featured' => true,
        'title' => 'Gamification in Education: Transforming Classrooms into Playgrounds',
        'category_id' => $knowledgeBaseCategory->getKey(),
        'public' => true,
    ])
        ->create();

    KnowledgeBaseItem::factory()->state([
        'id' => '9dbe944d-330e-40c1-94b2-3312b07a7557',
        'is_featured' => false,
        'title' => 'The Rise of Microlearning: Bite-Sized Education for a Fast-Paced World',
        'category_id' => $knowledgeBaseCategory->getKey(),
        'public' => true,
    ])
        ->create();

    $url = URL::signedRoute(name: 'api.portal.search', absolute: false);
    $response = post($url, ['filter' => 'featured']);

    $response = $response->json();

    expect($response)->toMatchSnapshot();
});

test('filter article based on selected tags', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $knowledgeBaseCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Tech-Driven Education',
        'slug' => 'tech-driven-education',
        'description' => 'Explore how technology is shaping education, from virtual classrooms to AI-driven learning tools.',
    ])->create();

    $tag = Tag::factory()
        ->state([
            'id' => '9dbe944d-330e-40c1-94b2-3312b07a1829',
            'name' => 'Education',
        ])
        ->create();

    KnowledgeBaseItem::factory()->state([
        'id' => '9dbe944d-330e-40c1-94b2-3312b07a7590',
        'title' => 'Gamification in Education: Transforming Classrooms into Playgrounds',
        'category_id' => $knowledgeBaseCategory->getKey(),
        'is_featured' => false,
        'public' => true,
    ])
        ->hasAttached($tag)
        ->create();

    KnowledgeBaseItem::factory()->state([
        'id' => '9dbe944d-330e-40c1-94b2-3312b07a7557',
        'title' => 'The Rise of Microlearning: Bite-Sized Education for a Fast-Paced World',
        'category_id' => $knowledgeBaseCategory->getKey(),
        'is_featured' => true,
        'public' => true,
    ])
        ->hasAttached(Tag::factory()->state(['name' => 'Student']))
        ->create();

    $url = URL::signedRoute(name: 'api.portal.search', absolute: false);
    $response = post($url, ['tags' => $tag->getKey()]);

    $response = $response->json();

    expect($response)->toMatchSnapshot();
});

test('filter article and category based on searched keyword', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $knowledgeBaseCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Student-Centered Learning',
        'slug' => 'student-centered-learning',
        'description' => 'Focus on approaches that place students\' needs, interests, and goals at the forefront of education.',
    ])->create();

    $otherknowledgeBaseCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Teaching Techniques Unlocked',
        'slug' => 'teaching-techniques-unlocked',
        'description' => 'Deep dive into methods and tools that empower educators to create impactful learning experiences.',
    ])->create();

    KnowledgeBaseItem::factory()->state([
        'id' => '9dbe944d-330e-40c1-94b2-3312b07a1355',
        'is_featured' => true,
        'title' => 'The Science of Learning: Cognitive Strategies That Work',
        'category_id' => $otherknowledgeBaseCategory->getKey(),
        'public' => true,
    ])
        ->create();

    KnowledgeBaseItem::factory()->state([
        'id' => '9dbe944d-330e-40c1-94b2-3312b07b7589',
        'is_featured' => false,
        'title' => 'Personalized Learning: Tailoring Education for Every Student',
        'category_id' => $knowledgeBaseCategory->getKey(),
        'public' => true,
    ])
        ->create();

    KnowledgeBaseItem::factory()->state([
        'id' => '9dbe944d-330e-40c1-94b2-3312b07c7547',
        'is_featured' => false,
        'title' => 'From STEM to STEAM: The Power of Creativity in Education',
        'category_id' => $knowledgeBaseCategory->getKey(),
        'public' => true,
    ])
        ->create();

    $url = URL::signedRoute(name: 'api.portal.search', absolute: false);
    $response = post($url, ['search' => json_encode('Learning')]);

    $response = $response->json();

    expect($response)->toMatchSnapshot();
});

test('filter most viewed articles', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $knowledgeBaseCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Student-Centered Learning',
        'slug' => 'student-centered-learning',
        'description' => 'Focus on approaches that place students\' needs, interests, and goals at the forefront of education.',
    ])->create();

    $otherknowledgeBaseCategory = KnowledgeBaseCategory::factory()->state([
        'name' => 'Teaching Techniques Unlocked',
        'slug' => 'teaching-techniques-unlocked',
        'description' => 'Deep dive into methods and tools that empower educators to create impactful learning experiences.',
    ])->create();

    KnowledgeBaseItem::factory()->state([
        'id' => '9dbe944d-330e-40c1-94b2-3312b07a1355',
        'is_featured' => true,
        'title' => 'The Science of Learning: Cognitive Strategies That Work',
        'category_id' => $otherknowledgeBaseCategory->getKey(),
        'public' => true,
        'portal_view_count' => 0,
    ])
        ->create();

    KnowledgeBaseItem::factory()->state([
        'id' => '9dbe944d-330e-40c1-94b2-3312b07b7589',
        'is_featured' => false,
        'title' => 'Personalized Learning: Tailoring Education for Every Student',
        'category_id' => $knowledgeBaseCategory->getKey(),
        'public' => true,
        'portal_view_count' => 25,
    ])
        ->create();

    KnowledgeBaseItem::factory()->state([
        'id' => '9dbe944d-330e-40c1-94b2-3312b07c7547',
        'is_featured' => false,
        'title' => 'From STEM to STEAM: The Power of Creativity in Education',
        'category_id' => $knowledgeBaseCategory->getKey(),
        'public' => true,
        'portal_view_count' => 50,
    ])
        ->create();

    $url = URL::signedRoute(name: 'api.portal.search', absolute: false);
    $response = post($url, ['filter' => 'most-viewed']);

    $response = $response->json();

    expect($response)->toMatchSnapshot();
});
