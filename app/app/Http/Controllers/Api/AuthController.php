<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/auth/send-otp",
     *   tags={"Auth"},
     *   summary="Enviar OTP por email",
     *   description="Envía un código OTP de 6 dígitos al correo. Si el correo ya está verificado en BD, no reenvía.",
     *   @OA\RequestBody(
     *     required=true,
     *     content={
     *       @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *           type="object",
     *           required={"email"},
     *           @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *       )
     *     }
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     content={
     *       @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(property="ok", type="boolean", example=true),
     *           @OA\Property(property="message", type="string", example="OTP enviado"),
     *           @OA\Property(property="already_verified", type="boolean", nullable=true, example=true)
     *         )
     *       )
     *     }
     *   ),
     *   @OA\Response(response=422, description="Validación fallida"),
     *   @OA\Response(response=429, description="Cooldown (solicitudes muy seguidas)"),
     *   @OA\Response(response=500, description="Error al enviar correo")
     * )
     */
    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower($data['email']);

        // Si ya está verificado en BD, no reenviar OTP
        $user = User::where('email', $email)->first();
        if ($user && $user->email_verified_at) {
            return response()->json([
                'ok' => true,
                'already_verified' => true,
                'message' => 'El correo ya está verificado.'
            ], 200);
        }

        // Anti-abuso: cooldown simple (60s)
        $cooldownKey = "otp:cooldown:$email";
        if (Cache::has($cooldownKey)) {
            return response()->json([
                'ok' => false,
                'message' => 'Espera un minuto antes de solicitar otro código.'
            ], 429);
        }

        // Generar OTP de 6 dígitos
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Guardar OTP 10 min
        Cache::put("otp:code:$email", $code, now()->addMinutes(10));
        // Cooldown 60s
        Cache::put($cooldownKey, true, now()->addSeconds(60));

        // Enviar correo
        try {
            Mail::raw(
                "Tu código de verificación es: $code\nVence en 10 minutos.",
                function ($m) use ($email) {
                    $m->to($email)->subject('Tu código Jugador12');
                }
            );
        } catch (\Throwable $e) {
            Log::error('Error enviando OTP', ['email' => $email, 'e' => $e->getMessage()]);
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo enviar el OTP. Revisa la configuración SMTP.'
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'message' => 'OTP enviado'
        ], 200);
    }

    /**
     * @OA\Post(
     *   path="/api/auth/verify-otp",
     *   tags={"Auth"},
     *   summary="Verificar OTP",
     *   description="Valida el código OTP y marca email verificado. Si no existe usuario, lo crea ya verificado.",
     *   @OA\RequestBody(
     *     required=true,
     *     content={
     *       @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *           type="object",
     *           required={"email","code"},
     *           @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *           @OA\Property(property="code",  type="string", pattern="^\d{6}$", example="123456"),
     *           @OA\Property(property="name",  type="string", example="Juan Pérez", nullable=true, description="Opcional: si no existe el usuario, se usará este nombre")
     *         )
     *       )
     *     }
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Verificado",
     *     content={
     *       @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(property="ok", type="boolean", example=true),
     *           @OA\Property(property="verified", type="boolean", example=true),
     *           @OA\Property(property="created_user", type="boolean", example=false)
     *         )
     *       )
     *     }
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Código incorrecto o vencido",
     *     content={
     *       @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(property="ok", type="boolean", example=false),
     *           @OA\Property(property="verified", type="boolean", example=false),
     *           @OA\Property(property="message", type="string", example="Código incorrecto o vencido.")
     *         )
     *       )
     *     }
     *   ),
     *   @OA\Response(response=422, description="Validación fallida")
     * )
     */
    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'code'  => ['required', 'digits:6'],
            'name'  => ['nullable', 'string', 'max:255'],
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

        $user = User::where('email', $email)->first();
        $created = false;

        if ($user) {
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
                $user->save();
            }
        } else {
            // Crear usuario verificado
            $user = new User();
            $user->name  = $data['name'] ?? '';
            $user->email = $email;
            // Password aleatorio (por si luego migras a login con contraseña)
            $user->password = Hash::make(bin2hex(random_bytes(10)));
            $user->email_verified_at = now();
            $user->save();
            $created = true;
        }

        // Consumir OTP y limpiar flags
        Cache::forget("otp:code:$email");
        Cache::forget("otp:preverified:$email"); // por si lo usaste antes

        return response()->json([
            'ok' => true,
            'verified' => true,
            'created_user' => $created,
        ], 200);
    }
}
