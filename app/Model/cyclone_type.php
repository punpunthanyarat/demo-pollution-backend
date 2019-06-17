<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class cyclone_type extends Model
{
  protected $table = 'cyclone_type';
  protected $primaryKey = 'type_id';
  protected $fillable = [
    'type',
    'd_d',
    'h_d',
    'w_d',
    'dc_d',
    'lv_d',
    'lb_d',
    'lc_d',
    'dd_d'
  ];
}
