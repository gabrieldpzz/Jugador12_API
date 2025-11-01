<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Mail\OtpMail;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Auth",
 *   description="Autenticación via Firebase + verificación por OTP correo"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/auth/send-otp",
     *   tags={"Auth"},
     *   summary="Enviar OTP al correo del usuario autenticado (Firebase ID Token)",
     *   description="Genera un código de 6 dígitos, lo guarda con expiración y lo envía por correo.",
     *   @OA\Response(response=200, description="Enviado", @OA\JsonContent(
     *     @OA\Property(property="sent", type="boolean", example=true),
     *     @OA\Property(property="expires_in", type="integer", example=600)
     *   )),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function sendOtp(Request $r)
    {
        $user = $r->attributes->get('auth_user');
        if (!$user) return response()->json(['error' => 'unauthenticated'], 401);

        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('email_otp_codes')->insert([
            'user_id'    => $user->id,
            'code_hash'  => Hash::make($code),
            'expires_at' => Carbon::now()->addMinutes(10),
            'attempts'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($user->email)->send(new OtpMail($code));

        return response()->json(['sent' => true, 'expires_in' => 600]);
    }

    /**
     * @OA\Post(
     *   path="/api/auth/verify-otp",
     *   tags={"Auth"},
     *   summary="Verificar OTP",
     *   description="Valida el código de 6 dígitos y marca el correo como verificado.",
     *   @OA\RequestBody(required=true, @OA\JsonContent(
     *     required={"code"},
     *     @OA\Property(property="code", type="string", example="123456")
     *   )),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(
     *     @OA\Property(property="verified", type="boolean", example=true)
     *   )),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=410, description="Expirado"),
     *   @OA\Response(response=422, description="Inválido")
     * )
     */
    public function verifyOtp(Request $r)
    {
        $user = $r->attributes->get('auth_user');
        if (!$user) return response()->json(['error' => 'unauthenticated'], 401);

        $code = (string) ($r->input('code'));
        if (strlen($code) !== 6) {
            return response()->json(['error' => 'invalid_code'], 422);
        }

        $row = DB::table('email_otp_codes')
            ->where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->orderByDesc('id')
            ->first();

        if (!$row) {
            return response()->json(['error' => 'invalid_code'], 422);
        }

        if (Carbon::parse($row->expires_at)->isPast()) {
            return response()->json(['error' => 'expired_code'], 410);
        }

        if (!Hash::check($code, $row->code_hash)) {
            DB::table('email_otp_codes')->where('id', $row->id)
              ->update(['attempts' => min(255, $row->attempts + 1)]);
            return response()->json(['error' => 'invalid_code'], 422);
        }

        DB::table('email_otp_codes')->where('id', $row->id)
          ->update(['consumed_at' => now(), 'updated_at' => now()]);

        if (is_null($user->email_verified_at)) {
            $user->email_verified_at = now();
            $user->save();
        }

        return response()->json(['verified' => true]);
    }
}
