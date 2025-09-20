<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function handlePayment(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('student.deposit')->with('error', 'No reference provided.');
        }

        // Check if user already has an active membership
        $existingMembership = Membership::where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if ($existingMembership) {
            return redirect()->route('student.deposit')->with('error', 'You already have an active membership.');
        }

        $response = Http::withToken(env('PAYSTACK_SECRET_KEY'))
            ->get(env('PAYSTACK_PAYMENT_URL') . '/transaction/verify/' . $reference);

        $result = $response->json();

        if ($result['status'] && $result['data']['status'] === 'success') {
            $amountInNaira = $result['data']['amount'] / 100; // Convert from kobo to Naira
            
            // Calculate months based on payment amount
            if ($amountInNaira < 20000) {
                return redirect()->route('student.deposit')->with('error', 'Payment amount is insufficient.');
            }

            $months = 1;
            if ($amountInNaira > 20000) {
                $months = floor(($amountInNaira - 20000) / 10000) + 1;
            }

            if ($months > 12) {
                return redirect()->route('student.deposit')->with('error', 'Maximum membership duration is 12 months.');
            }

            // Create payment record
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'amount' => $amountInNaira,
                'status' => 'paid',
                'reference' => $reference,
                'payment_date' => now(),
            ]);

            // Create membership with duration based on payment amount
            Membership::create([
                'user_id' => Auth::id(),
                'start_date' => now(),
                'end_date' => now()->addMonths($months),
                'status' => 'active'
            ]);

            return redirect()->route('student.dashboard')
                ->with('success', "Payment successful! Your {$months}-month membership is now active.");
        }

        // Log failed payment
        Payment::create([
            'user_id' => Auth::id(),
            'amount' => $result['data']['amount'] / 100,
            'status' => 'failed',
            'reference' => $reference,
            'payment_date' => now(),
        ]);

        return redirect()->route('student.deposit')->with('error', 'Payment failed or could not be verified.');
    }
}
