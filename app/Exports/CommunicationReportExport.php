<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CommunicationReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $type;

    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Recipient Name',
            'Recipient Email',
            'Role',
            'Delivered At',
            'Read At',
            'Status',
        ];
    }

    public function map($row): array
    {
        $user = $this->type === 'message' ? $row->recipient : $row->user;
        
        return [
            $user->name ?? 'Unknown',
            $user->email ?? 'N/A',
            $user->getRoleNames()->first() ?? 'User',
            $row->created_at->format('Y-m-d H:i:s'),
            $row->read_at ? $row->read_at->format('Y-m-d H:i:s') : '-',
            $row->read_at ? 'Read' : 'Pending',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
