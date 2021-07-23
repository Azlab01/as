<?php

namespace App\Repositories;
use Illuminate\Support\Str;
/*
* in charge of all processing and injecting into the controllers
*/
abstract class ResourceRepository
{

    protected $model;

    public function getPaginate($n)
    {
        return $this->model->paginate($n);
    }

    public function store(Array $inputs)
    {
        return $this->model->create($inputs);
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getByAttribute($column = "column", $key = "key", $symbole = "=")
    {
        return $this->model->where($column, $symbole, $key)->firstOrFail();
    }

    public function update($id, Array $inputs)
    {
        return $this->getById($id)->update($inputs);
    }

    public function destroy($id)
    {
        return $this->getById($id)->delete();
    }

    public function destroyApi($id)
    {
        return $this->getById($id)->delete();

    }

	public function getAll()
    {
        return $this->model->all();
    }

    public function getPluck($key = 'id', $value = 'name')
    {
        return $this->model->pluck($key, $value);
    }

    public function slug_check($request, $table, $id = null, $update = false){

        $inputs = $request->all();
        if($update){
            if($request->slug){
                $request->validate([
                    'slug' => ["regex:/^[a-z0-9-]+$/i","max:150","unique:".$table.",slug,".$id]
                ]);
                $request->slug = Str::slug($request->slug);
                $inputs = array_merge($request->all() ,["slug"=>Str::slug($request->slug)]);

            }else{
                $inputs = array_merge($request->all() ,["slug"=>Str::slug($request->name)]);
            }
        }else{
            if($request->slug){
                $request->validate([
                    'slug' => ["regex:/^[a-z0-9-]+$/i","max:150","unique:".$table.",slug"]
                ]);
                $request->slug = Str::slug($request->slug);
                $inputs = array_merge($request->all() ,["slug"=>Str::slug($request->slug)]);

            }else{
                $inputs = array_merge($request->all() ,["slug"=>Str::slug($request->name)]);
            }
        }
        return $inputs;
    }

}
