<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminStudentManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_dashboard_lists_student_accounts(): void
    {
        $admin = $this->admin();
        $student = User::factory()->create([
            'name' => 'Siswa Test',
            'role' => 'siswa',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.siswa'))
            ->assertOk()
            ->assertSee('Siswa Test')
            ->assertSee($student->email);
    }

    public function test_admin_can_create_a_student_account(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.siswa.store'), [
                'name' => 'Siswa Baru',
                'email' => 'siswa.baru@example.com',
                'password' => 'password123',
                'role' => 'admin',
            ])
            ->assertRedirect();

        $student = User::where('email', 'siswa.baru@example.com')->firstOrFail();

        $this->assertSame('siswa', $student->role);
        $this->assertTrue(Hash::check('password123', $student->password));
    }

    public function test_admin_can_edit_student_details_and_optionally_change_password(): void
    {
        $admin = $this->admin();
        $student = User::factory()->create(['role' => 'siswa']);
        $originalPassword = $student->password;

        $this->actingAs($admin)
            ->get(route('admin.siswa.edit', $student))
            ->assertOk()
            ->assertSee($student->name);

        $this->actingAs($admin)
            ->put(route('admin.siswa.update', $student), [
                'name' => 'Nama Diperbarui',
                'email' => 'updated.student@example.com',
                'password' => '',
            ])
            ->assertRedirect(route('admin.siswa'));

        $student->refresh();
        $this->assertSame('Nama Diperbarui', $student->name);
        $this->assertSame('updated.student@example.com', $student->email);
        $this->assertSame($originalPassword, $student->password);
    }

    public function test_admin_can_delete_a_student_but_cannot_edit_or_delete_an_admin_as_a_student(): void
    {
        $admin = $this->admin();
        $student = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($admin)
            ->delete(route('admin.siswa.destroy', $student))
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $student->id]);

        $this->actingAs($admin)
            ->get(route('admin.siswa.edit', $admin))
            ->assertNotFound();

        $this->actingAs($admin)
            ->delete(route('admin.siswa.destroy', $admin))
            ->assertNotFound();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_create_edit_and_delete_lomba(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.lomba'))
            ->assertOk()
            ->assertSee('Tambah Mata Lomba');

        $this->actingAs($admin)
            ->post(route('admin.lomba.store'), [
                'nama_lomba' => 'Web Design',
                'deskripsi' => 'Desain website',
            ])
            ->assertRedirect();

        $lomba = \App\Models\Lomba::where('nama_lomba', 'Web Design')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.lomba.edit', $lomba))
            ->assertOk()
            ->assertSee('Web Design');

        $this->actingAs($admin)
            ->put(route('admin.lomba.update', $lomba), [
                'nama_lomba' => 'UI Design',
                'deskripsi' => 'Desain antarmuka',
            ])
            ->assertRedirect(route('admin.lomba'));

        $this->assertDatabaseHas('lombas', [
            'id' => $lomba->id,
            'nama_lomba' => 'UI Design',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.lomba.destroy', $lomba))
            ->assertRedirect();

        $this->assertDatabaseMissing('lombas', ['id' => $lomba->id]);
    }

    public function test_admin_navigation_links_to_separate_management_pages(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Kelola Lomba')
            ->assertSee('Kelola Siswa')
            ->assertSee('Manajemen Status');

        $student = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($student)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertDontSee('Kelola Lomba')
            ->assertDontSee('Manajemen Status');
    }
}
