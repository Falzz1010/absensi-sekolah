<?php

namespace App\Filament\Resources\MuridResource\Pages;

use App\Filament\Resources\MuridResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMurids extends ListRecords
{
    protected static string $resource = MuridResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('File Excel')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required()
                        ->helperText('Format kolom: nama | email | kelas | status'),
                ])
                ->action(function (array $data) {
                    try {
                        \Maatwebsite\Excel\Facades\Excel::import(
                            new \App\Imports\MuridImport,
                            $data['file']
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Import berhasil!')
                            ->body('Data murid berhasil diimport')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Import gagal!')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            
            Actions\Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\TemplateMuridExport,
                        'template_murid.xlsx'
                    );
                }),
            
            Actions\CreateAction::make(),
        ];
    }
}
