<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

use App\Model\cyclone_result;
use App\Model\cyclone_sm;
use App\Model\cyclone_type;
use App\Model\cyclone;
use App\Model\cyclone_sumation;

class CycloneController extends Controller
{
    public function get_cyclone_type()
    {
      $input = Input::all();
      $data = null;
      $i = 0;
      $query = cyclone_type::leftjoin('cyclone','cyclone_type.type_id','=','cyclone.type_id')
      ->select('cyclone_type.type_id', 'type', 'd_d', 'h_d', 'w_d', 'dc_d', 'lv_d', 'lb_d', 'lc_d', 'dd_d');
      // ->where('cyclone_type.type_id','=',1);
      // Punpun8308
      $query = $query->get();
      // $count_query = $query->get();
      // $count_data = count($count_query);
      // ->get();
      foreach($query as $key){
        $data[$i]['type_id'] = $key->type_id;
        $data[$i]['type'] = $key->type;
        $data[$i]['d_d'] = $key->d_d;
        $data[$i]['h_d'] = $key->h_d;
        $data[$i]['w_d'] = $key->w_d;
        $data[$i]['dc_d'] = $key->dc_d;
        $data[$i]['lv_d'] = $key->lv_d;
        $data[$i]['lb_d'] = $key->lb_d;
        $data[$i]['lc_d'] = $key->lc_d;
        $data[$i]['dd_d'] = $key->dd_d;
        $i++;
      }
      return response()->json(['status'=>'successful','data'=>$data],200);
    }
    public function post_result(Request $request){
      $validate = Validator::make($request->all(),[
        'ne' => 'required',
        'q' => 'required',
        'vi' => 'required'
      ]);
      if($validate->fails()){
        $errors = $validate->errors();
        return response($errors,405);
      }
      $item = cyclone_result::create($request->all());
      return response()->json(["status"=>"successful", "data"=>$item],200);
    }
    public function post_cyclone_result(Request $request){
      $validate = Validator::make($request->all(),[
        'user_id' => 'required | numeric',
        'type_id => numeric',
        'diameter => numeric',
        'd => numeric',
        'h => numeric',
        'w => numeric',
        'de => numeric',
        'lv => numeric',
        'lb => numeric',
        'lc => numeric',
        'dd => numeric',
        'ne => numeric',
        'q => numeric',
        'vi => numeric',
        'delta_t => numeric',
        'u => numeric',
        'pp => numeric',
        'pg => numeric',
        'dpc => numeric',
        'vpt => numeric',
        'k => numeric',
        'hv => numeric',
        'delta_p => numeric',
        'winput => numeric',
        'delta_h2o => numeric',
        'percent_collection' => 'required'
      ]);
      if($validate->fails()){
        $errors = $validate->errors();
        return response($errors,405);
      } else {
        $create_cyclone = cyclone::create([
          'user_id'=>$request->user_id,
          'type_id'=>$request->type_id,
          'diameter'=>$request->diameter,
          'd'=>$request->d,
          'h'=>$request->h,
          'w'=>$request->w,
          'de'=>$request->de,
          'lv'=>$request->lv,
          'lb'=>$request->lb,
          'lc'=>$request->lc,
          'dd'=>$request->dd
        ]);
        $create_cyclone_result = cyclone_result::create([
          'user_id'=>$request->user_id,
          'type_id'=>$request->type_id,
          'ne'=>$request->ne,
          'q'=>$request->q,
          'vi'=>$request->vi,
          'delta_t'=>$request->delta_t,
          'u'=>$request->u,
          'pp'=>$request->pp,
          'pg'=>$request->pg,
          'dpc'=>$request->dpc,
          'vpt'=>$request->vpt,
          'k'=>$request->k,
          'hv'=>$request->hv,
          'delta_p'=>$request->delta_p,
          'winput'=>$request->winput,
          'delta_h2o'=>$request->delta_h2o
        ]);
        $percent_collection = $request->percent_collection;
        $dpj = null;
        $dpjdpc = null;
        $nj = null;
        $njmj = null;
        if ($request->has('percent_collection')) {
          for ($i=0; $i < count($percent_collection); $i++) {
            $dpj[$i] = ($percent_collection[$i]['size_min'] + $percent_collection[$i]['size_max']) / 2;
            $dpjdpc[$i] = ($dpj[$i]) / (number_format($request->dpc, 2, '.', ''));
            $nj[$i] = number_format((1 / (1 + pow(($request->dpc / $dpj[$i]), 2))), 2, '.', '');
            $njmj[$i] = number_format(($nj[$i] * $percent_collection[$i]['mj']), 2, '.', '');
            $create_cyclone_sm = cyclone_sm::create([
              'cyclone_id'=>$create_cyclone_result->cyclone_id,
              'type_id'=>$request->type_id,
              'user_id'=>$request->user_id,
              'j'=> $i + 1,
              'size_min'=> $percent_collection[$i]['size_min'],
              'size_max'=> $percent_collection[$i]['size_max'],
              'mj'=> $percent_collection[$i]['mj'],
              'dpj'=> $dpj[$i],
              'dpj_dpc'=> $dpjdpc[$i],
              'nj'=> $nj[$i],
              'njmj'=> $njmj[$i]
            ]);
          }
        } else {
          return response()->json(['status'=>'failed','message'=> 'Please input percent collection'],400);
        }
        $create_cyclone_sumation = cyclone_sumation::create([
          'sum_njmj'=>$this->sum_percent_collection($create_cyclone_result->cyclone_id),
          'diameter'=>$request->diameter
        ]);
        return response()->json(['status'=>'successful'],200);
      }
    }
    public function get_cyclone_result(Request $request,$user_id) {
      $input = Input::all();
      $data = null;
      $i = 0;
      $query = cyclone::select('*')
      ->leftjoin('cyclone_result','cyclone_result.cyclone_id','=','cyclone.cyclone_id')
      ->where('cyclone.user_id', $user_id);
      $query = $query->get();
      foreach($query as $key){
        $data[$i]['cyclone_id'] = $key->cyclone_id;
        $data[$i]['user_id'] = $key->user_id;
        $data[$i]['type_id'] = $key->type_id;
        $data[$i]['diameter'] = $key->diameter;
        $data[$i]['d'] = $key->d;
        $data[$i]['h'] = $key->h;
        $data[$i]['w'] = $key->w;
        $data[$i]['de'] = $key->de;
        $data[$i]['lv'] = $key->lv;
        $data[$i]['lb'] = $key->lb;
        $data[$i]['lc'] = $key->lc;
        $data[$i]['dd'] = $key->dd;
        $data[$i]['ne'] = $key->ne;
        $data[$i]['q'] = $key->q;
        $data[$i]['vi'] = $key->vi;
        $data[$i]['delta_t'] = $key->delta_t;
        $data[$i]['u'] = $key->u;
        $data[$i]['pp'] = $key->pp;
        $data[$i]['pg'] = $key->pg;
        $data[$i]['dpc'] = $key->dpc;
        $data[$i]['vpt'] = $key->vpt;
        $data[$i]['k'] = $key->k;
        $data[$i]['hv'] = $key->hv;
        $data[$i]['delta_p'] = $key->delta_p;
        $data[$i]['winput'] = $key->winput;
        $data[$i]['delta_h2o'] = $key->delta_h2o;
        $data[$i]['collection_result'] = $this->collection_result($key->cyclone_id);
        $data[$i]['sum_percent_collection'] = $this->get_percent_collection($key->cyclone_id);
        $i++;
      }
      return response()->json(['status'=>'successful', 'data'=>$data],200);
    }
    protected function collection_result($cyclone_id){
      $query = cyclone_sm::select('*')
      ->where('cyclone_sm.cyclone_id', $cyclone_id)
      ->get();
      $i = 0;
      $sum = 0;
      $data = array();
      foreach ($query as $key) {
        $data[$i]['j'] = $key->j;
        $data[$i]['size_min'] = $key->size_min;
        $data[$i]['size_max'] = $key->size_max;
        $data[$i]['dpj'] = $key->dpj;
        $data[$i]['dpj_dpc'] = $key->dpj_dpc;
        $data[$i]['nj'] = $key->nj;
        $data[$i]['mj'] = $key->mj;
        $data[$i]['njmj'] = $key->njmj;
        $i++;
      }
      return $data;
    }
    protected function get_percent_collection($cyclone_id){
      $query = cyclone_sumation::select('*')
      ->where('cyclone_sumation.cyclone_id', $cyclone_id)
      ->get();
      $i = 0;
      $data = array();
      foreach ($query as $key) {
        $data[$i]['cyclone_sumation'] = $key->sum_njmj;
        $i++;
      }
      return $data;
    }
    protected function sum_percent_collection($cyclone_id){
      $query = cyclone_sm::select('*')
      ->where('cyclone_sm.cyclone_id', $cyclone_id)
      ->get();
      $i = 0;
      $sum = null;
      $data = array();
      foreach ($query as $key) {
        $sum = $sum +  $key->njmj;
        $i++;
      }
      return $sum;
    }
    public function get_comparation(Request $request,$user_id) {
      $input = Input::all();
      $query = cyclone_sumation::leftjoin('cyclone_result', 'cyclone_sumation.cyclone_id', '=', 'cyclone_result.cyclone_id')
      ->join('cyclone_type', 'cyclone_result.type_id', '=', 'cyclone_type.type_id')
      ->where('cyclone_result.user_id', $user_id);
      $query = $query->get();
      $data = array();
      $i = 0;
      foreach($query as $key){
        $data[$i]['cyclone_id'] = $key->cyclone_id;
        $data[$i]['diameter'] = $key->diameter;
        $data[$i]['type_id'] = $key->type_id;
        $data[$i]['type_name'] = $key->type;
        $data[$i]['sum_njmj'] = $key->sum_njmj;
        $data[$i]['winput'] = $key->winput;
        $data[$i]['delta_h2o'] = $key->delta_h2o;
        $i++;
      }
      return response()->json(['status'=>'successful', 'data'=>$data],200);
    }
}
