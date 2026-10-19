<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Knowledge;
use App\Models\KnowldegeImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        $knowledge = Knowledge::with('images')->latest()->get();
        return view('backend.Knowledge_base.index', compact('knowledge'));
    }

    public function create()
    {
        return view('backend.Knowledge_base.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'category'    => 'required|string|in:home,service,about',
            'images.*'    => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $knowledge = Knowledge::create([
                'title'       => $request->title,
                'description' => $request->description,
                'category'    => $request->category,
                'is_active'   => true,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('knowledge', 'public');
                    $knowledge->images()->create([
                        'path' => $path,
                    ]);
                }
            }
        });

        return redirect()->route('admin.knowledge-base.index')
            ->with('success', 'Knowledge created successfully!');
    }

    public function edit(Knowledge $knowledge)
    {
        $knowledge->load('images');
        return view('backend.Knowledge_base.edit', compact('knowledge'));
    }
    
    // app/Http/Controllers/Admin/KnowledgeBaseController.php

public function show(Knowledge $knowledge)
{
    $knowledge->load('images');
    return view('backend.Knowledge_base.show', compact('knowledge'));
}
    
    

    public function update(Request $request, Knowledge $knowledge)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'category'    => 'required|string|in:home,service,about',
            'images.*'    => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        DB::transaction(function () use ($request, $knowledge) {
            $knowledge->update([
                'title'       => $request->title,
                'description' => $request->description,
                'category'    => $request->category,
            ]);

            if ($request->hasFile('images')) {
                // Optionally delete old images if you want to replace
                $knowledge->images()->delete();

                foreach ($request->file('images') as $file) {
                    $path = $file->store('knowledge', 'public');
                    $knowledge->images()->create([
                        'path' => $path,
                    ]);
                }
            }
        });

        return redirect()->route('admin.knowledge-base.index')
            ->with('success', 'Knowledge updated successfully!');
    }

    public function destroy(Knowledge $knowledge)
    {
        // Delete images from storage
        foreach ($knowledge->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $knowledge->delete();

        return redirect()->route('admin.knowledge-base.index')
            ->with('success', 'Knowledge deleted successfully!');
    }

    public function toggleStatus(Knowledge $knowledge)
    {
        $knowledge->update(['is_active' => !$knowledge->is_active]);
        return redirect()->route('admin.knowledge-base.index')
        
            ->with('success', 'Knowledge status updated successfully!');
    }
    
    
    
    
}
