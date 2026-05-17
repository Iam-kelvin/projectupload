<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectLibraryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_public_project_search_and_show_pages_work(): void
    {
        $category = Category::factory()->create(['name' => 'Artificial Intelligence', 'slug' => 'artificial-intelligence']);
        $tag = Tag::factory()->create(['name' => 'Search', 'slug' => 'search']);
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'title' => 'Neural Archive Retrieval',
            'abstract' => 'A searchable archive for project documents.',
            'pdf_text' => 'latent vector indexing and semantic search',
        ]);
        $project->tags()->attach($tag);

        $this->get('/projects?q=semantic')
            ->assertOk()
            ->assertSee('Neural Archive Retrieval')
            ->assertSee('Artificial Intelligence');

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('latent vector indexing and semantic search')
            ->assertSee('Log in to continue');
    }

    public function test_normal_users_can_upload_and_edit_only_their_own_projects(): void
    {
        Storage::fake('local');

        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $otherUser = User::factory()->create(['role' => User::ROLE_USER]);
        $category = Category::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('projects.store'), array_merge($this->projectPayload($category), [
                'pdf_file' => UploadedFile::fake()->createWithContent('reader.pdf', '%PDF-1.4 reader upload'),
            ]));

        $project = Project::where('title', 'Reliable Project Repository')->firstOrFail();

        $response->assertRedirect(route('projects.show', $project));
        $this->assertSame($user->id, $project->uploaded_by);

        $this->actingAs($user)
            ->get(route('projects.edit', $project))
            ->assertOk()
            ->assertSee('Edit project');

        $this->actingAs($otherUser)
            ->get(route('projects.edit', $project))
            ->assertForbidden();
    }

    public function test_signed_in_users_can_save_and_track_viewed_projects(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $project = Project::factory()->create(['title' => 'Saved Reading Target']);

        $this->actingAs($user)
            ->get(route('projects.show', $project))
            ->assertOk();

        $this->assertDatabaseHas('project_views', [
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);

        $this->actingAs($user)
            ->post(route('projects.save', $project))
            ->assertRedirect();

        $this->assertDatabaseHas('project_saves', [
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Saved Reading Target')
            ->assertSee('Recently viewed');

        $this->actingAs($user)
            ->delete(route('projects.unsave', $project))
            ->assertRedirect();

        $this->assertDatabaseMissing('project_saves', [
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);
    }

    public function test_staff_can_upload_and_duplicate_pdfs_are_rejected(): void
    {
        Storage::fake('local');

        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $payload = $this->projectPayload($category, [$tag->id]);

        $this->actingAs($admin)
            ->post(route('admin.projects.store'), array_merge($payload, [
                'pdf_file' => UploadedFile::fake()->createWithContent('paper.pdf', '%PDF-1.4 project body'),
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'title' => 'Reliable Project Repository',
            'uploaded_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.projects.create'))
            ->post(route('admin.projects.store'), array_merge($payload, [
                'title' => 'Duplicate File Attempt',
                'pdf_file' => UploadedFile::fake()->createWithContent('copy.pdf', '%PDF-1.4 project body'),
            ]))
            ->assertSessionHasErrors('pdf_file');
    }

    public function test_staff_can_create_new_tags_inline_when_uploading(): void
    {
        Storage::fake('local');

        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $existingTag = Tag::factory()->create(['name' => 'Heritage', 'slug' => 'heritage']);

        $this->actingAs($admin)
            ->post(route('admin.projects.store'), array_merge($this->projectPayload($category, [$existingTag->id]), [
                'new_tags' => "Folklore, Maternal Health\nTextile Design",
                'pdf_file' => UploadedFile::fake()->createWithContent('culture.pdf', '%PDF-1.4 culture project'),
            ]))
            ->assertRedirect();

        $project = Project::where('title', 'Reliable Project Repository')->firstOrFail();

        $this->assertDatabaseHas('tags', ['slug' => 'folklore', 'name' => 'Folklore']);
        $this->assertDatabaseHas('tags', ['slug' => 'maternal-health', 'name' => 'Maternal Health']);
        $this->assertDatabaseHas('tags', ['slug' => 'textile-design', 'name' => 'Textile Design']);
        $this->assertTrue($project->tags()->where('slug', 'heritage')->exists());
        $this->assertTrue($project->tags()->where('slug', 'folklore')->exists());
    }

    public function test_roles_limit_admin_tools(): void
    {
        $normalUser = User::factory()->create(['role' => User::ROLE_USER]);
        $moderator = User::factory()->moderator()->create();
        $admin = User::factory()->admin()->create();
        $project = Project::factory()->create();

        $this->actingAs($normalUser)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($moderator)->get(route('admin.projects.create'))->assertOk();
        $this->actingAs($moderator)->get(route('admin.categories.index'))->assertForbidden();
        $this->actingAs($moderator)->delete(route('admin.projects.destroy', $project))->assertForbidden();

        $this->actingAs($admin)->delete(route('admin.projects.destroy', $project))->assertRedirect();
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_super_admin_is_the_top_user_level(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $secondSuperAdmin = User::factory()->create([
            'name' => 'Second Super Admin',
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        $admin = User::factory()->admin()->create();
        $moderator = User::factory()->moderator()->create();

        $this->actingAs($superAdmin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($secondSuperAdmin->email)
            ->assertSee($admin->email)
            ->assertSee($moderator->email);

        $this->actingAs($admin)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_registration_collects_research_preferences_and_opens_dashboard(): void
    {
        $category = Category::factory()->create(['name' => 'Health & Life Sciences']);

        $this->post(route('register'), [
            'name' => 'Nora Ade',
            'email' => 'nora@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'field_of_study' => 'Public Health',
            'interest_keywords' => 'maternal health, field survey',
            'preferred_categories' => [$category->id],
        ])->assertRedirect(route('dashboard'));

        $user = User::where('email', 'nora@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertSame('Public Health', $user->field_of_study);
        $this->assertSame('maternal health, field survey', $user->interest_keywords);
        $this->assertSame([$category->id], $user->preferredCategoryIds());
    }

    public function test_login_redirects_normal_users_to_research_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'reader@example.com',
            'role' => User::ROLE_USER,
        ]);

        $this->post(route('login'), [
            'email' => 'reader@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Research desk');
    }

    public function test_login_and_registration_can_return_to_the_project_being_read(): void
    {
        $project = Project::factory()->create();
        $projectUrl = route('projects.show', $project);
        $user = User::factory()->create([
            'email' => 'return-reader@example.com',
            'role' => User::ROLE_USER,
        ]);

        $this->get(route('login', ['redirect' => $projectUrl]))->assertOk();

        $this->post(route('login'), [
            'email' => 'return-reader@example.com',
            'password' => 'password',
            'redirect' => $projectUrl,
        ])->assertRedirect($projectUrl);

        auth()->logout();

        $this->post(route('register'), [
            'name' => 'Return Reader',
            'email' => 'return-register@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'redirect' => $projectUrl,
        ])->assertRedirect($projectUrl);
    }

    public function test_pdf_preview_and_download_routes_stream_files(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('projects/test.pdf', '%PDF-1.4 fake document');
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $project = Project::factory()->create([
            'pdf_file' => 'test.pdf',
            'pdf_path' => 'projects/test.pdf',
            'pdf_original_name' => 'test.pdf',
        ]);

        $this->get(route('projects.preview', $project))->assertRedirect(route('login'));
        $this->get(route('projects.download', $project))->assertRedirect(route('login'));

        $this->actingAs($user)->get(route('projects.preview', $project))->assertOk();
        $this->actingAs($user)->get(route('projects.download', $project))->assertOk();
    }

    public function test_uploads_can_auto_fill_missing_metadata_from_project_text(): void
    {
        Storage::fake('local');

        $user = User::factory()->create(['role' => User::ROLE_USER]);
        Category::factory()->create([
            'name' => 'Health & Life Sciences',
            'slug' => 'health-life-sciences',
        ]);
        $tag = Tag::factory()->create([
            'name' => 'Maternal Health',
            'slug' => 'maternal-health',
        ]);

        $this->actingAs($user)
            ->post(route('projects.store'), [
                'title' => 'Maternal Health Clinic Outreach',
                'student_name' => 'Ada Okafor',
                'supervisor' => null,
                'project_type' => 'Research',
                'category_id' => null,
                'abstract' => null,
                'keywords' => null,
                'completion_year' => 2025,
                'tags' => [],
                'pdf_file' => UploadedFile::fake()->createWithContent('health.pdf', '%PDF-1.4 maternal health clinic outreach'),
            ])
            ->assertRedirect();

        $project = Project::where('title', 'Maternal Health Clinic Outreach')->firstOrFail();

        $this->assertNotNull($project->category_id);
        $this->assertNotNull($project->abstract);
        $this->assertTrue($project->tags()->whereKey($tag->id)->exists());
    }

    public function test_upload_sanitizes_invalid_pdf_text_before_storage(): void
    {
        Storage::fake('local');

        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $invalidGlyphBytes = "\xED\xA0\xB5\xED\xB0\xB6";

        $this->actingAs($user)
            ->post(route('projects.store'), [
                'title' => 'Epoxy Resin Simulation',
                'student_name' => 'Peace Gideon',
                'supervisor' => 'Teddy',
                'project_type' => 'Case Study',
                'category_id' => null,
                'abstract' => null,
                'keywords' => null,
                'completion_year' => 2024,
                'tags' => [],
                'pdf_file' => UploadedFile::fake()->createWithContent(
                    'invalid-text.pdf',
                    "%PDF-1.4\n(SIMULATION {$invalidGlyphBytes} PROCESS) Tj"
                ),
            ])
            ->assertRedirect();

        $project = Project::where('title', 'Epoxy Resin Simulation')->firstOrFail();

        $this->assertTrue(mb_check_encoding($project->pdf_text, 'UTF-8'));
        $this->assertStringContainsString('SIMULATION', $project->pdf_text);
        $this->assertStringNotContainsString($invalidGlyphBytes, $project->pdf_text);
    }

    public function test_pdf_text_preview_restores_document_like_lines(): void
    {
        $project = Project::factory()->make([
            'pdf_text' => 'TABLE OF CONTENTS CERTIFICATION ........................................ ii DEDICATION ........................................ iii CHAPTER ONE ........................................ 1 INTRODUCTION ........................................ 1 1.1 Background of Study ........................................ 1 1.2 Aim and Objectives ........................................ 7 CHAPTER TWO ........................................ 10 LITERATURE REVIEW ........................................ 10 2.1 Epoxy Resin ........................................ 10',
        ]);

        $preview = $project->pdfTextPreview(1400, true);

        $this->assertStringContainsString("TABLE OF CONTENTS\nCERTIFICATION", $preview);
        $this->assertStringContainsString("\n1.1 Background of Study", $preview);
        $this->assertStringContainsString("\nCHAPTER TWO", $preview);
    }

    public function test_api_exposes_projects_and_stats(): void
    {
        Project::factory()->create([
            'title' => 'Mobile API Search Target',
            'pdf_text' => 'offline-first mobile repository sync',
        ]);

        $this->getJson('/api/projects?q=offline-first')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Mobile API Search Target']);

        $this->getJson('/api/stats')
            ->assertOk()
            ->assertJsonStructure(['data' => ['total_projects', 'projects_per_year', 'top_supervisors', 'project_types']]);
    }

    public function test_api_staff_can_create_projects(): void
    {
        Storage::fake('local');

        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;
        $category = Category::factory()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->post('/api/projects', array_merge($this->projectPayload($category), [
            'new_tags' => 'Public Policy, Field Survey',
            'pdf_file' => UploadedFile::fake()->createWithContent('api.pdf', '%PDF-1.4 api upload'),
        ]))
            ->assertCreated()
            ->assertJsonFragment(['title' => 'Reliable Project Repository']);

        $this->assertDatabaseHas('tags', ['slug' => 'public-policy']);
        $this->assertDatabaseHas('tags', ['slug' => 'field-survey']);
    }

    public function test_registration_captures_interests_and_dashboard_recommends_projects(): void
    {
        $category = Category::factory()->create(['name' => 'Health & Life Sciences']);
        Project::factory()->create([
            'category_id' => $category->id,
            'title' => 'Maternal Health Outreach Models',
            'keywords' => 'maternal health, public policy',
            'abstract' => 'Community care models for maternal health projects.',
        ]);

        $this->post(route('register'), [
            'name' => 'Ada Researcher',
            'email' => 'ada@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'field_of_study' => 'Public health',
            'interest_keywords' => 'maternal health, community care',
            'preferred_categories' => [$category->id],
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'ada@example.com',
            'field_of_study' => 'Public health',
        ]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Recommended projects')
            ->assertSee('Maternal Health Outreach Models');
    }

    public function test_search_results_can_be_ranked_by_relevance(): void
    {
        Project::factory()->create([
            'title' => 'Malaria Vaccine Research',
            'keywords' => 'immunology',
            'pdf_text' => 'clinical trial methods',
        ]);

        Project::factory()->create([
            'title' => 'General Health Archive',
            'keywords' => 'archive',
            'pdf_text' => 'malaria appears once in the appendix',
        ]);

        $this->get(route('projects.index', ['q' => 'malaria']))
            ->assertOk()
            ->assertSeeInOrder(['Malaria Vaccine Research', 'General Health Archive']);
    }

    private function projectPayload(Category $category, array $tags = []): array
    {
        return [
            'title' => 'Reliable Project Repository',
            'student_name' => 'Ada Okafor',
            'supervisor' => 'Dr. Stone',
            'project_type' => 'Research',
            'category_id' => $category->id,
            'abstract' => 'A useful repository for project PDFs.',
            'keywords' => 'repository, search, pdf',
            'completion_year' => 2025,
            'tags' => $tags,
        ];
    }
}
