<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * POST /api/auth/send-otp
     * body: { "email": "user@dominio.com" }
     * Envía un código OTP de 6 dígitos al correo. No requiere estar autenticado.
     */
    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email','max:255'],
        ]);

        $email = strtolower($data['email']);

        // Anti-abuso simple: 1 código por minuto.
        $cooldownKey = "otp:cooldown:$email";
        if (Cache::has($cooldownKey)) {
            return response()->json([
                'ok' => false,
                'message' => 'Espera un minuto antes de solicitar otro código.',
            ], 429);
        }

        // Generar OTP de 6 dígitos
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Guardar OTP 10 min
        Cache::put("otp:code:$email", $code, now()->addMinutes(10));
        // cooldown 60s
        Cache::put($cooldownKey, true, now()->addSeconds(60));

        // Enviar correo
        try {
            Mail::raw(
                "Tu código de verificación es: $code\nVence en 10 minutos.",
                function ($m) use ($email) {
                    $m->to($email)
                      ->subject('Tu código Jugador12');
                }
            );
        } catch (\Throwable $e) {
            Log::error('Error enviando OTP', ['email' => $email, 'e' => $e->getMessage()]);
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo enviar el OTP. Revisa SMTP.',
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'message' => 'OTP enviado',
        ]);
    }

    /**
     * POST /api/auth/verify-otp
     * body: { "email": "user@dominio.com", "code": "123456" }
     * Verifica el código. Si coincide, marca verificado 15 min.
     */
    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email','max:255'],
            'code'  => ['required','digits:6'],
        ]);

        $email = strtolower($data['email']);
        $code  = $data['code'];

        $saved = Cache::get("otp:code:$email");
        if (!$saved || $saved !== $code) {
            return response()->json([
                'ok' => false,
                'verified' => false,
                'message' => 'Código incorrecto o vencido.',
            ], 400);
        }

        // Marcar verificado por 15 min
        Cache::put("otp:verified:$email", true, now()->addMinutes(15));
        // Consumir OTP
        Cache::forget("otp:code:$email");

        return response()->json([
            'ok' => true,
            'verified' => true,
        ]);
    }
}
