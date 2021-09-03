<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use SimpleSquid\Nova\Fields\AdvancedNumber\AdvancedNumber;
use Sloveniangooner\SearchableSelect\SearchableSelect;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Core\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @return mixed
     * @var string
     */
    public function title()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return "Username: {$this->username}";
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'firstname', 'lastname', 'email', 'username',
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
            ID::make()->sortable(),

            Text::make('Firstname')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Lastname')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Username')
                ->sortable()
                ->rules('required', 'max:255')
                ->updateRules('nullable'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:email')
                ->updateRules('nullable'),

            AdvancedNumber::make('Coins')
                ->thousandsSeparator(',')
                ->min(0)
                ->rules('required', 'max:254'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),
            Avatar::make('Avatar')
                ->disk('public')
                ->path('user_profile')
                ->storeAs(function (Request $request) {
                    $extension = $request->file('avatar')->extension();
                    return sha1($request->avatar->getClientOriginalName()) . '.' . $extension;
                }),
            Select::make('User Role')
                ->hideFromIndex()
                ->options([
                    '1' => 'User',
                    '2' => 'Admin',
                    '3' => 'Match Manager'
                ]),
            Text::make('Referral Code')
                ->hideFromDetail()
                ->hideFromIndex(),
            Boolean::make('Approved Referral Code')
                ->hideFromIndex()
                ->trueValue(1)
                ->falseValue(0)
                ->help('Please check if you input referral code'),
            Select::make('User Type ID')
                ->hideFromIndex()
                ->options([
                    '1' => 'Silver Account',
                    '2' => 'Gold Account'
                ])
                ->rules('required_with:referral_code')
                ->nullable(),
            SearchableSelect::make('Reference ID', 'reference_id')
                ->resource('users')
                ->hideFromIndex()
                ->help('If the user uses referral code from other user, please input the ID of the user.')
                ->label('email')
                ->value('id')
                ->nullable()
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
        return false;
    }
}
