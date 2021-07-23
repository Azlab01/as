<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Repositories\AstreinteRepository;
use App\Astreinte;

class AstreinteController extends Controller
{
    public function __construct(AstreinteRepository $repository)
    {
        $this->repo = $repository;
    }


    public function index()
    {
        $astreinte = Astreinte::latest()->get();

        return view('backoffice.astreinte.index', compact('astreinte'));
    }

    public function edit(Request $request,Astreinte $astreinte)
    {
        return view('backoffice.astreinte.edit', compact('astreinte'));
    }

    public function store(Request $request)
    {

      $inputs = $request->all();
        $this->repo->store($inputs);

        return redirect()->route('astreinte.index');
    }


    public function show(Astreinte $astreinte)
    {
        $astreinte = $this->repo->getById($astreinte->id);
        return view('backoffice.astreinte.show', compact('astreinte'));
    }

    public function create(Request $request)
    {

        return view('backoffice.astreinte.create');

    }

    public function update(Request $request, Astreinte $astreinte)
    {
        $id = $astreinte->id;
        $inputs = $request->all();
        
        $res = $this->repo->update($id, $inputs);

       // flash()->warning(__("Impossible de modifier"));

        return redirect()->to($this->getRedirectUrl())
                            ->withInput($request->input())
                            ->withErrors($errors = null, $this->errorBag());
    }

    public function destroy(Astreinte $astreinte)
    {
        $astreinte->contents()->detach();
        $res = $this->repo->destroyApi($astreinte->id);
        if($res){
            return response()->json(['success'=>true]);
        }
        return response()->json(['error'=>true]);

    }

    public function all(Request $request)
    {
        $datas = Astreinte::pluck("name");
        if(isset($datas[0])) {
            return response()->json($datas);
        }
    }
}
