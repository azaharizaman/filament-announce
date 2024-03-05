<?php

namespace Rupadana\FilamentAnnounce\Resources;

use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Support\Facades\FilamentColor;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Guava\FilamentIconPicker\Tables\IconColumn;
use Rupadana\FilamentAnnounce\Models\Announcement;
use Rupadana\FilamentAnnounce\Resources\AnnouncementResource\Pages;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail')
                    ->aside()
                    ->description('Announcement detail')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->minLength(5)
                            ->required(),
                        TextInput::make('title')
                            ->minLength(5)
                            ->required(),
                        Textarea::make('body')
                            ->minLength(20)
                            ->required()
                            ->columnSpanFull(),
                        IconPicker::make('icon')
                            ->preload()
                            ->default('heroicon-o-megaphone'),
                        Select::make('color')
                            ->options([
                                ...collect(FilamentColor::getColors())->map(fn ($value, $key) => ucfirst($key))->toArray(),
                                'custom' => 'Custom',
                            ])
                            ->live(),
                        ColorPicker::make('custom_color')
                            ->hidden(fn (Get $get) => $get('color') != 'custom')
                            ->requiredIf('color', 'custom')
                            ->rgb(),

                        Select::make('users')
                            ->options([
                                'all' => 'all',
                                ...User::all()->pluck('name', 'id'),
                            ])
                            ->multiple()
                            ->required(),
                        Select::make('organization_id')
                            ->relationship(name: 'organization', titleAttribute:'name'),
                    ])
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('title'),
                TextColumn::make('body'),
                IconColumn::make('icon'),
                TextColumn::make('organization.name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'view' => Pages\ViewAnnouncement::route('/{record}'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-announce.navigation.group');
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(config('filament-announce.can_access.role') ?? []);
    }
}
