<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class cyclone_result extends Model
{
  protected $table = 'cyclone_result';
  protected $primaryKey = 'cyclone_id';
  protected $fillable = [
    'user_id',
    'type_id',
    'ne',
    'q',
    'vi',
    'delta_t',
    'u',
    'pp',
    'pg',
    'dpc',
    'vpt',
    'k',
    'hv',
    'delta_p',
    'winput',
    'delta_h2o'
  ];
}
