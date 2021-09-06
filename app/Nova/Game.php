<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class Game extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Core\Game::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return "Schedule: {$this->schedule}";
    }

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
            ID::make(__('ID'), 'id')
                ->sortable(),
            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),
            BelongsTo::make('Game Type', 'game_type', 'App\Nova\GameType')
                ->sortable()
                ->hideFromIndex()
                ->display(function($game_type) {
                    return $game_type->name;
                })
                ->withoutTrashed(),
            BelongsTo::make('League', 'league', 'App\Nova\League')
                ->sortable()
                ->hideFromIndex()
                ->display(function($game_type) {
                    return $game_type->name;
                })
                ->withoutTrashed(),
            DateTime::make('Schedule')->sortable()->rules('required'),
            Number::make('Fee')
                ->rules('required'),
            Number::make('Match Count')
                ->rules('required'),
            Select::make('Status')
                ->options([
                    'settled'   => 'Settled',
                    'upcoming'  => 'Upcoming',
                    'ongoing'   => 'Ongoing',
                    'draw'      => 'Draw',
                    'cancelled' => 'Cancelled'
                ]),
            Textarea::make('label')
                ->required()
                ->hideFromIndex(),

            Text::make('Current Round')
                ->hideFromIndex()
                ->hideFromDetail()
                ->hideWhenCreating()
                ->help('Please do not edit if necessary.'),
            Text::make('Ended Round')
                ->hideFromIndex()
                ->hideFromDetail()
                ->hideWhenCreating()
                ->help('Please do not edit if necessary.'),
            HasMany::make('Match Submatches', 'matchSubmatch', 'App\Nova\MatchSubmatch'),
            HasMany::make('Match Winners')

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

    public static function authorizedToCreate(Request $request)
    {
        return true;
    }

    /**
     * The resource label
     *
     */

    public static function label()
    {
        return __('Matches');
    }

    /**
     * Singular resource label
     */

    public static function singularLabel()
    {
        return __('Match');
    }
}
