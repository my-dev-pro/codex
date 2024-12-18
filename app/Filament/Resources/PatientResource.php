<?php

namespace App\Filament\Resources;

use App\Enum\Gender;
use App\Enum\Nationlity;
use App\Enum\Role;
use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-s-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Patient Name')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required(),
                        Forms\Components\TextInput::make('middle_name'),
                        Forms\Components\TextInput::make('last_name')
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Fieldset::make('Metadata')
                    ->schema([
                        Forms\Components\Select::make('nationality')
                            ->options(Nationlity::class)
                            ->default('Egyptian'),
                        Forms\Components\TextInput::make('national_id')
                            ->label('National ID')
                            ->required(),
                        Forms\Components\Section::make('Others')
                            ->schema([
                                Forms\Components\Select::make('gender')
                                    ->options(Gender::class)
                                    ->searchable()
                                    ->required(),

                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->maxDate(now())
                                    ->required(),

                                Forms\Components\Select::make('created_by')
                                    ->relationship('doctor', 'name')
                                    ->searchable()
                                    ->visible(fn () => in_array(Auth()->user()->role, [Role::ADMIN->value, Role::MODERATOR->value, Role::SUPER_MODERATOR->value])) // role
                                    ->required(),
                            ])
                            ->columns(3),
                    ])
                    ->columns(2),

                Forms\Components\Fieldset::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('telephone')
                            ->tel(),
                        Forms\Components\TextInput::make('mobile'),
                        Forms\Components\TextInput::make('email')
                            ->email(),
                        Forms\Components\Textarea::make('address')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->getStateUsing(function ($record) {
                        return implode(' ', array_filter([
                            $record->first_name,
                            $record->middle_name,
                            $record->last_name,
                        ]));
                    })
                    ->formatStateUsing(fn ($state) => ucwords($state)),
                Tables\Columns\TextColumn::make('telephone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->label('Doctor Name')
                    ->getStateUsing(function ($record) {
                        return implode(' ', array_filter([
                            $record->doctor->name,
                        ]));
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                //
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation(),
                ])
                ->visible(in_array(Auth()->user()->role, [Role::ADMIN->value, Role::SUPER_MODERATOR->value])),
            ]);

    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }

    // show patients of current doctor
    public static function getEloquentQuery(): Builder
    {
        if (in_array(Auth()->user()->role, [Role::ADMIN->value, Role::SUPER_MODERATOR->value])) {
            return parent::getEloquentQuery();
        }
        return parent::getEloquentQuery()->where('created_by', Auth()->user()->getAuthIdentifier());
    }
}
