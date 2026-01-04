<?php

namespace Tests\Feature\Principal;

use App\Models\User;
use App\Models\Message;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CommunicationTest extends TestCase
{
    use RefreshDatabase;

    protected $principal;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create Principal Role if not exists
        if (!Role::where('name', 'principal')->exists()) {
            Role::create(['name' => 'principal']);
        }

        $this->principal = User::factory()->create();
        $this->principal->assignRole('principal');
    }

    public function test_principal_can_view_inbox()
    {
        $response = $this->actingAs($this->principal)
                         ->get(route('principal.communication.index'));

        $response->assertStatus(200);
        $response->assertViewIs('principal.communication.index');
    }

    public function test_principal_can_send_message_with_attachment()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $data = [
            'subject' => 'Test Subject',
            'body' => 'Test Body',
            'classification' => 'General Notice',
            'channels' => ['system'],
            'attachments' => [$file],
        ];

        $response = $this->actingAs($this->principal)
                         ->post(route('principal.communication.store'), $data);

        $response->assertRedirect(route('principal.communication.sent'));
        
        $this->assertDatabaseHas('messages', [
            'subject' => 'Test Subject',
            'sender_id' => $this->principal->id,
        ]);

        $message = Message::where('subject', 'Test Subject')->first();
        $this->assertCount(1, $message->attachments()->get());
    }

    public function test_principal_can_create_announcement()
    {
        $data = [
            'title' => 'New Announcement',
            'content' => 'Announcement Content',
            'category' => 'Academic',
            'priority' => 'normal',
            'audience' => ['all'],
            'start_date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->principal)
                         ->post(route('principal.communication.announcements.store'), $data);

        $response->assertRedirect(route('principal.communication.announcements'));

        $this->assertDatabaseHas('announcements', [
            'title' => 'New Announcement',
            'created_by' => $this->principal->id,
        ]);
    }

    public function test_principal_can_view_delivery_report()
    {
        $message = Message::create([
            'sender_id' => $this->principal->id,
            'subject' => 'Report Test',
            'body' => 'Body',
            'classification' => 'General',
            'channels' => ['system'],
        ]);
        
        // Add a recipient
        $recipient = User::factory()->create();
        $message->recipients()->create(['recipient_id' => $recipient->id]);

        $response = $this->actingAs($this->principal)
                         ->get(route('principal.communication.report', ['id' => $message->id, 'type' => 'message']));

        $response->assertStatus(200);
        $response->assertViewIs('principal.communication.report');
    }

    public function test_principal_can_view_announcement_report()
    {
        $announcement = Announcement::create([
            'title' => 'Report Test',
            'content' => 'Content',
            'category' => 'General',
            'created_by' => $this->principal->id,
        ]);

        $response = $this->actingAs($this->principal)
                         ->get(route('principal.communication.report', ['id' => $announcement->id, 'type' => 'announcement']));

        $response->assertStatus(200);
        $response->assertViewIs('principal.communication.report');
    }
}
