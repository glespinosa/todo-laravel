<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Todo::where('user_id', auth()->user()->id)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'completed' => 'required|boolean'
        ]);

        $data['user_id'] = auth()->user()->id;

        $todo = Todo::create($data);

        return response($todo, 201);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Todo $todo)
    {
        //to prevent other user to update a todo
        if ($todo->user_id !== auth()->user()->id)
            return response()->json('Unauthenticated', 401);

        $data = $request->validate([
            'title' => 'required|string',
            'completed' => 'required|boolean'
        ]);

        $todo->update($data);

        return response($todo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $todo = Todo::find($id);
        $user = $todo->user->id;

        //checking if the user is authenticated to delete the todo
        if ($user !== auth()->user()->id)
            return response()->json('Unauthenticated', 401);

        $todo->delete();
        return response('Deleted item ' . $id, 200);
    }

    public function updateAll(Request $request)
    {
        $data = $request->validate([
            'completed' => 'required|boolean'
        ]);

        Todo::where('user_id', auth()->user()->id)->update($data);
        return response('Updated', 200);
    }

    public function destroyCompleted(Request $request)
    {
        $todosToDelete = $request->todos;

        $todos = Todo::whereIn('id', $todosToDelete)
            ->where('user_id', auth()->user()->id)->get();

        if (count($todos) !== count($todosToDelete))
            return response()->json('Unauthenticated', 401);

        $request->validate([
            'todos' => 'required|array'
        ]);

        Todo::destroy($request->todos);

        return response()->json('Bulk delete ', 200);
    }
}
