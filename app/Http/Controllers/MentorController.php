<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MentorController extends Controller
{
    public function index()
    {
        $mentor = Mentor::all();
        return response()->json([
            'status' => 'success',
            'data' => $mentor,
        ]);
    }

    public function show($id)
    {
        $mentor = Mentor::find($id);
        if (!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'mentor not found',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $mentor,
        ]);
    }

    public function create(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string',
                'profile' => 'required|url',
                'profession' => 'required|string',
                'email' => 'required|string',
            ];

            $data = $request->all();

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 400);
            }

            $mentor = Mentor::where('email', $data['email'])->first();
            if ($mentor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'email already exist',
                ], 400);
            }

            $registeredMentor = Mentor::create($data);
            return response()->json(['status' => 'success', 'data' => $registeredMentor]);
        } catch (Exception $err) {
            return response()->json([
                'status' => 'error',
                'message' => $err,
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'name' => 'string',
                'profile' => 'url',
                'profession' => 'string',
                'email' => 'string',
            ];
            $data = $request->all();
            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 400);
            }

            $mentor = Mentor::find($id);
            if (!$mentor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'mentor not found',
                ], 404);
            }

            $email = $request->input('email');
            if ($email) {
                $checkEmail = Mentor::where('email', $email)->first();
                if ($checkEmail && $data['email'] !== $mentor->email) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'email already exist',
                    ], 400);
                }
            }

            $mentor->fill($data);
            $mentor->save();

            return response()->json(['status' => 'success', 'data' => $mentor]);
        } catch (Exception $err) {
            return response()->json([
                'status' => 'error',
                'message' => $err,
            ], 500);
        }
    }

    public function destroy($id)
    {
        $mentor = Mentor::find($id);
        if (!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'mentor not found',
            ], 404);
        }

        $mentor->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'mentor success deleted',
        ]);
    }
}
