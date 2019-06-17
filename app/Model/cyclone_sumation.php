<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class cyclone_sumation extends Model
{
  protected $table = 'cyclone_sumation';
  protected $primaryKey = 'cyclone_id';
  protected $fillable = [
    'diameter',
    'sum_njmj'
  ];
}
