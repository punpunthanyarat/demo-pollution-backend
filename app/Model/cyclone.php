<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class cyclone extends Model
{
  protected $table = 'cyclone';
  protected $primaryKey = 'cyclone_id';
  protected $fillable = [
    'user_id',
    'type_id',
    'diameter',
    'd',
    'h',
    'w',
    'de',
    'lv',
    'lb',
    'lc',
    'dd'
  ];
}
