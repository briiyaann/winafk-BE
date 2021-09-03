<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Status;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use SimpleSquid\Nova\Fields\AdvancedNumber\AdvancedNumber;
use Sloveniangooner\SearchableSelect\SearchableSelect;

class Withdraw extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Core\Withdraw::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
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
            SearchableSelect::make('User', 'user_id')
                ->resource('users')
                ->hideFromIndex()
                ->label('email')
                ->value('id')
                ->required(),
            AdvancedNumber::make('Amount')
                ->sortable()
                ->thousandsSeparator(',')
                ->min(0)
                ->rules('required', 'max:254'),
            Text::make('Phone')
                ->hideFromIndex()
                ->rules('required_without:email'),
            Text::make('Email')
                ->hideFromIndex()
                ->rules('required_without:phone'),
            Status::make('Status')
                ->loadingWhen(['pending'])
                ->failedWhen(['denied']),
            Select::make('Status')
                ->default('pending')
                ->hideFromIndex()
                ->hideFromDetail()
                ->options([
                    'pending'   => 'Pending',
                    'denied'    => 'Denied',
                    'approved'  => 'Approved'
                ]),
            BelongsTo::make('Approved By', 'user', 'App\Nova\User')
                ->nullable()
                ->display(function($user) {
                    return $user->firstname . ' ' . $user->lastname;
                })
                ->withoutTrashed(),
            Textarea::make('reason')
                ->hideFromIndex()
                ->rules('required_if:status,denied')
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

    /**
     * The resource label
     *
     */

    public static function label()
    {
        return __('Cashouts');
    }

    /**
     * Singular resource label
     */

    public static function singularLabel()
    {
        return __('Cashout');
    }

    public static function relatableUsers(NovaRequest $request, $query)
    {
        return $query->where('user_role', 2);
    }
}
