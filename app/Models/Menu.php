<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'route_name',
        'icon',
        'parent_id',
        'order',
        'location',
        'is_active',
        'open_in_new_tab'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
    ];

    /**
     * Get the parent menu
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Get the child menus
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get active menus for a location
     */
    public static function getMenus($location = 'main')
    {
        return static::where('location', $location)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('order');
            }])
            ->orderBy('order')
            ->get();
    }

    /**
     * Get the URL for this menu item
     */
    public function getUrl()
    {
        if ($this->url) {
            return $this->url;
        }

        if ($this->route_name && \Route::has($this->route_name)) {
            return route($this->route_name);
        }

        return '#';
    }
}
