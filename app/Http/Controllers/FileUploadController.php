<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FileUploadController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'attachments.*' => 'required|mimes:doc,pdf,docx,text,jpeg,jpg,png|max:25000',
        ]);

        collect($request->file('attachments'))->each(function ($file) {
            $name = $file->getClientOriginalName();
            $file->storeAs('public/messages/images', $name);
        });

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded!',
        ]);
    }
}
