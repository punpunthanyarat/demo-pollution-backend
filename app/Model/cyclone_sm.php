<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class cyclone_sm extends Model
{
  protected $table = 'cyclone_sm';
  protected $primaryKey = 'id';
  protected $fillable = [
    'cyclone_id',
    'type_id',
    'user_id',
    'j',
    'size_min',
    'size_max',
    'dpj',
    'dpj_dpc',
    'nj',
    'mj',
    'njmj'
  ];
}
