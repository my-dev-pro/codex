<?php

namespace App\Filament\Resources;

use App\Enum\Gender;
use App\Enum\Nationlity;
use App\Enum\Role;
use App\Enum\SponsoredTest;
use App\Enum\Test;
use App\Enum\TestFollowUp;
use App\Filament\Resources\TestRequestResource\Pages;
use App\Filament\Resources\TestRequestResource\RelationManagers;
use App\Models\Patient;
use App\Models\TestRequest;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class TestRequestResource extends Resource
{
    protected static ?string $model = TestRequest::class;

    protected static ?string $navigationIcon = 'heroicon-s-folder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Forms\Components\Section::make([
                        Forms\Components\Select::make('test_type')
                            ->label('Test Type')
                            ->options([
                                'private' => 'Private',
                                'sponsored' => 'Sponsored',
                            ])
                            ->inlineLabel()
                            ->columns(2)
                            ->live()
                            ->required(),

                        Forms\Components\Select::make('name')
                            ->label('Test Name')
                            ->options(function (Get $get) {
                                $test_type = $get('test_type');
                                return match($test_type) {
                                    'private' => Test::class,
                                    'sponsored' => SponsoredTest::class,
                                    default => [],
                                };
                            })
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('doctor_id')
                            ->label('Doctor Name')
                            ->relationship('doctorInfo', 'name')
                            ->native(false)
                            ->searchable(['name', 'mobile'])
                            ->default(in_array(Auth()->user()->role, ['doctor']) ? Auth()->user()->getAuthIdentifier() : '')
                            ->disabled(in_array(Auth()->user()->role, ['doctor']))
                            ->live()
                            ->required(),

                        Forms\Components\Select::make('patient_id')
                            ->label('Patient Name')
                            ->options(function (Get $get) {
                                $doctor = $get('doctor_id');
                                return Patient::where('created_by', $doctor)
                                    ->get()
                                    ->mapWithKeys(function (Patient $patient) {
                                        return [$patient->id => "{$patient->first_name} {$patient->middle_name} {$patient->last_name}"];
                                    });
                            })
                            ->formatStateUsing(function ($state) {
                                // Fetch the patient name from the database based on the state (patient_id)
                                $patient = Patient::find($state);
                                return $patient ? "{$patient->first_name} {$patient->middle_name} {$patient->last_name}" : null;
                            })
                            ->createOptionForm([
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
                                                    ->label('Doctor Name')
                                                    ->options(
                                                        User::where('role', Role::DOCTOR->value)
                                                        ->pluck('name', 'id')
                                                    )
                                                    ->native(false)
                                                    ->searchable()
                                                    ->visible(fn () => in_array(Auth()->user()->role, [Role::ADMIN->value, Role::MODERATOR->value, Role::SUPER_MODERATOR->value,])) // role
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
                            ])
                            ->createOptionUsing(function ($data) {
                                // Create a new patient record using the form data
                                $patient = Patient::create($data);
                                return $patient->id; // Return the ID of the newly created patient
                            })
                            ->searchable(['first_name', 'middle_name', 'last_name','mobile', 'telephone', 'email', 'national_id'])
                            ->required(),

                        Forms\Components\Textarea::make('note')
                            ->columnSpanFull(),

                        Forms\Components\Section::make('Notifications')
                            ->description('Selected will be notified by the system when test result uploaded.')
                            ->relationship('notifications')
                            ->schema([
                                Forms\Components\CheckboxList::make('receiver')
                                ->options([
                                    'patient' => 'Patient',
                                    Role::DOCTOR->value => ucfirst(Role::DOCTOR->value),
                                ])
                                ->columns(2)
                            ])->aside(),
                    ]),

                    Forms\Components\Section::make([
                        Forms\Components\Section::make([
                            Forms\Components\Select::make('status')
                                ->options(TestFollowUp::class)
                                ->default(TestFollowUp::NEW)
                                ->searchable()
                                ->required(),

                            Forms\Components\ToggleButtons::make('is_paid')
                                ->label('Paid')
                                ->boolean()
                                ->inline()
                                ->default(false)
                                ->grouped()
                                ->visible(fn () =>  in_array(Auth()->user()->role, [Role::ADMIN->value, Role::MODERATOR->value, Role::SUPER_MODERATOR->value,] )) // roles
                                ->required(),
                        ]),


                        Forms\Components\Section::make('Test Results')
                            ->relationship('results')
                            ->schema([
                                Forms\Components\FileUpload::make('result_path')
                                    ->label('Result Report')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->disk('public')
                                    ->visibility('public')
                                    ->getUploadedFileNameForStorageUsing(
                                        fn (TemporaryUploadedFile $file, $record): string => (string) str($file->storeAs('results', $record->test_id . '.pdf')),
                                    )
                                    ->downloadable()
                                    ->deletable(fn() => in_array(Auth()->user()->role, [Role::ADMIN->value, Role::GENETICIST->value, Role::SUPER_MODERATOR->value,]))
                                    ->openable(),

                                Forms\Components\Textarea::make('note')
                                    ->label('Result Report Note')
                                    ->helperText('This note is visible by doctors and moderators.')
                                    ->columnSpanFull(),
                            ])
                            ->visible( in_array(Auth()->user()->role, [Role::ADMIN->value, Role::GENETICIST->value, Role::DOCTOR->value, Role::SUPER_MODERATOR->value,]) ),

                    ])
                        ->visible(fn () =>  in_array(Auth()->user()->role, [Role::ADMIN->value, Role::MODERATOR->value, Role::GENETICIST->value, Role::SUPER_MODERATOR->value,] )) // roles
                        ->aside(),

                ])->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Test ID')
                    ->searchable(),

                Tables\Columns\TextColumn::make('patient_id')
                    ->label('Patient Name')
                    ->getStateUsing(function ($record) {
                        return implode(' ', array_filter([
                            $record->patient->first_name,
                            $record->patient->middle_name,
                            $record->patient->last_name,
                        ]));
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Test Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('doctor_id')
                    ->label('Doctor Name')
                    ->getStateUsing(function ($record) {
                        return implode(' ', array_filter([
                            $record->doctor->name,
                        ]));
                    })
                    ->searchable(),


                Tables\Columns\TextColumn::make('status')
                    ->badge()
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
                SelectFilter::make('status')
                ->options(TestFollowUp::class)
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation(),
                ])
                    ->visible(in_array(Auth()->user()->role, [Role::ADMIN->value,])),
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
            'index' => Pages\ListTestRequests::route('/'),
            'create' => Pages\CreateTestRequest::route('/create'),
            'edit' => Pages\EditTestRequest::route('/{record}/edit'),
        ];
    }

    // show patients of current doctor
    public static function getEloquentQuery(): Builder
    {
        if (in_array(Auth()->user()->role, [Role::ADMIN->value,Role::MODERATOR->value, Role::GENETICIST->value, Role::SUPER_MODERATOR->value,])) {
            return parent::getEloquentQuery();
        }
        return parent::getEloquentQuery()->where('doctor_id', Auth()->user()->getAuthIdentifier());
    }
}
