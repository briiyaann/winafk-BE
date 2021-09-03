<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class League extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Core\League::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make('name')->sortable()->required(),
            Text::make('Fee')->sortable()->hideFromIndex()->required(),
            Image::make('Background')
                ->disk('public')
                ->path('leagues/background')
                ->required()
                ->storeAs(function (Request $request) {
                    $extension = $request->file('background')->extension();
                    return Str::random(40) . '.' . $extension;
                }),
            Image::make('Banner')
                ->disk('public')
                ->path('leagues/banner')
                ->required()
                ->storeAs(function (Request $request) {
                    $extension = $request->file('banner')->extension();
                    return Str::random(40) . '.' . $extension;
                }),
            BelongsTo::make('Game Type', 'leagueGameType', 'App\Nova\GameType')
                ->hideFromIndex()
                ->required()
                ->display(function($game_type) {
                    return $game_type->name;
                })
                ->withoutTrashed(),
            Boolean::make('Is Active')
                ->trueValue(1)
                ->falseValue(0),
            Textarea::make('Description')
                ->hideFromIndex()
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
