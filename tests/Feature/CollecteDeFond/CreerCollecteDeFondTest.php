<?php

namespace Tests\Feature\CollecteDeFond;

use App\Http\Controllers\api\collecteDeFondController;
use App\Mail\CollecteEmail;
use App\Models\collecteDeFond;
use App\Models\Payment;
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


public function testModifierCollecteDeFond()
    {
        // Créez un utilisateur fondation
        $fondation = User::factory()->create();

        // Créez une collecte de fond associée à cet utilisateur
        $collecteDeFond = collecteDeFond::factory()->create(['user_id' => $fondation->id]);

        // Préparez les données de la requête pour la mise à jour
        $collecteUpdateData = [
            'titre' => 'Nouveau Titre',
            'description' => 'Nouvelle Description',
            'image' => UploadedFile::fake()->image('nouvelle_image.jpg'),
            'objectifFinancier' => 2000,
            'numeroCompte' => '987654321',
        ];

        // Authentifiez l'utilisateur fondation
        $this->actingAs($fondation, 'api');

        // Utilisez Storage::fake pour simuler le système de fichiers
        Storage::fake('public');

        // Exécutez la méthode update avec les données simulées
        $response = $this->post("/api/modifierCollecte/{$collecteDeFond->id}", $collecteUpdateData);

        // Assurez-vous que la réponse contient les éléments attendus
        $response->assertJsonStructure(['status', 'message', 'data']);
        $response->assertJson(['status' => true, 'message' => 'La Collecte de Fonds a été bien modifiée']);

        // Assurez-vous que la base de données a été mise à jour avec les nouvelles données
        $this->assertDatabaseHas('collecte_de_fonds', ['titre' => 'Nouveau Titre']);
    }


    public function testModifierProfil()
    {
        
        $utilisateur = User::factory()->create();

        // Authentifiez l'utilisateur
        $this->actingAs($utilisateur, 'api');

       
        $modificationProfilData = [
            'nom' => 'Nouveau Nom',
            'prenom' => 'Nouveau Prenom',
            'image' => UploadedFile::fake()->image('nouvelle_image.jpg'),
            'description' => 'Nouvelle Description',
            'numeroEnregistrement' => 'ABC123',
            'adresse' => 'Nouvelle Adresse',
            'email' => 'nouveau@mail.com',
            'password' => 'nouveau_mot_de_passe',
            'telephone' => '123456789',
        ];

       
        Storage::fake('public');

       
        $response = $this->post('/api/modifierProfil', $modificationProfilData);

        // Assurez-vous que la réponse contient les éléments attendus
        $response->assertJsonStructure(['status', 'message', 'data']);
        $response->assertJson(['status' => true, 'message' => 'Votre profil a été modifié avec succès']);

        // Assurez-vous que la base de données a été mise à jour avec les nouvelles données
        $this->assertDatabaseHas('users', ['nom' => 'Nouveau Nom', 'prenom' => 'Nouveau Prenom']);
    }


    public function testCloturerUneCollecte()
    {
        
        $fondation = User::factory()->create();

    
        $collecteDeFond = CollecteDeFond::factory()->create(['user_id' => $fondation->id]);

      
        $this->actingAs($fondation, 'api');

        // Exécutez la méthode cloturerUneCollecte
        $response = $this->put("/api/cloturerUneCollecte/{$collecteDeFond->id}");

        // Assurez-vous que la réponse contient les éléments attendus
        $response->assertJson(['status' => true, 'message' => 'La collecte de Fonds a été clôturé avec succès']);

        
        $this->assertDatabaseHas('collecte_de_fonds', ['id' => $collecteDeFond->id, 'statut' => 'cloturer']);
    }

    public function testDecloturerUneCollecte()
    {
      
        $fondation = User::factory()->create();

      
        $collecteDeFond = CollecteDeFond::factory()->create(['user_id' => $fondation->id, 'statut' => 'cloturer']);

       
        $this->actingAs($fondation, 'api');

      
        $response = $this->put("/api/decloturerUneCollecte/{$collecteDeFond->id}");

       
        $response->assertJson(['status' => true, 'message' => 'La collecte de Fonds a été declôturé avec succès']);

       
        $this->assertDatabaseHas('collecte_de_fonds', ['id' => $collecteDeFond->id, 'statut' => 'encours']);
    }

    public function testListeCollecteEnCours()
    {
      
        $fondation = User::factory()->create();

        // Créez plusieurs collectes de fond associées à cet utilisateur avec le statut "encours"
        $collecte1 = CollecteDeFond::factory()->create(['user_id' => $fondation->id, 'statut' => 'encours','description' => 'Description courte',]);
        $collecte2 = CollecteDeFond::factory()->create(['user_id' => $fondation->id, 'statut' => 'encours','description' => 'Description courte',]);

       
        $this->actingAs($fondation, 'api');

       
        $response = $this->get('/api/listeCollecteEnCours');

       
        $response->assertJson(['status' => true, 'message' => 'Liste de toutes les collectes de fonds en cours']);
        $response->assertJsonCount(2, 'data'); 
    }


    public function testListeCollecteCloturer()
    {
       
        $fondation = User::factory()->create();

        
        $collecte1 = CollecteDeFond::factory()->create(['user_id' => $fondation->id, 'statut' => 'cloturer']);
        $collecte2 = CollecteDeFond::factory()->create(['user_id' => $fondation->id, 'statut' => 'cloturer']);

      
        $this->actingAs($fondation, 'api');

       
        $response = $this->get('/api/listeCollecteCloturer');

       
        $response->assertJson(['status' => true, 'message' => 'Liste de toutes les collectes de fonds clôturer']);
        $response->assertJsonCount(2, 'data'); 
    }

    public function testSupprimerCollecte()
    {
       
        $fondation = User::factory()->create();

        
        $collecte = CollecteDeFond::factory()->create(['user_id' => $fondation->id]);

      
        $this->actingAs($fondation, 'api');

      
        $response = $this->delete("/api/supprimerCollecte/{$collecte->id}");

      
        $response->assertJson(['status' => true, 'message' => 'La Collecte de Fonds a été bien supprimée']);
        $response->assertJson(['data' => $collecte->toArray()]); 
        $this->assertDatabaseMissing('collecte_de_fonds', ['id' => $collecte->id]); 
    }


    public function testHistoriqueDesDons()
    {
        
        $donateur = User::factory()->create(['role' => 'donateur']);

      
        $collecte = CollecteDeFond::factory()->create(['user_id' => $donateur->id]);

       
        $don1 = Payment::factory()->create(['user_id' => $donateur->id, 'collecte_de_fond_id' => $collecte->id]);
        $don2 = Payment::factory()->create(['user_id' => $donateur->id, 'collecte_de_fond_id' => $collecte->id]);

       
        $this->actingAs($donateur, 'api');

      
        $response = $this->get('/api/historiqueDons');

      
        $response->assertJson(['status' => true, 'message' => "Voici l'historique de vos dons"]);
        $response->assertJsonCount(2, 'data'); 
    }


    public function testHistoriqueDon()
    {
      
        $donateur = User::factory()->create(['role' => 'donateur']);

      
        $collecte = CollecteDeFond::factory()->create(['user_id' => $donateur->id]);

       
        $don = Payment::factory()->create(['user_id' => $donateur->id, 'collecte_de_fond_id' => $collecte->id]);

       
        $this->actingAs($donateur, 'api');

      
        $response = $this->get("/api/historiqueDon/{$don->id}");

        
        $response->assertJson(['status' => true, 'message' => "Voici l'historique du don spécifié"]);
        $response->assertJsonCount(1, 'data');
    }


    public function testListeDonateurADesDons()
    {
        
        $fondation = User::factory()->create(['role' => 'fondation']);

       
        $collecte = CollecteDeFond::factory()->create(['user_id' => $fondation->id]);

       
        $donateur = User::factory()->create(['role' => 'donateur']);

     
        $don = Payment::factory()->create(['user_id' => $donateur->id, 'collecte_de_fond_id' => $collecte->id]);

        
        $this->actingAs($fondation, 'api');

       
        $response = $this->get('/api/listeDonateurADesDons');

       
        $response->assertJson(['status' => true, 'message' => 'Liste Collectes De Fonds  avec les donateurs associés']);
        $response->assertJsonCount(1, 'data'); 
        $response->assertJsonCount(1, 'data.0.Collecte.Dons'); 
    }

    
}