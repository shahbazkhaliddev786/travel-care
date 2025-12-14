<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Customer::with(['user'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Age',
            'Gender',
            'Weight',
            'Location',
            'Chronic Pathologies',
            'Allergies',
            'Chronic Medications',
            'Is Verified',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param mixed $customer
     * @return array
     */
    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->user->name ?? $customer->name ?? '',
            $customer->user->email ?? $customer->email ?? '',
            $customer->country_code . ' ' . $customer->phone ?? '',
            $customer->age ?? '',
            $customer->gender ?? '',
            $customer->weight ?? '',
            ($customer->city && $customer->country) ? $customer->city . ', ' . $customer->country : ($customer->city ?? $customer->country ?? ''),
            is_array($customer->chronic_pathologies) 
                ? implode(', ', $customer->chronic_pathologies) 
                : $customer->chronic_pathologies ?? '',
            is_array($customer->allergies) 
                ? implode(', ', $customer->allergies) 
                : $customer->allergies ?? '',
            is_array($customer->chronic_medications) 
                ? implode(', ', $customer->chronic_medications) 
                : $customer->chronic_medications ?? '',
            $customer->is_verified ? 'Yes' : 'No',
            $customer->created_at ? $customer->created_at->setTimezone(config('app.timezone', 'UTC'))->format('M d, Y g:i A') : '',
            $customer->updated_at ? $customer->updated_at->setTimezone(config('app.timezone', 'UTC'))->format('M d, Y g:i A') : ''
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
