<?php

namespace Tests\Feature\User;

use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    
    public function testInscription()
    {
       
        $userData = [
            'nom' => 'John',
            'prenom' => 'Doe',
            'image' => UploadedFile::fake()->image('user_image.jpg'),
            'description' => 'Description de l\'utilisateur',
            'numeroEnregistrement' => '123456',
            'adresse' => '123 Rue Test',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'telephone' => '123456789',
            'role' => 'donateur',
        ];

       
        $response = $this->post('/api/register', $userData);

     
        $response->assertStatus(200);
        $response->assertJson(['status' => true, 'message' => 'Inscription effectuée avec succès']);

        // Vérifiez que l'utilisateur a été correctement enregistré dans la base de données
        $this->assertDatabaseHas('users', [
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'email' => $userData['email'],
            'telephone' => $userData['telephone'],
            'role' => $userData['role'],
            'statut' => $userData['role'] == 'admin' || $userData['role'] == 'donateur' ? 'accepte' : 'enattente',
        ]);
    }


    public function testConnexion()
    {
       
        $user = User::factory()->create();
    
     
        $loginData = [
            'email' => $user->email,
            'password' => 'password', 
        ];
    
        $response = $this->json('POST', '/api/login', $loginData);
    
       
        $response->assertStatus(200);
    
        
        $response->assertJsonStructure(['status', 'message', 'token', 'role', 'datas']);
    
       
        $this->assertNotEmpty($response->json('token'));
    
      
        $this->assertEquals('donateur', $response->json('role'));
    }

    public function testApprouverDemande()
    {
       
        $user = User::factory()->create(['statut' => 'enattente']);

        $this->actingAs($user, 'api');
        
        $response = $this->json('Post', "/api/approuver/{$user->id}");

      
        $response->assertStatus(200);

        // Rechargez l'utilisateur depuis la base de données
        $user = $user->fresh();

        // Assurez-vous que le statut de l'utilisateur est maintenant 'accepte'
        $this->assertEquals('accepte', $user->statut);

        // Assurez-vous que la réponse JSON a les propriétés attendues
        $response->assertJson([
            'status' => true,
            'message' => 'Demande approuvée avec succès',
        ]);
    }


    public function testRefuserDemande()
{
   
    $user = User::factory()->create(['statut' => 'enattente']);

    
    $this->actingAs($user, 'api');

  
    $response = $this->json('POST', "/api/refuserDemande/{$user->id}");

   
    $response->assertStatus(200);

    
    $user = $user->fresh();

    
    $this->assertEquals('refuse', $user->statut);

    
    $response->assertJson([
        'status' => true,
        'message' => 'Demande refuser avec succès',
    ]);
}

public function testBloquer()
{
   
    $user = User::factory()->create();

    
    $this->actingAs($user, 'api');

   
    $response = $this->json('POST', "/api/bloquer/{$user->id}");

   
    $response->assertStatus(200);

  
    $user = $user->fresh();

   
    // $this->assertTrue($user->bloque);

   
    $response->assertJson([
        'status' => true,
        "message" => "l'utilisateur a été bloqué  avec succès"
    ]);
}


public function testDebloquer()
{
    
    $user = User::factory()->create(['bloque' => true]);

   
    $this->actingAs($user, 'api');

 
    $response = $this->json('POST', "/api/debloquer/{$user->id}");

   
    $response->assertStatus(200);

    
    $user = $user->fresh();

    
    // $this->assertFalse($user->bloque);

   
    $response->assertJson([
        'status' => true,
        'message' => "l'utilisateur a été debloqué  avec succès"
    ]);
}


public function testLogout()
{
  
    $user = User::factory()->create();

    // Génère un token JWT pour l'utilisateur
    $token = JWTAuth::fromUser($user);

    
    $this->withHeader('Authorization', 'Bearer ' . $token);

    
    
    $this->actingAs($user, 'api');
   
    $response = $this->json('GET', '/api/logoutAdmin');

   
    $response->assertStatus(200)
        ->assertJson([
            'message' => $response->json('message'),
        ]);

    // Vérifiez si l'utilisateur est bien déconnecté
    $this->assertGuest('api');
}


public function testListeDonateur()
{
    

    $user = User::factory()->create(['role' => 'admin']);

    $token = JWTAuth::fromUser($user);
    $this->withHeader('Authorization', 'Bearer ' . $token);
    
    $this->actingAs($user);
    $response = $this->json('GET', '/api/listeDonateur');
   
    $response->assertStatus(200);

    $responseData = $response->json();

    
    $this->assertTrue($responseData['status']);

    // Vérifiez que le champ "message" est correct
    $this->assertEquals("Liste de tous les donateurs", $responseData['message']);

    // Vérifiez que le champ "data" est un tableau non vide
    $this->assertNotEmpty($responseData['data']);

    // Vérifiez que chaque élément du tableau "data" a le rôle 'donateur'
    foreach ($responseData['data'] as $donateur) {
        $this->assertEquals('donateur', $donateur['role']);
    }
}

}
