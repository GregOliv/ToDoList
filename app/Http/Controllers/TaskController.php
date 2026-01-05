<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Menampilkan daftar semua tugas milik pengguna yang sedang login.
     */
    public function index(Request $request)
    {
        // Ambil semua tugas yang user_id-nya sesuai dengan ID user yang sedang login
        $tasks = $request->user()->tasks()->with('category')->orderBy('id', 'desc')->get();
        return response()->json($tasks);
    }

    /**
     * Menyimpan tugas baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|in:low,medium,high',
            'deadline' => 'nullable|date|after_or_equal:today',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $task = $request->user()->tasks()->create($validated);

        return response()->json($task, 201); // 201 Created
    }

    /**
     * Memperbarui tugas yang sudah ada.
     */
    public function update(Request $request, Task $task)
    {
        // Cek otorisasi: Pastikan task milik user yang sedang login
        if ($request->user()->id !== $task->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'required|boolean',
            'priority' => 'nullable|string|in:low,medium,high',
            'deadline' => 'nullable|date|after_or_equal:today',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    /**
     * Menghapus tugas.
     */
    public function destroy(Request $request, Task $task)
    {
        // Cek otorisasi
        if ($request->user()->id !== $task->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted'], 200);
    }
}