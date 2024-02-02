<?php

namespace Tests\Feature\CollecteDeFond;

use App\Http\Controllers\api\collecteDeFondController;
use App\Mail\CollecteEmail;
use App\Models\User;
//use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
//use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreerCollecteDeFondTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use WithFaker;
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }


    

    public function testCreerCollecteDeFond()
    {
        $fondation = User::factory()->create();
    
        $collecteRequestData = [
            'titre' => 'Ma Collecte',
            'description' => 'Description de ma collecte',
            'image' => UploadedFile::fake()->image('collecte_image.jpg'),
            'objectifFinancier' => 1000,
            'numeroCompte' => '123456789',
        ];
    
        $this->actingAs($fondation, 'api');
    
       // Utilisez Storage::fake pour simuler le système de fichiers
        Storage::fake('public');

   

    // Exécutez la méthode store avec les données simulées
    $response = $this->post('/api/creerCollecte', $collecteRequestData);

    $response->assertJsonStructure(['status', 'message', 'data']);

    $response->assertJson(['status' => true, 'message' => 'La Collecte de Fonds a été bien crée']);

    $this->assertDatabaseHas('collecte_de_fonds', ['titre' => 'Ma Collecte']);

   
}

}