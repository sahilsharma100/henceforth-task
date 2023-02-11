<?php

namespace App\Http\Controllers\Api\V1;


use Validator;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{

    public function myTask(Request $request)
    {
        $limit = $request->limit ?? 10;
        $data = Task::whereUserId(Auth::user()->id)->where('title', 'like', '%'.$request->search.'%')->paginate($limit);
        return response()->json(['data' => $data, 'status' => true, 'message' => 'My Taks'], 200);
        
    }

    public function add(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|max:220',
                'slug' => 'required|unique:tasks',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'status' => false, 'message' => ''], 422);
        }

        $insert = [
            "user_id" => Auth::user()->id,
            'title' => $request->title,
            'slug' => \Str::slug($request->slug),
        ];
        $data = Task::create($insert);
        return response()->json(['data' => [], 'status' => true, 'message' => 'Task Created'], 201);
    }


    public function edit($id)
    {
        $data = Task::whereUserId(Auth::user()->id)->whereId($id)->first();
        if($data){
            return response()->json(['data' => $data, 'status' => true, 'message' => 'Task Found'], 200);
        }
        return response()->json(['data' => [], 'status' => false, 'message' => 'Task Not found'], 404);
    }

    public function update(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required',
                'title' => 'required|max:220',
                'slug' => 'required|unique:tasks,id,:id',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'status' => false, 'message' => ''], 422);
        }

        $data = Task::whereUserId(Auth::user()->id)->whereId($request->id)->first();
        if($data){
            $data->slug = $request->slug;
            $data->title = $request->title;
            $data->save();
            return response()->json(['data' => [], 'status' => true, 'message' => 'Task Updated'], 201);
        }
        return response()->json(['data' => [], 'status' => false, 'message' => 'Task Not Found'], 404);
    }

    public function markUnmark(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'status' => false, 'message' => ''], 422);
        }

        $data = Task::whereUserId(Auth::user()->id)->whereId($request->id)->first();
        if($data){
            $data->status = $data->status == 1 ? 0 : 1;
            $data->save();
            return response()->json(['data' => $data, 'status' => true, 'message' => 'Task Status Updated'], 200);
        }
        return response()->json(['data' => [], 'status' => false, 'message' => 'Task Not found'], 404);
    }
}
