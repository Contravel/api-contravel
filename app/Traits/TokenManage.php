<?php

namespace App\Traits;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Facades\Log;
use stdClass;

trait TokenManage
{
    public function generateToken($id, $user, $hash)
    {
        $respuesta = new \stdClass();
        try {
            $time = time();
            $expiration = $time + (60 * 60 * 6); // 6 hours expiration

            $payload = [
                'id' => $id, // Subject (user ID)
                'sub' => $user, // //User identifier
                'iat' => $time, // Issued at
                'exp' => $expiration, // Expiration time
                'status' => true, // Token status
                'uuid' => $hash, // User UUID
            ];
            Log::info($payload);
            $token =  JWT::encode($payload, $this->secret, 'HS256');
            $respuesta->status = true;
            $respuesta->token = $token;
            return $respuesta;
        } catch (\InvalidArgumentException $e) {
            $respuesta->status =  false;
            $respuesta->message = 'Error InvalidArgumentException: ' . $e->getMessage();
            return $respuesta;
        } catch (\UnexpectedValueException $e) {
            $respuesta->status =  false;
            $respuesta->message = 'Error UnexpectedValueException: ' . $e->getMessage();
            return $respuesta;
        } catch (\Exception $e) {
            $respuesta->status =  false;
            $respuesta->message = 'Error Exception: ' . $e->getMessage();
            return $respuesta;
        } catch (\Throwable $e) {
            $respuesta->status =  false;
            $respuesta->message = 'Error Throwable: ' . $e->getMessage();
            return $respuesta;
        }
    }

    public function validateToken(string $token)
    {
        $key = mb_convert_encoding(env('JWT_SECRET'), 'UTF-8');
        $headers = new stdClass();
        $headers->algorithm = 'HS256';
        $respuesta = new \stdClass();
        try {
            $decrypt = JWT::decode($token, new Key($key, 'HS256'), $headers);
            $respuesta->status = true;
            $respuesta->token = $decrypt;
            return $respuesta;
        } catch (ExpiredException $e) {
            $respuesta->status =  false;
            $respuesta->message = 'Error Token vencido: ' . $e->getMessage();
            return $respuesta;
        } catch (SignatureInvalidException $e) {
            $respuesta->status =  false;
            $respuesta->message = 'Error Token Invalido: ' . $e->getMessage();
            return $respuesta;
        } catch (\DomainException $e) {
            $respuesta->status =  false;
            $respuesta->message = 'Error Token Invalido: ' . $e->getMessage();
            return $respuesta;
        } catch (\Throwable $e) {
            $respuesta->status =  false;
            $respuesta->message = 'Error Inesperado: ' . $e->getMessage();
            return $respuesta;
        }
    }
}
