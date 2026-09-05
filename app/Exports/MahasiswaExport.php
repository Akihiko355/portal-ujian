<?php

namespace App\Exports;

use App\Models\ExamSchedule;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected ExamSchedule $schedule;

    public function __construct(protected int $departmentId, int $examScheduleId)
    {
        $this->schedule = ExamSchedule::with(['subject', 'department'])->findOrFail($examScheduleId);
    }

    public function query()
    {
        return User::query()
            ->where('department_id', $this->departmentId)
            ->with('department')
            ->orderBy('name');
    }

    public function headings(): array
    {
        return ['Nama', 'Email', 'Telepon', 'Departemen', 'Password', 'Nomor Ujian'];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->phone,
            $user->department?->name ?? '',
            '',
            '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'Mata Kuliah: ' . $this->schedule->subject->name);
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'Departemen: ' . $this->schedule->department->name);
        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue('A3', 'Tanggal Ujian: ' . $this->schedule->exam_start_datetime->format('d M Y H:i') . ' - ' . $this->schedule->exam_end_datetime->format('H:i'));
        $sheet->mergeCells('A4:F4');
        $sheet->setCellValue('A4', 'Briefing: ' . $this->schedule->briefing_datetime->format('d M Y H:i'));

        $sheet->getStyle('A1:F4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
        ]);

        $sheet->getStyle('A6:F6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(18);

        return [];
    }
}
