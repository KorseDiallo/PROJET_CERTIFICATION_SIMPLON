<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\PaytechService;
use App\Http\Requests\PayementRequest;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


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

    public function payment(PayementRequest $request)
    {
        // dd('ok');
        // Définir l'URL IPN
        $IPN_URL = 'https://urltowebsite.com';
    
        // Définir le montant
        $amount = $request->validated()['price'];
    
        // Code de produit
        $code =$request->validated()['collecte_id'] ; // Cela peut être l'ID du produit
        // dd( $request->validated());
    
      
        // URL de succès
        $success_url = secure_url(route('payment.success', ['code' => $code, 'data' => $request->validated()]));
        // dd('ok');
        // URL d'annulation
        $cancel_url = secure_url(route('payment.index'));


    
        // Instancier le service de paiement
        $paymentService = new PaytechService(config('paytech.PAYTECH_API_KEY'), config('paytech.PAYTECH_SECRET_KEY'));
    
        // Envoyer la requête de paiement
        $jsonResponse = $paymentService->setQuery([
            'item_name' => "Don pour Collecte de Fonds",
            'item_price' => $amount,
            'command_name' => "Paiement pour un don via PayTech",
        ])
            ->setCustomeField([
                'item_id' => $request->validated()['collecte_id'],
                'time_command' => time(),
                'ip_user' => $_SERVER['REMOTE_ADDR'],
                'lang' => $_SERVER['HTTP_ACCEPT_LANGUAGE']
            ])
            ->setTestMode(true)
            ->setCurrency("xof")
            ->setRefCommand(uniqid())
            ->setNotificationUrl([
                'ipn_url' => $IPN_URL . '/ipn',
                'success_url' => $success_url,
                'cancel_url' => $cancel_url
            ])->send();

            // dd($jsonResponse);
    
        // Vérifier la réponse
        if ($jsonResponse['success'] < 0) {
            return back()->withErrors($jsonResponse['errors'][0]);
        } elseif ($jsonResponse['success'] == 1) {
            // Rediriger vers le site Paytech pour finaliser le paiement
            $token = $jsonResponse['token'];
            session(['token' => $token]);
            return Redirect::to($jsonResponse['redirect_url']);
        }
    }

    public function success(Request $request, $code)
    {
        $validated = $_GET['data'];
        $validated['token'] = session('token') ?? '';

        // Call the save methods to save data to database using the Payment model

        $payment = $this->savePayment($validated);

        session()->forget('token');

        return Redirect::to(route('payment.success.view', ['code' => $code]));
    }

    public function savePayment($data = [])
{
    // dd(Auth()->user()->id);
    # Sauvegarde du paiement dans la base de données
    $payment = payment::firstOrCreate([
        'token' => $data['token'],
    ], [
        'amount' => $data['price'],
        'user_id' => 2,//auth()->user()->id, 
        'collecte_de_fond_id' => $data['collecte_id'], 
    ]);

    if (!$payment) {
        # Redirection vers la page d'accueil si le paiement n'est pas enregistré
        return $response = [
            'success' => false,
            'data' => $data
        ];
    }

    # Redirection vers la page de succès si le paiement est réussi
    $data['payment_id'] = $payment->id;


    return $response = [
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
