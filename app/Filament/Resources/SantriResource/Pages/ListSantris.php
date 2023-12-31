<?php

namespace App\Filament\Resources\SantriResource\Pages;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\kamar;
use App\Models\Kelas;
use App\Models\santri;
use Filament\Pages\Actions;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\SantriResource;
use App\Filament\Resources\SantriResource\Widgets\SantriStatsOverview;

class ListSantris extends ListRecords
{
    protected static string $resource = SantriResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Santri'),

            Actions\Action::make('export')
                ->label('Print Laporan Santri Baru')
                ->icon('heroicon-s-document-download')
                ->form([
            Forms\Components\Select::make('kamar_id')
            ->options([
            'all' => 'Semua Kamar', // Tambahkan opsi "Semua Kamar" di sini
            ] + kamar::all()->pluck('nama_kamar', 'id')->toArray())
            ->required(),

    ])
    ->action(function (array $data) {
        $filename = 'Laporan Data Santri Baru_' . now()->toDateTimeString() . '.pdf';
        $kamar_nama = ''; // Variabel diinisialisasi di luar blok kondisional

        // Check if "Semua Kamar" is selected
        if ($data['kamar_id'] === 'all') {
            // Query to get all 'santri' records for all rooms
            $santriRecords = Santri::all();
            $kamarRecords = Kamar::all();
            $kamar_nama = 'Semua Kamar'; // Nama kamar untuk PDF
        } else {
            // Query to get all 'santri' records for the specific room
            $santriRecords = Santri::where('kamar_id', $data['kamar_id'])->get();
            $kamar_nama = kamar::find($data['kamar_id'])->nama_kamar;
        }

                    // count total rows displayed
                    $totalRows = $santriRecords->count();

                    $pdf = PDF::loadView('pdf_santri', [
                        'records' => $santriRecords,$kamar_nama,
                        'totalRows' => $totalRows,
                        'kamar_id' => $data['kamar_id'],
                        // 'kelas_id' => $data['kelas_id'],
                        'kamar_nama' => $kamar_nama,
                        // 'kelas_nama' => $kelas_nama,
                    ])->output();

                    return response()->streamDownload(fn () => print($pdf), $filename);
                })

        ];
    }

    // protected function getHeaderWidgets():array
    // {
    //     return [
    //         SantriStatsOverview::class,
    //     ];
    // }

     // Actions\Action::make('export')
            //     ->label('Print Laporan Santri Baru')
            //     ->icon('heroicon-s-document-download')
            //     ->form([
            //         Forms\Components\Select::make('kamar_id')
            //             ->options(kamar::all()->pluck('nama_kamar', 'id')->toArray())
            //             ->required(),
            //         Forms\Components\Select::make('kelas_id')
            //             ->options(Kelas::all()->pluck('nama_kelas', 'id')->toArray())
            //             ->required(),
            //     ])
            //     ->action(function (array $data) {
            //         $filename = 'export_' . now()->toDateTimeString() . '.pdf';

            //         // query to get all 'santri' records for the specific room and class
            //         $santriRecords = Santri::where('kamar_id', $data['kamar_id'])
            //             ->where('kelas_id', $data['kelas_id'])
            //             ->get();

            //         $kamar_nama = kamar::find($data['kamar_id'])->nama_kamar;
            //         $kelas_nama = Kelas::find($data['kelas_id'])->nama_kelas;
}
