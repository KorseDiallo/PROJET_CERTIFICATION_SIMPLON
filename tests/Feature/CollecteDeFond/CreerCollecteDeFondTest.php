<?php

namespace Tests\Feature\CollecteDeFond;

use App\Models\User;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreerCollecteDeFondTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

//     public function testCreerCollecteDeFond()
//     {
       
//     $user = User::create([
//         'nom' => 'Fondation Test',
//         // 'prenom' => 'Prenom Test',
//         'image' => 'chemin_image.jpg', 
//         'description' => 'Description Test',
//         'numeroEnregistrement' => '123456',
//         'adresse' => 'Adresse Test',
//         'email' => 'test@example.com',
//         'password' => bcrypt('password123'),
//         'telephone' => '123456789',
//         'role' => 'fondation', 
//         'statut' => 'accepte', 
//         'bloque' => false,
//         'is_deleted' => false,
//     ]);

//     $this->actingAs($user);

   
//     $requestData = [
//         'titre' => 'Titre de la collecte',
//         'description' => 'Description de la collecte',
//         // 'image' => UploadedFile::fake()->image('test_image.jpg'),
//         'image' => UploadedFile::image('test_image.jpg'),
//         'objectifFinancier' => 1000,
//         'numeroCompte' => '123456789',
//     ];

   
//     $response = $this->post('/votre-url-de-la-route-store', $requestData);

   
//     $response->assertJson([
//         'status' => true,
//         'message' => 'La Collecte de Fonds a été bien créée',
//         'data' => [
//             'titre' => $requestData['titre'],
//             'description' => $requestData['description'],
           
//         ],
//     ]);

   
//     $this->assertDatabaseHas('collecte_de_fonds', [
//         'titre' => $requestData['titre'],
//         'description' => $requestData['description'],
       
//     ]);

   
//     $imageFile = UploadedFile::image('test_image.jpg');

   
//     $storedImagePath = $this->storeImage($imageFile);

   
//     Storage::disk('public')->assertExists($storedImagePath);
// }

// private function storeImage($image)
// {
//     return $image->store('imagesCollecte', 'public');
// }

}

