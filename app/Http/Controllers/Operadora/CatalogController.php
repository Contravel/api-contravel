<?php

namespace App\Http\Controllers\Operadora;

use App\Http\Controllers\ApiController;
use App\Mail\InfoPaqueteMail;
use App\Mail\VisaSolicitudMail;
use App\Models\operadora\CatalogoDigital;
use App\Models\operadora\Paises;
use App\Models\operadora\paquetes;
use App\Models\operadora\Paquetes as OperadoraPaquetes;
use App\Models\operadora\ServicesTrenes;
use App\Models\operadora\VisaContacto;
use App\Traits\TokenManage;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CatalogController extends ApiController
{
    use TokenManage;

    public function updateStatus(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'date' => 'required',
                ],
                [
                    'date.required' => 'The date field is required.',
                ]
            );
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return $this->errorResponse('Error de validación', $errors, 422);
            }

            $date = $request->input('date');
            $fecha_actual = date("Y-m-d", strtotime($date));
            DB::beginTransaction();
            $sentence = Paquetes::where('fecha_expi', '<', $fecha_actual)->update(['status' => false]);
            return $this->successResponse('Paquetes actualizados correctamente', $sentence);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Catalog Error",  'No se pudo actualizar la información la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }

    public function getLastestOffers()
    {

        try {
            $fecha_actual = date("d-m-Y");
            $fecha_rest   = date("Y-m-d", strtotime($fecha_actual . "- 10 days"));
            $fecha_expi   = date("Y-m-d", strtotime($fecha_actual . "+ 29 days"));

            $paquetes = Paquetes::where(function ($q) use ($fecha_rest, $fecha_actual, $fecha_expi) {
                $q->where(function ($q2) use ($fecha_rest) {
                    $q2->where('status', 'true')
                        ->where('fecha_mod', '>=', $fecha_rest);
                })
                    ->orWhere(function ($q2) use ($fecha_actual, $fecha_expi) {
                        $q2->where('status', 'true')
                            ->where('fecha_expi', '>', $fecha_actual)
                            ->where('fecha_expi', '<=', $fecha_expi);
                    })
                    ->orWhere(function ($q2) {
                        $q2->where('status', 'true')
                            ->where('oferta', 'true');
                    });
            })
                ->orderByDesc('oferta')
                ->orderBy('fecha_expi', 'asc')
                ->limit(10)
                ->get();

            return $this->successResponse('Paquetes obtenidos correctamente', $paquetes);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error",  'No se pudo almacenar la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }


    public function getCatalogDigital()
    {
        try {

            $catalogo = CatalogoDigital::all();
            return $this->successResponse('Catalogo Digital obtenido correctamente', $catalogo);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Catalogo Error",  'No se pudo obtener la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }

    public function getCircuit()
    {
        try {

            $paquetes = Paquetes::where('status', 'true')
                ->where('tipo', 'Circuito')
                ->orderByDesc('oferta')
                ->orderBy('fecha_expi', 'asc')
                ->get();
            return $this->successResponse('Circuitos obtenidos correctamente', $paquetes);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Catalogo Error",  'No se pudo obtener la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }

    public function getByRegion(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'tipo' => 'required',
                    'region' => 'required'
                ],
                [
                    'tipo.required' => 'The tipo field is required.',
                    'region.required' => 'The region field is required.',
                ]
            );
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return $this->errorResponse('Error de validación', $errors, 422);
            }
            $tipo   = $request->input('tipo');
            $region = $request->input('region'); // puede ser string o array

            $query = Paquetes::where('status', 'true')
                ->where('tipo', $tipo);

            if (is_array($region) && count($region) > 1) {
                $query->whereIn('region', $region); // varias regiones
            } else {
                $query->where('region', is_array($region) ? $region[0] : $region); // 1 sola región
            }

            $paquetes = $query->orderBy('fecha_expi', 'asc')->get();

            return $this->successResponse('Paquetes obtenidos correctamente', $paquetes);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Catalogo Error",  'No se pudo obtener la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }

    public function getCountries(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'tipo' => 'required',
                    'pais' => 'required'
                ],
                [
                    'tipo.required' => 'The tipo field is required.',
                    'pais.required' => 'The region field is required.',
                ]
            );
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return $this->errorResponse('Error de validación', $errors, 422);
            }
            $tipo = $request->input('tipo');
            $paises = $request->input('pais'); // puede ser array o string
            Log::debug('Paises recibidos', ['tipo' => $tipo, 'paises' => $paises]);
            $query = Paises::select('tbl_paises.pais', 'tbl_paises.region')
                ->distinct()
                ->join('tbl_paquetes', 'tbl_paises.pais', '=', 'tbl_paquetes.destino')
                ->where('tbl_paquetes.status', 'true')
                ->where('tbl_paquetes.tipo', $tipo);

            if (is_array($paises) && count($paises) > 0) {
                $query->whereIn('tbl_paises.region', $paises);
            } elseif (is_string($paises)) {
                $query->where('tbl_paises.region', $paises);
            }

            $resultados = $query->get();

            // 👇 Aquí ya correcto para log
            Log::debug('resultados', $resultados->toArray());

            return $this->successResponse('Países obtenidos correctamente', $resultados);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Catalogo Error",  'No se pudo obtener la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }


    public function getByCountry(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'tipo' => 'required',
                    'pais' => 'required'
                ],
                [
                    'tipo.required' => 'The tipo field is required.',
                    'pais.required' => 'The region field is required.',
                ]
            );
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return $this->errorResponse('Error de validación', $errors, 422);
            }
            $tipo = $request->input('tipo');
            $pais = $request->input('pais');
            Log::debug('País recibido', ['tipo' => $tipo, 'pais' => $pais]);
            $paquetes = Paquetes::where('status', 'true')
                ->where('tipo', $tipo)
                ->where('destino', $pais)
                ->orderBy('fecha_expi', 'asc')
                ->get();
            Log::debug($paquetes);
            Log::debug('Paquetes obtenidos', $paquetes->toArray());

            return $this->successResponse('Paquetes obtenidos correctamente', $paquetes);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Catalogo Error",  'No se pudo obtener la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }


    public function getCruises()
    {
        try {
            $paquetes = Paquetes::where('status', 'true')
                ->where('tipo', 'Crucero')
                ->orderByDesc('oferta')
                ->orderBy('fecha_expi', 'asc')
                ->get();
            return $this->successResponse('Cruceros obtenidos correctamente', $paquetes);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Catalogo Error",  'No se pudo obtener la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }

    public function getTrains()
    {
        try {
            $trenes = ServicesTrenes::all();

            return $this->successResponse('Trenes obtenidos correctamente', $trenes);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Catalogo Error",  'No se pudo obtener la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }

    public function getVisa()
    {
        try {
            $visa = VisaContacto::all();
            return $this->successResponse('Visas obtenidas correctamente', $visa);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Catalogo Error",  'No se pudo obtener la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }

    public function sendMailVisa(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'nombre' => 'required',
                    'email' => 'required',
                    'telefono' => 'required',
                    'nacionalidad' => 'required',
                    'residencia' => 'required',
                    'origen' => 'required',
                    'destino' => 'required',
                    'pasaporte' => 'required',
                    'salida' => 'required',
                    'retorno' => 'required'
                ],
                [
                    'nombre.required' => 'The nombre field is required.',
                    'email.required' => 'The email field is required.',
                    'telefono.required' => 'The telefono field is required.',
                    'nacionalidad.required' => 'The nacionalidad field is required.',
                    'residencia.required' => 'The residencia field is required.',
                    'origen.required' => 'The origen field is required.',
                    'destino.required' => 'The destino field is required.',
                    'pasaporte.required' => 'The pasaporte field is required.',
                    'salida.required' => 'The salida field is required.',
                    'retorno.required' => 'The retorno field is required.',
                ]
            );
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return $this->errorResponse('Error de validación', $errors, 422);
            }

            $dataContacto = VisaContacto::first();
            $data = $request->all();
            Mail::to($dataContacto->correo)->send(new VisaSolicitudMail($data, $dataContacto));

            return $this->successResponse('Correo enviado correctamente', true);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Catalogo Error",  'No se pudo obtener la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }

    public function getDetails(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'id' => 'required',
                ],
                [
                    'id.required' => 'The nombre field is required.',
                ]
            );
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return $this->errorResponse('Error de validación', $errors, 422);
            }

            $paquete = Paquetes::find($request->input('id'));

            return $this->successResponse('Paquete encontrado', $paquete);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Catalogo Error",  'No se pudo obtener la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }

    public function sendMailOffer(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'nombre' => 'required',
                    'agencia' => 'required',
                    'id' => 'required',
                    'message' => 'required',
                    'email' => 'required',
                ],
                [
                    'nombre.required' => 'The nombre field is required.',
                    'agencia.required' => 'The agencia field is required.',
                    'id.required' => 'The id field is required.',
                    'message.required' => 'The message field is required.',
                    'email.required' => 'The email field is required.',
                ]
            );
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return $this->errorResponse('Error de validación', $errors, 422);
            }

            $detallesPaquete = Paquetes::find($request->input('id'));
            Log::debug('Detalles del paquete', ['detalles' => $detallesPaquete]);
            $data = $request->all();
            Log::debug('Datos recibidos para el correo', ['data' => $data]);
            Mail::to('it@contravel.com.mx')->send(new InfoPaqueteMail($data, $detallesPaquete));

            return $this->successResponse('Correo enviado correctamente', true);
        } catch (QueryException $e) {
            DB::rollBack();
            return $this->errorResponse("Catalogo Error",  'No se pudo obtener la información: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse("Sesión Error", 'Error general: ' . $e->getMessage(), 500);
        }
    }

    public function download(Request $request)
    {
        if (!$request->has('url')) {
            abort(400, "Debes especificar el nombre del archivo a descargar.");
        }
        $url = $request->get('url');
        $ruta = "D:/Apache24/htdocs" . $url;

        $nombreArchivo = basename($url);
        Log::debug('Ruta del archivo a descargar', ['ruta' => $ruta, 'nombreArchivo' => $nombreArchivo]);
        if (file_exists($ruta) && is_readable($ruta)) {
            return response()->download($ruta, $nombreArchivo);
        } else {
            return response()->json(['error' => "El archivo no se puede descargar. Verifica el nombre y la ruta."], 404);
        }
    }
}
