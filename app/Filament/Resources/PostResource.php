<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\View;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PostResource\RelationManagers;

class PostResource extends Resource
{
    protected static ?string $navigationGroup = 'blogs';

    protected static ?string $model = Post::class;

    // protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $recordTitleAttribute = 'title';

    protected static int $globalSearchResultsLimit = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('blogs');
    }

    public static function getNavigationLabel(): string
    {
        return __('posts');
    }

    public static function getModelLabel(): string
    {
        return __('posts');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('section_main_content'))
                    // ->description(__('section_main_content_description'))
                    ->icon('heroicon-m-computer-desktop')
                    ->collapsible()
                    ->persistCollapsed()
                    ->compact()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('resources/post.title'))
                            ->required()
                            ->maxLength(400)
                            ->live(onBlur: false, debounce: 300)
                            ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                                $set('slug', Str::slug($state));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->label(__('resources/post.slug'))
                            ->required()
                            ->maxLength(400)
                            ->disabled(fn (?string $operation, ?Model $record) => $operation == 'edit'),

                        Forms\Components\TextInput::make('excerpt')
                            ->label(__('resources/post.excerpt'))
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('content')
                            ->label(__('resources/post.content'))
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(8),
                Forms\Components\Section::make(__('section_meta_data'))
                    // ->description(__('section_meta_data_description'))
                    ->icon('heroicon-m-adjustments-horizontal')
                    ->collapsible()
                    ->persistCollapsed()
                    ->compact()
                    ->schema([
                        Forms\Components\Tabs::make('Tabs')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make(__('post_information'))
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')
                                            ->directory('posts')
                                            ->image()
                                            ->optimize('webp')
                                            ->imageEditor()
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9')
                                            ->imageResizeTargetWidth('640')
                                            ->imageResizeTargetHeight('480')
                                            ->openable()
                                            ->downloadable()
                                            ->imageEditorAspectRatios([
                                                null,
                                                '16:9',
                                                '4:3',
                                                '1:1',
                                            ])
                                            ->maxSize(2048),


                                        Forms\Components\Select::make('categories')
                                            ->label(__('resources/post.categories'))
                                            ->relationship('categories', 'name')
                                            ->preload()
                                            ->multiple()
                                            ->maxItems(5)
                                            ->searchable()
                                            ->required()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->label(__('resources/post.categories_name'))
                                                    ->hint(__('categories.name_hint'))
                                                    ->hintIcon('heroicon-m-information-circle')
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->autofocus()
                                                    ->minLength(3)
                                                    ->maxLength(255)
                                                    ->live(onBlur: false, debounce: 500)
                                                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                                                        $set('slug', Str::slug($state));
                                                    }),
                                                Forms\Components\TextInput::make('slug')
                                                    ->label(__('resources/post.categories_slug'))
                                                    ->hint(__('resources/post.categories_slug_hint'))
                                                    ->hintIcon('heroicon-m-information-circle')
                                                    ->required()
                                                    ->maxLength(255),
                                            ]),
                                        Forms\Components\Select::make('tags')
                                            ->label(__('resources/post.tags'))
                                            ->relationship('tags', 'name')
                                            ->preload()
                                            ->multiple()
                                            ->maxItems(5)
                                            ->searchable()
                                            ->required()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->label(__('resources/post.tags.name'))
                                                    ->required()
                                                    ->hint(__('tags.name_hint'))
                                                    ->hintIcon('heroicon-m-information-circle')
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->autofocus()
                                                    ->minLength(3)
                                                    ->maxLength(255)
                                                    ->live(onBlur: false, debounce: 500)
                                                    ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                                                        $set('slug', Str::slug($state));
                                                    })
                                                    ->minLength(2)
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('slug')
                                                    ->label(__('resources/post.tags.slug'))
                                                    ->required()
                                                    ->hint(__('tags.slug_hint'))
                                                    ->hintIcon('heroicon-m-information-circle')
                                                    ->maxLength(255),
                                            ]),

                                        Forms\Components\Toggle::make('featured')
                                            ->label(__('resources/post.featured'))
                                            ->required(),
                                        Forms\Components\Select::make('user_id')
                                            ->label(__('resources/post.author'))
                                            ->relationship('user', 'name')
                                            ->required(),
                                        Forms\Components\DateTimePicker::make('published_at')
                                            ->label(__('resources/post.published_at')),
                                    ]),
                                Forms\Components\Tabs\Tab::make(__('post_meta_seo'))
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title')
                                            ->label(__('resources/post.meta_title'))
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('meta_keywords')
                                            ->label(__('resources/post.meta_keywords'))
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('meta_description')
                                            ->label(__('resources/post.meta_description'))
                                            ->maxLength(255),
                                    ]),

                            ]),


                    ])->columnSpan(4),


            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('resources/tables.image'))
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('resources/tables.post_title'))
                    ->description(fn (Post $record): string => Str::limit($record->excerpt, 30))
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('comments_count')
                    ->label(__('resources/tables.comments_count'))
                    ->badge()
                    ->alignCenter()
                    ->counts('comments')
                    ->sortable(),
                Tables\Columns\TextColumn::make('post.tags')
                    // ->label(__('resources/tables.categories_count'))
                    // ->badge()
                    // ->alignCenter()
                    // ->counts('categories')
                    ->sortable()->listWithLineBreaks(),
                // Tables\Columns\TextColumn::make('tags_count')
                //     ->label(__('resources/tables.tags_count'))
                //     ->badge()
                //     ->alignCenter()
                //     ->counts('tags')
                //     ->sortable(),
                Tables\Columns\CheckboxColumn::make('featured')
                    ->label(__('resources/tables.featured'))
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('resources/tables.published_at'))
                    ->dateTime('D, d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('resources/tables.deleted_at'))
                    ->dateTime('D, d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Tables\Grouping\Group::make('created_at')
                    ->collapsible()
                    ->titlePrefixedWithLabel(true)
                    ->label(__('resources/tables.group_by_created_at')),
                Tables\Grouping\Group::make('user.name')
                    ->collapsible()
                    ->titlePrefixedWithLabel(true)
                    ->label(__('resources/tables.group_by_user_name')),
                // Tables\Grouping\Group::make('comments.count')
                //     ->collapsible()
                //     ->titlePrefixedWithLabel(true)
                //     ->label(__('resources/tables.group_by_comments_count')),
            ])
            ->groupingSettingsInDropdownOnDesktop()
            ->groupRecordsTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->button()
                    ->label(__('resources/tables.group_records')),
            );
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
