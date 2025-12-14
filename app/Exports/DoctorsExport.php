<?php

namespace App\Exports;

use App\Models\Doctor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DoctorsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Doctor::with(['user'])->get();
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
            'Professional ID',
            'Specialization',
            'Type',
            'City',
            'Years of Experience',
            'Consultation Fee',
            'Messaging Fee',
            'Video Call Fee',
            'Voice Call Fee',
            'House Visit Fee',
            'Working Hours From',
            'Working Hours To',
            'Working Days',
            'Working Location',
            'Payment Methods',
            'PayPal Email',
            'Is Verified',
            'Can Video Consult',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param mixed $doctor
     * @return array
     */
    public function map($doctor): array
    {
        return [
            $doctor->id,
            $doctor->user->name ?? $doctor->name ?? '',
            $doctor->user->email ?? $doctor->email ?? '',
            $doctor->country_code . ' ' . $doctor->phone ?? '',
            $doctor->professional_id ?? '',
            $doctor->specialization ?? '',
            $doctor->type ?? '',
            $doctor->city ?? '',
            $doctor->years_of_experience ?? '',
            $doctor->consultation_fee ?? '',
            $doctor->messaging_fee ?? '',
            $doctor->video_call_fee ?? '',
            $doctor->voice_call_fee ?? '',
            $doctor->house_visit_fee ?? '',
            $doctor->working_hours_from ?? '',
            $doctor->working_hours_to ?? '',
            is_array($doctor->working_days) 
                ? implode(', ', $doctor->working_days) 
                : $doctor->working_days ?? '',
            $doctor->working_location ?? '',
            is_array($doctor->payment_methods) 
                ? implode(', ', $doctor->payment_methods) 
                : $doctor->payment_methods ?? '',
            $doctor->paypal_email ?? '',
            $doctor->is_verified ? 'Yes' : 'No',
            $doctor->can_video_consult ? 'Yes' : 'No',
            $doctor->created_at ? $doctor->created_at->setTimezone(config('app.timezone', 'UTC'))->format('M d, Y g:i A') : '',
            $doctor->updated_at ? $doctor->updated_at->setTimezone(config('app.timezone', 'UTC'))->format('M d, Y g:i A') : ''
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