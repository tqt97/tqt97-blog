<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Comment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CommentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CommentResource\RelationManagers;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'blogs';

    protected static ?string $recordTitleAttribute = 'name';

    protected static int $globalSearchResultsLimit = 5;

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('blogs');
    }

    public static function getNavigationLabel(): string
    {
        return __('comments');
    }

    public static function getModelLabel(): string
    {
        return __('comments');
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
                        Forms\Components\Select::make('user_id')
                            ->label(__('resources/comment.user_post'))
                            ->relationship('user', 'name')
                            ->preload()
                            ->required()
                            ->disabled(fn (?string $operation, ?Model $record) => $operation == 'edit')
                            ->columnSpan(6),
                        Forms\Components\Select::make('post_id')
                            ->label(__('resources/comment.post'))
                            ->relationship('post', 'title')
                            ->preload()
                            ->required()
                            ->disabled(fn (?string $operation, ?Model $record) => $operation == 'edit')
                            ->columnSpan(6),
                        Forms\Components\Textarea::make('content')
                            ->label(__('resources/comment.content'))
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label(__('resources/comment.published_at'))
                            ->columnSpan(6),
                    ])->columns(12)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('post.title')
                    ->label(__('resources/tables.post_title'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('resources/tables.comment_user'))
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                // Tables\Columns\TextColumn::make('post_count')
                //     ->label(__('resources/tables.posts_count'))
                //     ->badge()
                //     ->alignCenter()
                //     ->counts('post')
                //     ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('resources/tables.published_at'))
                    ->dateTime()
                    ->sortable(),
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
                Tables\Actions\EditAction::make(),
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
                Tables\Grouping\Group::make('post.title')
                    ->collapsible()
                    ->titlePrefixedWithLabel(true)
                    ->label(__('resources/tables.group_by_post_title')),
                Tables\Grouping\Group::make('user.name')
                    ->collapsible()
                    ->titlePrefixedWithLabel(true)
                    ->label(__('resources/tables.group_by_user_post')),
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
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
