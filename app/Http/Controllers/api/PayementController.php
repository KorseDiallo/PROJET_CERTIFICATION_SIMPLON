<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\PaytechService;
use App\Http\Requests\PayementRequest;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use OpenApi\Annotations as OA;
use Illuminate\Support\Str;


/**
 *@OA\SecurityScheme(
 *securityScheme="bearerAuthss",
 *type="http",
 *scheme="bearer",
 *bearerFormat="JWT",
 *)
 */


class PayementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */

    public function index()
    {

        return view('index');
    }

    // 1 choix remplace la methode payement

 /**
 * @OA\Post(
 *      path="/api/faireUnDon",
 *      operationId="faireUnDon",
 *      tags={"Faire Un Don "},
 *      summary="Initie un paiement et gère le succès du paiement",
 *      description="Initie un paiement avec PayTech et gère le succès du paiement",
 *      security={{"bearerAuth":{}}},
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              @OA\Property(property="price", type="integer"),
 *              @OA\Property(property="collecte_id", type="integer"),
 *              
 *          )
 *      ),
 *     
 *      @OA\Response(
 *          response=200,
 *          description="Succès de l'initialisation du paiement ou redirection vers la page de succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="token", type="string"),
 *              @OA\Property(property="redirect_url", type="string"),
 *              @OA\Property(property="message", type="string", example="success")
 *          )
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Erreur lors de l'initialisation du paiement",
 *          @OA\JsonContent(
 *              @OA\Property(property="error", type="string")
 *          )
 *      )
 * )
 */



 public function initiatePayment(PayementRequest $request)
 {
     // Récupérez les informations nécessaires de la requête $request
     $amount = $request->input('price');
     $collecteId = $request->input('collecte_id');
     $userexist=DB::table('password_reset_tokens')->insert([
         'donateurConnecter' =>auth()->user()->id,
         'token'=>1
     ]);
      $nom= $request->input('nom');
      $email= $request->input('email');
      $telephone= $request->input('telephone');
  
     // Construisez l'URL de succès
     $success_url = secure_url(route('payment.success', ['code' => $collecteId, 'data' => $request->all()]));
 
     // Construisez l'URL d'annulation
     $cancel_url = secure_url(route('payment.index'));
 
     // Instanciez le service de paiement
     $paymentService = new PaytechService(config('paytech.PAYTECH_API_KEY'), config('paytech.PAYTECH_SECRET_KEY'));
 
     // Envoyez la requête de paiement
     $jsonResponse = $paymentService->setQuery([
         'item_name' => "Don pour Collecte de Fonds",
         'item_price' => $amount,
         'command_name' => "Paiement pour un don via PayTech",
     ])
     ->setCustomeField([
         'item_id' => $collecteId,
         'time_command' => time(),
         'ip_user' => $request->ip(),
         'lang' => $request->server('HTTP_ACCEPT_LANGUAGE')
     ])
     ->setTestMode(true)
     ->setCurrency("xof")
     ->setRefCommand(uniqid())
     ->setNotificationUrl([
         'ipn_url' => 'https://urltowebsite.com/ipn',  
         'success_url' => $success_url,
         'cancel_url' => $cancel_url
     ])->send();
 
     // Traitez la réponse et retournez une réponse appropriée à votre application
     if ($jsonResponse['success'] < 0) {
         return response()->json(['error' => $jsonResponse['errors'][0]], 422);
     } elseif ($jsonResponse['success'] == 1) {
        
         return response()->json(['token' => $jsonResponse['token'], 'redirect_url' => $jsonResponse['redirect_url']]);
     }
    
 }


 
    /**
 * @OA\Post(
 *      path="/api/faireUnDonAnonyme",
 *      operationId="faireUnDonAnonyme",
 *      tags={"Faire Un Don Par Un Donateur Anonyme "},
 *      summary="Initie un paiement et gère le succès du paiement",
 *      description="Initie un paiement avec PayTech et gère le succès du paiement",
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              @OA\Property(property="price", type="integer"),
 *              @OA\Property(property="collecte_id", type="integer"),
 *             
 *          )
 *      ),
 *     
 *      @OA\Response(
 *          response=200,
 *          description="Succès de l'initialisation du paiement ou redirection vers la page de succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="token", type="string"),
 *              @OA\Property(property="redirect_url", type="string"),
 *              @OA\Property(property="message", type="string", example="success")
 *          )
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Erreur lors de l'initialisation du paiement",
 *          @OA\JsonContent(
 *              @OA\Property(property="error", type="string")
 *          )
 *      )
 * )
 */


public function initiatePaymentAnonyme(PayementRequest $request)
{
    // Récupérez les informations nécessaires de la requête $request
    $amount = $request->input('price');
    $collecteId = $request->input('collecte_id');
    // $userexist=DB::table('password_reset_tokens')->insert([
    //     'donateurConnecter' =>auth()->user()->id,
    //     'token'=>1
    // ]);
     $nom= $request->input('nom');
     $email= $request->input('email');
     $telephone= $request->input('telephone');
 
    // Construisez l'URL de succès
    $success_url = secure_url(route('payment.success', ['code' => $collecteId, 'data' => $request->all()]));

    // Construisez l'URL d'annulation
    $cancel_url = secure_url(route('payment.index'));

    // Instanciez le service de paiement
    $paymentService = new PaytechService(config('paytech.PAYTECH_API_KEY'), config('paytech.PAYTECH_SECRET_KEY'));

    // Envoyez la requête de paiement
    $jsonResponse = $paymentService->setQuery([
        'item_name' => "Don pour Collecte de Fonds",
        'item_price' => $amount,
        'command_name' => "Paiement pour un don via PayTech",
    ])
    ->setCustomeField([
        'item_id' => $collecteId,
        'time_command' => time(),
        'ip_user' => $request->ip(),
        'lang' => $request->server('HTTP_ACCEPT_LANGUAGE')
    ])
    ->setTestMode(true)
    ->setCurrency("xof")
    ->setRefCommand(uniqid())
    ->setNotificationUrl([
        'ipn_url' => 'https://urltowebsite.com/ipn',  
        'success_url' => $success_url,
        'cancel_url' => $cancel_url
    ])->send();

    // Traitez la réponse et retournez une réponse appropriée à votre application
    if ($jsonResponse['success'] < 0) {
        return response()->json(['error' => $jsonResponse['errors'][0]], 422);
    } elseif ($jsonResponse['success'] == 1) {
       
        return response()->json(['token' => $jsonResponse['token'], 'redirect_url' => $jsonResponse['redirect_url']]);
    }
   
}
 

    public function success(Request $request, $code)
    {
        $validated = $_GET['data'];
        // $validated['token'] = session('token') ?? '';
        $validated['token'] = Str::random(156);

        // Call the save methods to save data to database using the Payment model

        $payment = $this->savePayment($validated);

        session()->forget('token');

        return Redirect::to(route('payment.success.view', ['code' => $code]));
    }






    public function savePayment($data = [])
    {
        
        // Récupérez les informations nécessaires du tableau $data
        $token = $data['token'];
        $amount = $data['price'];
        $collecteId = $data['collecte_id'];
    
        $id= DB::table('password_reset_tokens')->first();

       
        
            // Si l'utilisateur est connecté, enregistrez son ID, sinon null
         $userId = auth()->check() ? ($id ? $id->donateurConnecter : null) : null;

        // Utilisez le nom, l'email et le téléphone du donateur, seulement s'il est anonyme
        //  $nom = $userId ? null : $data['nom'];
        //  $email = $userId ? null : $data['email'];
        //  $telephone = $userId ? null : $data['telephone'];
        //   dd($userId);
        $payment = Payment::firstOrCreate([
             'token' => $token,
            
        ], [
            'amount' => $amount,
            'user_id' =>   $userId ?? null,
            'collecte_de_fond_id' => $collecteId,
            // 'nom'  => $nom,
            // 'email' => $email,
            // 'telephone' => $telephone,
        ]);
       
        DB::table('password_reset_tokens')->delete();
        
        if (!$payment) {
            // Redirection vers la page d'accueil si le paiement n'est pas enregistré
            return [
                'success' => false,
                'data' => $data
            ];
    
        }
    
        // Redirection vers la page de succès si le paiement est réussi
        $data['payment_id'] = $payment->id;
    
        return [
            'success' => true,
            'data' => $data
        ];
    }
    







    public function paymentSuccessView(Request $request, $code)
    {
        // You can fetch data from db if you want to return the data to views

        /* $record = Payment::where([
            ['token', '=', $code],
            ['user_id', '=', auth()->user()->id]
        ])->first(); */

        return 'success Félicitation, Votre paiement est éffectué avec succès';
    }

    public function cancel()
    {
        # code...
    }





    

}
