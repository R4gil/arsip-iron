<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class KlasifikasiImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_import_updates_classification_search_data(): void
    {
        $user = User::factory()->create(['role' => 'Admin']);

        $csv = "Kode,Keterangan\nPR,Perencanaan\nPR.01,Program dan Anggaran\n";
        $file = UploadedFile::fake()->createWithContent('klasifikasi.csv', $csv, 'text/csv');

        $this->actingAs($user)
            ->post(route('klasifikasi.import'), [
                'file_csv' => $file,
            ])
            ->assertRedirect(route('klasifikasi.index'));

        $this->assertDatabaseHas('klasifikasi', [
            'kode' => 'PR',
            'nama' => 'Perencanaan',
        ]);

        $response = $this->actingAs($user)->getJson(route('ajax.klasifikasi', ['q' => 'PR']));

        $response->assertOk();
        $response->assertJsonFragment([
            'kode' => 'PR',
            'nama' => 'Perencanaan',
        ]);
    }
}
