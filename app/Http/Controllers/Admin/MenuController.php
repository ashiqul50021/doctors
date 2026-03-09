<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display menu listing
     */
    public function index()
    {
        $menus = Menu::with('children')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $parentMenus = Menu::whereNull('parent_id')->orderBy('order')->get();
        return view('admin.menus.create', compact('parentMenus'));
    }

    /**
     * Store new menu
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'route_name' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer',
            'location' => 'required|in:main,footer',
        ]);

        Menu::create([
            'title' => $request->title,
            'url' => $request->url,
            'route_name' => $request->route_name,
            'icon' => $request->icon,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'location' => $request->location,
            'is_active' => $request->has('is_active'),
            'open_in_new_tab' => $request->has('open_in_new_tab'),
        ]);

        return redirect()->route('admin.menus.index')->with('success', 'Menu created successfully!');
    }

    /**
     * Show edit form
     */
    public function edit(Menu $menu)
    {
        $parentMenus = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->orderBy('order')
            ->get();

        return view('admin.menus.edit', compact('menu', 'parentMenus'));
    }

    /**
     * Update menu
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'route_name' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer',
            'location' => 'required|in:main,footer',
        ]);

        $menu->update([
            'title' => $request->title,
            'url' => $request->url,
            'route_name' => $request->route_name,
            'icon' => $request->icon,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'location' => $request->location,
            'is_active' => $request->has('is_active'),
            'open_in_new_tab' => $request->has('open_in_new_tab'),
        ]);

        return redirect()->route('admin.menus.index')->with('success', 'Menu updated successfully!');
    }

    /**
     * Delete menu (via GET for when JS fails)
     */
    public function delete(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted successfully!');
    }

    /**
     * Delete menu
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted successfully!');
    }
}
