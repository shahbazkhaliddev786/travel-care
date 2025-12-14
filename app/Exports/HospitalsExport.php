<?php

namespace App\Exports;

use App\Models\Hospital;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HospitalsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Hospital::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Type',
            'Email',
            'Phone',
            'Website',
            'Country',
            'City',
            'State',
            'Postal Code',
            'Address',
            'Specialties',
            'Facilities',
            'Bed Count',
            'Emergency Services',
            'Pharmacy',
            'Operating Hours From',
            'Operating Hours To',
            'Operating Days',
            'Professional ID',
            'Is Verified',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param mixed $hospital
     * @return array
     */
    public function map($hospital): array
    {
        return [
            $hospital->id,
            $hospital->name ?? '',
            $hospital->type ?? '',
            $hospital->email ?? '',
            $hospital->country_code . ' ' . $hospital->phone ?? '',
            $hospital->website ?? '',
            $hospital->country ?? '',
            $hospital->city ?? '',
            $hospital->state ?? '',
            $hospital->postal_code ?? '',
            $hospital->address ?? '',
            $hospital->specialties ?? '',
            $hospital->facilities ?? '',
            $hospital->bed_count ?? '',
            $hospital->emergency_services ? 'Yes' : 'No',
            $hospital->pharmacy ? 'Yes' : 'No',
            $hospital->operating_hours_from ?? '',
            $hospital->operating_hours_to ?? '',
            is_array($hospital->operating_days) 
                ? implode(', ', $hospital->operating_days) 
                : $hospital->operating_days ?? '',
            $hospital->professional_id ?? '',
            $hospital->is_verified == 1 ? 'Approved' : ($hospital->is_verified == -1 ? 'Rejected' : 'Pending'),
            $hospital->created_at ? $hospital->created_at->setTimezone(config('app.timezone', 'UTC'))->format('M d, Y g:i A') : '',
            $hospital->updated_at ? $hospital->updated_at->setTimezone(config('app.timezone', 'UTC'))->format('M d, Y g:i A') : ''
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}