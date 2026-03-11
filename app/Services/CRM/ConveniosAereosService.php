<?php

namespace App\Services\CRM;

use App\Models\comission\AgreementsAir;
use App\Models\ConveniosAereos\ConvenioAereo;
use Illuminate\Support\Facades\DB;

class ConveniosAereosService
{
  /**
   * Reordenar registros según el array recibido.
   * Formato esperado:
   * [
   *   ["id" => 10, "orden" => 1],
   *   ["id" => 5,  "orden" => 2],
   *   ...
   * ]
   */
  public function reordenar(array $items)
  {
    DB::transaction(function () use ($items) {
      foreach ($items as $item) {
        AgreementsAir::where('id', $item['id'])
          ->update(['orden' => $item['orden']]);
      }
    });
  }
}
