<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\admin\AgenciasAdmon;
use App\Models\AdminDB\Administracion;
use App\Models\AdminDB\CargosTPV;
use App\Models\admon_op\catalogFiles;
use App\Models\admon_op\comissionTPV;
use App\Models\DesarrolloDB\Archivos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PostAdmin extends Controller
{

    public function postCargosTPV(Request $request)
    {
        $response = [];
        $status = 200; // Código HTTP por defecto

        try {
            // Validar los campos requeridos
            $validateData = $request->validate([
                'tipo' => 'required|string|min:1',
            ]);

            // Obtener el valor de 'tipo' sin espacios extra
            $tipo = trim($validateData['tipo']);

            // Buscar coincidencias parciales en la base de datos
            $cargoTPV = comissionTPV::query()
                ->where('nombre_tpv', 'LIKE', "%{$tipo}%")
                ->get();

            // Definir la respuesta según los resultados
            if ($cargoTPV->isEmpty()) {
                $response = ['message' => 'No se encontró ninguna agencia con el nombre o número de cliente ingresado'];
                $status = 404;
            } else {
                $response = $cargoTPV[0]->comision_tpv;
            }
        } catch (ValidationException $e) {
            $response = ['error' => $e->errors()];
            $status = 422;
        } catch (\Exception $e) {
            Log::error('Error en postCargosTPV: ' . $e->getMessage());

            $response = ['error' => 'Error al consultar los datos. Inténtelo más tarde.'];
            $status = 500;
        }

        // Un solo return al final
        return response()->json($response, $status);
    }
    public function postAgencias(Request $request)
    {
        // Inicializamos la variable de respuesta en null
        $response = null;
        $status   = 200;

        try {
            // Validar los campos requeridos
            $validateData = $request->validate([
                'NameAgen' => 'required|string', // Validación: campo obligatorio y debe ser una cadena
            ]);

            // Extraer los datos validados
            $nameAgen = $validateData['NameAgen'];

            // Realizar la consulta con coincidencias parciales usando LIKE
            $agencias = AgenciasAdmon::where('NOMBRE', 'LIKE', '%' . $nameAgen . '%')
                ->orWhere('NUM_CLIENTE', 'LIKE', '%' . $nameAgen . '%')
                ->get();

            // Verificar si no se encontraron resultados
            if ($agencias->isEmpty()) {
                $response = ['message' => 'No se encontró ninguna agencia con el nombre o número de cliente ingresado'];
                $status   = 404;
            } else {
                // Guardar los datos en la variable de respuesta
                $response = ['data' => $agencias];
            }
        } catch (ValidationException $e) {
            // En caso de error de validación, guardar el mensaje de error
            $response = ['error' => $e->getMessage()];
            $status   = 422;
        } catch (\Exception $e) {
            // En caso de error en la base de datos u otros, guardar el mensaje de error
            $response = ['error' => 'Error al consultar los datos: ' . $e->getMessage()];
            $status   = 500;
        }

        // Retornar la respuesta final
        return response()->json($response, $status);
    }
    public function almacenarPDF(Request $request)
    {
        if (!$request->hasFile('files')) {
            return response()->json(['error' => 'No se proporcionaron archivos.'], 400);
        }

        if (!$request->has('localizador')) {
            return response()->json(['error' => 'El localizador es requerido.'], 400);
        }

        $localizador = $request->input('localizador');
        $archivosGuardados = [];

        foreach ($request->file('files') as $file) {
            $path = $file->store('PDFs', 'public');
            $filename = basename($path);

            $archivo = catalogFiles::create([
                'localizador' => $localizador,
                'nombre'      => $filename,
            ]);

            // Solo devolver los atributos necesarios
            $archivosGuardados[] = [

                'localizador' => $archivo->localizador,
                'nombre'     => $archivo->nombre,
            ];
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Archivos subidos correctamente.',
            'archivos' => $archivosGuardados
        ], 200);
    }

    public function almacenarImg(Request $request)
    {
        if ($request->hasFile('img')) {
            // Almacena el archivo y devuelve el path
            $path = $request->file('img')->store('imagenes', 'public');

            // Obtén el nombre del archivo almacenado
            $filename = basename($path);

            return response()->json(['filename' => $filename], 200);
        }
        return response()->json(['error' => 'No file uploaded.'], 400);
    }
    public function procesarDatos($request)
    {
        $response = ['success' => 'Proceso completado'];
        $status = 200;

        if (!$request->has('dato')) {
            $response = ['error' => 'Falta el dato'];
            $status = 400;
        } elseif ($request->input('dato') === 'error1') {
            $response = ['error' => 'Dato inválido'];
            $status = 400;
        } elseif ($request->input('dato') === 'error2') {
            $response = ['error' => 'Otro error'];
            $status = 400;
        }

        return response()->json($response, $status);
    }
}
