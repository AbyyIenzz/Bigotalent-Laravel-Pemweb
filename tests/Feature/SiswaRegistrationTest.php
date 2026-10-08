<?php

namespace Tests\Feature;

use App\Models\Lomba;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function siswa(): User
    {
        return User::factory()->create(['role' => 'siswa']);
    }

    private function lomba(): Lomba
    {
        return Lomba::create([
            'nama_lomba' => 'Web Design',
            'deskripsi' => 'Lomba desain web',
        ]);
    }

    public function test_siswa_can_register_for_a_lomba_once(): void
    {
        $siswa = $this->siswa();
        $lomba = $this->lomba();

        $this->actingAs($siswa)
            ->from(route('siswa.dashboard'))
            ->post(route('siswa.daftar'), [
                'lomba_id' => $lomba->id,
                'kelas' => 'X TKJ',
                'no_wa' => '081234567890',
            ])
            ->assertRedirect(route('siswa.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pendaftarans', [
            'user_id' => $siswa->id,
            'lomba_id' => $lomba->id,
            'kelas' => 'X TKJ',
            'no_wa' => '081234567890',
            'status' => 'Dokumen dalam Tinjauan',
        ]);
    }

    public function test_siswa_cannot_register_for_the_same_lomba_twice(): void
    {
        $siswa = $this->siswa();
        $lomba = $this->lomba();
        Pendaftaran::create([
            'user_id' => $siswa->id,
            'lomba_id' => $lomba->id,
            'kelas' => 'X TKJ',
            'no_wa' => '081234567890',
            'status' => 'Dokumen dalam Tinjauan',
        ]);

        $this->actingAs($siswa)
            ->from(route('siswa.dashboard'))
            ->post(route('siswa.daftar'), [
                'lomba_id' => $lomba->id,
                'kelas' => 'X TKJ',
                'no_wa' => '081234567890',
            ])
            ->assertRedirect(route('siswa.dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('pendaftarans', 1);
    }

    public function test_siswa_cannot_register_for_a_nonexistent_lomba(): void
    {
        $this->actingAs($this->siswa())
            ->from(route('siswa.dashboard'))
            ->post(route('siswa.daftar'), [
                'lomba_id' => 999,
                'kelas' => 'X TKJ',
                'no_wa' => '081234567890',
            ])
            ->assertRedirect(route('siswa.dashboard'))
            ->assertSessionHasErrors('lomba_id');

        $this->assertDatabaseCount('pendaftarans', 0);
    }

    public function test_registration_requires_valid_class_and_whatsapp_number(): void
    {
        $this->actingAs($this->siswa())
            ->from(route('siswa.dashboard'))
            ->post(route('siswa.daftar'), [
                'lomba_id' => $this->lomba()->id,
                'kelas' => '',
                'no_wa' => 'abc',
            ])
            ->assertRedirect(route('siswa.dashboard'))
            ->assertSessionHasErrors(['kelas', 'no_wa']);

        $this->assertDatabaseCount('pendaftarans', 0);
    }

    public function test_dashboard_displays_saved_class_and_whatsapp_details(): void
    {
        $siswa = $this->siswa();
        $lomba = $this->lomba();
        Pendaftaran::create([
            'user_id' => $siswa->id,
            'lomba_id' => $lomba->id,
            'kelas' => 'XI RPL',
            'no_wa' => '081234567890',
            'status' => 'Dokumen dalam Tinjauan',
        ]);

        $this->actingAs($siswa)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee('XI RPL')
            ->assertSee('081234567890');
    }

    public function test_dashboard_shows_only_the_current_siswa_registrations_in_latest_order(): void
    {
        $siswa = $this->siswa();
        $lomba = $this->lomba();
        $otherSiswa = $this->siswa();
        Pendaftaran::create([
            'user_id' => $siswa->id,
            'lomba_id' => $lomba->id,
            'status' => 'Final',
        ]);
        Pendaftaran::create([
            'user_id' => $otherSiswa->id,
            'lomba_id' => $lomba->id,
            'status' => 'Jadwal Briefing',
        ]);

        $this->actingAs($siswa)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Peserta: '.$siswa->name)
            ->assertSee('Web Design')
            ->assertSee('Lolos ke Final!')
            ->assertDontSee('Menunggu Undangan Briefing');
    }

    public function test_dashboard_shows_the_correct_badge_for_each_registration_status(): void
    {
        $siswa = $this->siswa();
        $statuses = [
            'Dokumen dalam Tinjauan' => ['Sedang Ditinjau Juri', 'bg-yellow-100'],
            'Jadwal Briefing' => ['Menunggu Undangan Briefing', 'bg-blue-100'],
            'Tahap Pelatihan' => ['Tahap Pelatihan', 'bg-purple-100'],
            'Final' => ['Lolos ke Final!', 'bg-green-100'],
        ];

        foreach ($statuses as $status => [$label, $badgeClass]) {
            $lomba = Lomba::create([
                'nama_lomba' => $status,
                'deskripsi' => null,
            ]);

            Pendaftaran::create([
                'user_id' => $siswa->id,
                'lomba_id' => $lomba->id,
                'status' => $status,
            ]);
        }

        $response = $this->actingAs($siswa)->get(route('siswa.dashboard'))->assertOk();

        foreach ($statuses as [$label, $badgeClass]) {
            $response->assertSee($label)->assertSee($badgeClass);
        }
    }
}
