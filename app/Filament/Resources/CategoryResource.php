<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;


class CategoryResource extends Resource
{
    protected static ?string $navigationGroup = 'blogs';

    protected static ?string $model = Category::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    protected static int $globalSearchResultsLimit = 5;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('blogs');
    }

    public static function getNavigationLabel(): string
    {
        return __('categories');
    }

    public static function getModelLabel(): string
    {
        return __('categories');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make([
                    'sm' => 1
                ])->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('resources/categories.name'))
                        ->hint(__('resources/categories.name_hint'))
                        ->hintIcon('heroicon-m-information-circle')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->autofocus()
                        ->minLength(3)
                        ->maxLength(255)
                        ->live(onBlur: false, debounce: 300)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $operation, ?string $old, ?string $state, ?Model $record) {
                            // if ($operation == 'edit' && $record->isPublished()) {
                            //     return;
                            // }

                            // if (($get('slug') ?? '') !== Str::slug($old)) {
                            //     return;
                            // }
                            $set('slug', Str::slug($state));
                        }),
                    Forms\Components\TextInput::make('slug')
                        ->label(__('resources/categories.slug'))
                        ->hint(__('resources/categories.slug_hint'))
                        ->hintIcon('heroicon-m-information-circle')
                        ->required()
                        ->maxLength(255)
                        // ->unique(Article::class, 'slug', fn ($record) => $record)
                        ->disabled(fn (?string $operation, ?Model $record) => $operation == 'edit'),
                    // ->disabled(fn (?string $operation, ?Model $record) => $operation == 'edit' && $record->isPublished()),

                    Forms\Components\Toggle::make('published')
                        ->label(__('resources/categories.published')),
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('resources/tables.name'))
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('resources/tables.slug'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('posts_count')
                    ->label(__('resources/tables.posts_count'))
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->counts('posts')
                    ->sortable(),
                Tables\Columns\IconColumn::make('published')
                    ->label(__('resources/tables.published'))
                    ->boolean()->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources/tables.created_at'))
                    ->dateTime('D, d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('resources/tables.updated_at'))
                    ->dateTime('D, d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Tables\Grouping\Group::make('created_at')
                    ->collapsible()
                    ->titlePrefixedWithLabel(true)
                    ->label(__('resources/tables.group_by_created_at')),
            ])
            ->groupingSettingsInDropdownOnDesktop()
            ->groupRecordsTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->button()
                    ->label(__('resources/tables.group_records')),
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCategories::route('/'),
        ];
    }
}
