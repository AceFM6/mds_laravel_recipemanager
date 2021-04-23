<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($recipe)
    {
        $ingredients = Auth::user()->recipes->find($recipe)->ingredients;
        return response()->json(["status" => "success", "error" => false, "count" => count($ingredients), "data" => $ingredients],200);
    }

    public function store(Request $request, $recipe) 
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|max:255|min:3",
            "price" => "required",
        ]);

        if($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        try {
            $recipe = Ingredient::create([
                "name" => $request->name,
                "price" => $request->price,
                "recipe_id" => $recipe
            ]);
            return response()->json(["status" => "success", "error" => false, "message" => "Success! ingredient created."], 201);
        }
        catch(Exception $exception) {
            return response()->json(["status" => "failed", "error" => $exception->getMessage()], 404);
        }
    }

    public function show($recipe, $ingredient)
    {
        $ingredient = Auth::user()->recipes->find($recipe)->ingredients->find($ingredient);

        if($ingredient) {
            return response()->json(["status" => "success", "error" => false, "data" => $ingredient], 200);
        }
        return response()->json(["status" => "failed", "error" => true, "message" => "Failed! no ingredient found."], 404);
    }


    public function update(Request $request, $recipe, $id)
    {
        $ingredient = Auth::user()->recipes->find($recipe)->ingredients->find($id);

        if($ingredient) {
            $validator = Validator::make($request->all(), [
                "name" => "required",
                "price" => "required",
            ]);

            if($validator->fails()) {
                return $this->validationErrors($validator->errors());
            }

            $ingredient['name'] = $request->name;
            $ingredient['price'] = $request->price;

            $ingredient->save();
            return response()->json(["status" => "success", "error" => false, "message" => "Success! ingredient updated."], 201);
        }
        return response()->json(["status" => "failed", "error" => true, "message" => "Failed no ingredient found."], 404);
    }

    public function destroy($recipe, $id)
    {
        $ingredient = Auth::user()->recipes->find($recipe)->ingredients->find($id);
        if($ingredient) {
            $ingredient->delete();
            return response()->json(["status" => "success", "error" => false, "message" => "Success! ingredient deleted."], 200);
        }
        return response()->json(["status" => "failed", "error" => true, "message" => "Failed no ingredient found."], 404);
    }


}
