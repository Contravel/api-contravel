<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LoginController;
use App\Models\admon_op\breakdownHotels;
use App\Models\admon_op\catalogSupplierHotels;
use App\Models\admon_op\commentReservations;
use App\Models\admon_op\confirmedReservations;
use App\Models\hoteles\bookings;
use App\Models\hoteles\cancelledBooking;
use App\Models\HotelesDB\Canceladas;
use App\Models\HotelesDB\Confirmadas;
use App\Models\HotelesDB\Observaciones;
use App\Models\HotelesDB\Proveedores;
use App\Models\HotelesDB\HotelesReservados;
use App\Models\HotelesDB\Reservacion;
use Carbon\Carbon;
use Dom\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Validation\ValidationException;

class PostHoteles extends ApiController
{
    public function postConsult(Request $request)
    {
        Log::info('ConsultReser: inicio del método', [
            'request' => $request->all()
        ]);

        try {

            Log::info('ConsultReser: validando request');

            $validateData = $request->validate([
                'Clave' => 'required|string',
            ]);

            Log::info('ConsultReser: request validado', [
                'Clave' => $validateData['Clave']
            ]);

            $num_Reserva = $validateData['Clave'];

            Log::info('ConsultReser: ejecutando consulta', [
                'busqueda' => $num_Reserva
            ]);

            $reservaciones = bookings::with([
                'canceladas',
                'confirmadas',
                'observaciones'
            ])
                ->where('CVE_RESERVACION', 'LIKE', "%{$num_Reserva}%")
                ->get();

            Log::info('ConsultReser: consulta ejecutada', [
                'resultados' => $reservaciones->count()
            ]);

            if ($reservaciones->isEmpty()) {

                Log::warning('ConsultReser: no se encontraron resultados', [
                    'clave' => $num_Reserva
                ]);

                return response()->json([
                    'message' => 'No se encontró ninguna reservación similar'
                ], 404);
            }

            Log::info('ConsultReser: respuesta enviada correctamente');

            return response()->json($reservaciones, 200);
        } catch (\Throwable $e) {

            Log::error('ConsultReser: error detectado', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }
    public function postReserva(Request $request)
    {
        $response = null;
        $status   = 200;

        try {
            $validateData = $request->validate([
                'Clave' => 'required',
            ]);

            $num_Reserva = $validateData['Clave'];

            $reservaciones = bookings::with(['plataforma', 'huesped', 'pagos'])
                ->where('CVE_RESERVACION', '=', $num_Reserva)
                ->get();

            if ($reservaciones->isEmpty()) {
                $response = [
                    'message' => 'No se encontró ninguna reservación con la clave proporcionada',
                    'success' => false,
                ];
                $status = 404;
            } else {
                $existeEnDesgloses = breakdownHotels::where('CVE_RESERVACION', '=', $num_Reserva)->exists();

                if ($existeEnDesgloses) {
                    $response = [
                        'message' => 'La reservación ya existe en la tabla de desgloses',
                        'success' => false,
                    ];
                    $status = 409;
                } else {
                    $response = $reservaciones;
                }
            }
        } catch (ValidationException $e) {
            $response = ['error' => $e->getMessage()];
            $status = 422;
        } catch (\Exception $e) {
            $response = ['error' => 'Error al consultar los datos: ' . $e->getMessage()];
            $status = 500;
        }

        return response()->json($response, $status);
    }


    public function postOBS(Request $request)
    {
        try {
            // Validar los campos requeridos
            $validateData = $request->validate([
                'ClaveReserva' => 'required',
                'Obs'          => 'required',
            ]);

            // Extraer los datos validados
            $claveReserva = $validateData['ClaveReserva'];
            $obs          = $validateData['Obs'];

            // Verificar si ya existe una reserva con la clave proporcionada
            $observacion = commentReservations::where('CVE_RESERVACION', $claveReserva)->first();

            if ($observacion) {
                // Si la reserva ya existe, actualiza la observación
                $observacion->OBSERVACIONES = $obs;
                $observacion->save();
                $message = 'Observación actualizada correctamente';
            } else {
                // Si no existe, crea una nueva reserva con observaciones
                $observacion = commentReservations::create([
                    'CVE_RESERVACION' => $claveReserva,
                    'OBSERVACIONES'   => $obs,
                ]);
                $message = 'Observación creada correctamente';
            }

            // Retornar el éxito con los datos insertados o actualizados
            return response()->json([
                'message' => $message,
                'data'    => $observacion,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar errores de validación
            return response()->json([
                'error' => $e->validator->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Manejar errores generales
            return response()->json([
                'error' => 'Error al insertar o actualizar los datos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function postCancel(Request $request)
    {
        // Inicializar la respuesta
        $response = ['message' => '', 'data' => null, 'error' => null];

        // Definir las cadenas dentro de la función para evitar duplicación

        $messageUpdateSuccess     = 'Status actualizado exitosamente';
        $errorReservationNotFound = 'Reservación no encontrada';
        $errorGeneral = 'Error al insertar los datos: '; // Definir la constante para el error general

        try {
            // Validar los campos requeridos
            $validateData = $request->validate([
                'ClaveReserva' => 'required',
                'Estatus'      => 'required',
                'User'         => 'required',
            ]);

            // Extraer los datos validados
            $claveReserva = $validateData['ClaveReserva'];
            $status       = $validateData['Estatus'];
            $user         = $validateData['User'];

            // Insertar los datos en la tabla Canceladas
            $insertData = cancelledBooking::create([
                'CVE_RESERVACION' => $claveReserva,
                'CVE_USER'        => $user,
            ]);

            // Buscar la reservación para actualizar el estado
            $reservacion = bookings::where('CVE_RESERVACION', $claveReserva)->first();

            if ($reservacion) {
                // Actualizar el campo STATUS_RESERVA
                $reservacion->STATUS_RESERVA = $status;
                $reservacion->save(); // Guardar los cambios

                // Mensaje de éxito
                $response['message'] = $messageUpdateSuccess;
                $response['data']    = $reservacion;
            } else {
                // Manejar el caso cuando la reservación no es encontrada
                $response['error'] = $errorReservationNotFound;
                return response()->json($response, 404); // Retornar en caso de error específico
            }

            // Asignar el mensaje de éxito para la inserción
            $response['message'] = 'Datos insertados correctamente';
            $response['data']    = $insertData;
            return response()->json($response, 201); // Retornar el éxito
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar errores de validación
            $response['error'] = $e->validator->errors();
        } catch (\Exception $e) {
            // Manejar errores generales, utilizando la constante definida
            $response['error'] = $errorGeneral . $e->getMessage();
        }

        // Retornar la respuesta en caso de error general o validación
        return response()->json($response, isset($response['error']) ? 422 : 500);
    }

    public function postInsert(Request $request)
    {
        try {
            // Validar los campos requeridos
            $validateData = $request->validate([
                'Nombre_operador' => 'required', // Nombre de operador obligatorio
                'CVE_USER'        => 'required', // Clave de usuario obligatoria
                'Num_Reserva'     => 'required', // Número de reserva obligatorio
                'Comentarios'     => 'nullable', // Comentarios opcionales
            ]);

            // Extraer los datos validados
            $cve_User        = $validateData['CVE_USER'];
            $nombre_operador = $validateData['Nombre_operador'];
            $comentarios     = $validateData['Comentarios'] ?? ''; // Proveer un valor vacío si no hay comentarios
            $num_Reserva     = $validateData['Num_Reserva'];

            // Insertar los datos en la tabla Confirmadas
            $insertData = confirmedReservations::create([
                'NOM_OPERADOR'    => $nombre_operador, // Asigna el nombre del operador
                'CVE_RESERVACION' => $num_Reserva,     // Asigna el número de reserva
                'CVE_USER'        => $cve_User,        // Asigna la clave de usuario
            ]);

            // Insertar o actualizar observaciones en la tabla Observaciones
            commentReservations::updateOrCreate(
                ['CVE_RESERVACION' => $num_Reserva], // Criterio de búsqueda
                ['OBSERVACIONES' => $comentarios]    // Valor a actualizar o insertar
            );

            // Retornar un mensaje de éxito
            return response()->json(['message' => 'Datos insertados/actualizados correctamente', 'data' => $insertData], 201);
        } catch (ValidationException $e) {
            // Manejar errores de validación
            return response()->json(['error' => 'Error de validación: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            // Manejar cualquier otro error
            return response()->json(['error' => 'Error al insertar/actualizar los datos: ' . $e->getMessage()], 500);
        }
    }

    public function postAllConfir(Request $request)
    {
        try {
            // Validar los campos requeridos
            $validateData = $request->validate([
                'fechaInicio' => 'required|date',
                'fechaFin'    => 'required|date',
            ]);

            $fechaInicio = $validateData['fechaInicio'];
            $fechaFin    = $validateData['fechaFin'];

            // Subconsulta para obtener las reservaciones confirmadas
            $reservacionesConfirmadas = confirmedReservations::select('CVE_RESERVACION')->get();

            // Consulta principal SIN relaciones no definidas
            $reservaciones = bookings::with(['huesped' => function ($query) {
                $query->select('CVE_RESERVACION', 'NOMBRE', 'APELLIDOS');
            }])
                ->whereBetween('FCH_RESERVACION', [$fechaInicio, $fechaFin])
                ->where('STATUS_RESERVA', 'PAID')
                ->whereIn('CVE_RESERVACION', $reservacionesConfirmadas->pluck('CVE_RESERVACION'))
                ->orderBy('FCH_CHECKIN', 'asc')
                ->get();

            // Estructura de respuesta
            $reservaciones = $reservaciones->map(function ($reservacion) {
                $reservacionArray = $reservacion->toArray();
                unset($reservacionArray['id']); // Elimina el campo 'id' si existe

                // Formatear huésped
                if ($reservacion->huesped) {
                    $reservacionArray['huesped'] = [
                        'NOMBRE'    => $reservacion->huesped->NOMBRE,
                        'APELLIDOS' => $reservacion->huesped->APELLIDOS,
                    ];
                }

                return $reservacionArray;
            });

            return response()->json($reservaciones);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            // Manejo general para evitar 500 sin explicación
            return response()->json([
                'error' => true,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }


    public function postConsultReserva(Request $request)
    {
        try {
            // Validar los campos requeridos
            $validateData = $request->validate([
                'fechaInicio' => 'required',
                'fechaFin'    => 'required',
            ]);

            $fechaInicio = $validateData['fechaInicio'];
            $fechaFin    = $validateData['fechaFin'];

            // Subconsulta para obtener las reservaciones confirmadas
            $reservacionesConfirmadas = confirmedReservations::select('CVE_RESERVACION')->get();

            // Consulta la tabla principal para obtener solo las reservaciones confirmadas
            $reservaciones = bookings::with(['plataforma', 'huesped', 'pagos', 'confirmadas'])
                ->whereBetween('FCH_RESERVACION', [$fechaInicio, $fechaFin])
                ->where('STATUS_RESERVA', 'PAID')
                ->whereIn('CVE_RESERVACION', $reservacionesConfirmadas->pluck('CVE_RESERVACION')) // Incluir solo las confirmadas
                ->orderBy('FCH_CHECKIN', 'asc')
                ->get();

            return response()->json($reservaciones);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
    public function postAllReserv(Request $request)
    {
        try {
            // Validar los campos requeridos
            $validateData = $request->validate([
                'fechaInicio' => 'required',
                'fechaFin'    => 'required',
            ]);

            $fechaInicio = $validateData['fechaInicio'];
            $fechaFin    = $validateData['fechaFin'];

            // Subconsulta para obtener las reservaciones confirmadas
            $reservacionesConfirmadas = confirmedReservations::select('CVE_RESERVACION')->get();

            // Consulta la tabla principal con los campos deseados
            $reservaciones = bookings::with(['plataforma', 'huesped', 'pagos'])
                ->whereBetween('FCH_RESERVACION', [$fechaInicio, $fechaFin])
                ->where('STATUS_RESERVA', 'PAID')
                ->whereNotIn('CVE_RESERVACION', $reservacionesConfirmadas->pluck('CVE_RESERVACION')) // Omitir las confirmadas
                ->orderBy('FCH_CHECKIN', 'asc')
                ->get();

            return response()->json($reservaciones);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function postReservOperador(Request $request)
    {
        try {
            $login = new LoginController();
            $function = $login->getPayload($request)->getContent();
            $data = json_decode($function, true);
            $user = $data['data']['token']['sub'];

            $fechaFin = Carbon::now()->format('Y-m-d');
            $fechaInicio = Carbon::now()
                ->subMonths(2)
                ->format('Y-m-d');
            Log::debug('postReservOperador: obteniendo reservas para el operador', [
                'user' => $user,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin
            ]);

            // Subconsulta para obtener las reservaciones confirmadas
            $reservacionesConfirmadas = confirmedReservations::select('CVE_RESERVACION')->get();

            // Consulta la tabla principal con los campos deseados
            $reservaciones = bookings::with(['plataforma', 'huesped', 'pagos'])
                ->whereBetween('FCH_RESERVACION', [$fechaInicio, $fechaFin])                         // Filtrar por fechas
                ->where('STATUS_RESERVA', 'PAID')                                                    // Filtrar por estado de reserva
                ->whereNotIn('CVE_RESERVACION', $reservacionesConfirmadas->pluck('CVE_RESERVACION')) // Omitir las confirmadas
                ->where('CVE_USUARIO', $user)                                                        // Filtrar por el usuario
                ->orderBy('FCH_CHECKIN', 'asc')                                                      // Ordenar por la fecha de check-in
                ->get();

            $reservaciones = bookings::with(['plataforma', 'huesped', 'pagos'])
                ->whereBetween('FCH_RESERVACION', [$fechaInicio, $fechaFin])
                ->where('STATUS_RESERVA', 'PAID')
                ->where('CVE_USUARIO', $user)

                ->whereNotIn(
                    'CVE_RESERVACION',
                    confirmedReservations::select('CVE_RESERVACION')
                )

                ->whereNotIn(
                    'CVE_RESERVACION',
                    breakdownHotels::select('CVE_RESERVACION')
                )

                ->orderBy('FCH_CHECKIN', 'asc')
                ->get();

            return $this->successResponse('no. reservas obtenidas', $reservaciones);                // Obtener un array plano

        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function agregarProveedor(Request $request)
    {
        try {

            $request->validate([
                'nombre' => 'required',
            ]);

            $proveedor = catalogSupplierHotels::create([
                'nombre' =>
                $request->nombre,
            ]);

            return response()->json([
                'Estatus' => 'true',
                'message' => 'Datos insertados correctamente',
                'data'    => $proveedor,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'Estatus' => 'false',
                'message' => 'Error al insertar los datos: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function createDesglose(Request $request)
    {
        // Validar los datos de entrada (sin requerir que sean obligatorios)
        $request->validate([
            'agencia'     => 'nullable',
            'checkin'     => 'nullable',
            'checkout'    => 'nullable',
            'cobrado'     => 'nullable',
            'comi'        => 'nullable',
            'comiagen'    => 'nullable',
            'comprobante' => 'nullable',
            'currency'    => 'nullable',
            'cxs'         => 'nullable',
            'destino'     => 'nullable',
            'dk'          => 'nullable',
            'email'       => 'nullable',
            'fecha'       => 'nullable',
            'fpago'       => 'nullable',
            'hotel'       => 'nullable',
            'localizador' => 'nullable',
            'netop'       => 'nullable',
            'noches'      => 'nullable',
            'obs'         => 'nullable',
            'operador'    => 'nullable',
            'porcomi'     => 'nullable',
            'porcomiagen' => 'nullable',
            'proveedor'   => 'nullable',
            'ref'         => 'nullable',
            'status'      => 'nullable',
            'titular'     => 'nullable',
            'total'       => 'nullable',
            'fchpago'     => 'nullable',
            'mpago'       => 'nullable',
            'alcance'     => 'nullable',
            'serie'       => 'nullable',
            'servicio'    => 'nullable',
        ]);

        // Asignar los valores del request a variables locales
        $fecha    = $request->fecha;
        $checkin  = $request->checkin;
        $checkout = $request->checkout;
        $fchpago  = $request->fchpago;

        try {
            // Crear el registro
            $insertData = breakdownHotels::create([
                'CVE_RESERVACION'    => $request->localizador,
                'USER'               => $request->operador,
                'proveedor'          => $request->proveedor,
                'CXS'                => $request->cxs,
                'cve_agencia'        => $request->dk,
                'nom_agencia'        => $request->agencia,
                'FCH_RESERVACION'    => $fecha,
                'FCH_CHECKIN'        => $checkin,
                'FCH_CHECKOUT'       => $checkout,
                'FCH_PAGO'           => $fchpago,
                'NOMBRE_HOTEL'       => $request->hotel,
                'TITULAR'            => $request->titular,
                'OBSERVACIONES'      => $request->obs ?? 'No hay observaciones', // Establecer valor por defecto si está vacío
                'TOTAL'              => $request->total,
                'CURRENCY'           => $request->currency,
                'neto_proveedor'     => $request->netop,
                'COBRADO'            => $request->cobrado,
                'COMISION'           => $request->comi,
                'PORC_COMI'          => $request->porcomi,
                'COMISION_CLIENTE'   => $request->comiagen,
                'PORCENTAJE_CLIENTE' => $request->porcomiagen,
                'DESTINO'            => $request->destino,
                'METODO_PAGO'        => $request->mpago,
                'FORMA_PAGO'         => $request->fpago,
                'alcance'            => $request->alcance,
                'SERIE'              => $request->serie,
                'COMPROBANTE'        => $request->comprobante,
                'email'              => $request->email,
                'REFAGEN'            => $request->ref,
                'NOCHES'             => $request->noches,
                'status'             => $request->status,
                'servicio'           => $request->servicio,
            ]);

            return response()->json(['Estatus' => 'true', 'message' => 'Datos insertados correctamente', 'data' => $insertData], 201);
        } catch (\Exception $e) {
            return response()->json(['Estatus' => 'false', 'message' => 'Error al insertar los datos: ' . $e->getMessage()], 400);
        }
    }


    public static function getProveedores()
    {
        // Obtener todos los registros de la tabla
        $proveedores = catalogSupplierHotels::all(); // Esto es equivalente a SELECT * FROM UserAcess

        // Retornar los datos en formato JSON
        return response()->json($proveedores);
    }

    public function getReport()
    {

        $fechaSolicitada = "2024-09-01";

        $reservaciones = bookings::with(['plataforma', 'huesped', 'pagos'])
            ->where('FCH_RESERVACION', '>=', $fechaSolicitada)
            ->where('STATUS_RESERVA', 'PAID')
            ->get();

        return response()->json($reservaciones);
    }
}
