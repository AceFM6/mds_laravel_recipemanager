<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recipes = Auth::user()->recipes;
        return response()->json(["status" => "success", "error" => false, "count" => count($recipes), "data" => $recipes],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "title" => "required|max:255|min:3|unique:recipes,title",
            "description" => "required",
            "instructions" => "required",
            "level" => 'required|integer|between:0,10',
            "time" => "required|integer"
        ]);

        if($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        try {
            $recipe = Recipe::create([
                "title" => $request->title,
                "description" => $request->description,
                "instructions" => $request->instructions,
                "time" => $request->time,
                "level" => $request->level,
                "user_id" => Auth::user()->id
            ]);
            return response()->json(["status" => "success", "error" => false, "message" => "Success! recipe created."], 201);
        }
        catch(Exception $exception) {
            return response()->json(["status" => "failed", "error" => $exception->getMessage()], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $recipe = Auth::user()->recipes->find($id);

        if($recipe) {
            return response()->json(["status" => "success", "error" => false, "data" => $recipe], 200);
        }
        return response()->json(["status" => "failed", "error" => true, "message" => "Failed! no recipe found."], 404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $recipe = Auth::user()->recipes->find($id);

        if($recipe) {
            $validator = Validator::make($request->all(), [
                "title" => "required|max:255|min:3|unique:recipes,title",
                "description" => "required",
                "instructions" => "required",
                "level" => 'required|integer|between:0,10',
                "time" => "required|integer"
            ]);

            if($validator->fails()) {
                return $this->validationErrors($validator->errors());
            }

            $recipe['title'] = $request->title;
            $recipe['description'] = $request->description;
            $recipe['instruction'] = $request->instructions;
            $recipe['time'] = $request->time;
            $recipe['level'] = $request->level;

            $recipe->save();
            return response()->json(["status" => "success", "error" => false, "message" => "Success! recipe updated."], 201);
        }
        return response()->json(["status" => "failed", "error" => true, "message" => "Failed no recipe found."], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $recipe = Auth::user()->recipes->find($id);
        if($recipe) {
            $recipe->delete();
            return response()->json(["status" => "success", "error" => false, "message" => "Success! recipe deleted."], 200);
        }
        return response()->json(["status" => "failed", "error" => true, "message" => "Failed no recipe found."], 404);
    }
}
