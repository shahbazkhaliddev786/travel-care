<?php

namespace App\Exports;

use App\Models\Laboratory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaboratoriesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Laboratory::with(['user'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'User ID',
            'Name',
            'Email',
            'Phone',
            'Country Code',
            'License Number',
            'License Scan',
            'Address',
            'City',
            'Country',
            'Specialization',
            'Bio',
            'Consultation Fee',
            'Messaging Fee',
            'Video Call Fee',
            'House Visit Fee',
            'Voice Call Fee',
            'Working Hours From',
            'Working Hours To',
            'Working Days',
            'Years of Experience',
            'Working Location',
            'Profile Image',
            'Gallery Images',
            'Is Verified',
            'Verification Date',
            'Rejection Reason',
            'Can Video Consult',
            'PayPal Email',
            'Bank Account Number',
            'Bank Name',
            'Bank Routing Number',
            'Bank Account Holder Name',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param mixed $laboratory
     * @return array
     */
    public function map($laboratory): array
    {
        return [
            $laboratory->id,
            $laboratory->user_id ?? '',
            $laboratory->name ?? '',
            $laboratory->user->email ?? $laboratory->email ?? '',
            $laboratory->phone ?? '',
            $laboratory->country_code ?? '',
            $laboratory->license_number ?? '',
            $laboratory->license_scan ?? '',
            $laboratory->address ?? '',
            $laboratory->city ?? '',
            $laboratory->country ?? '',
            $laboratory->specialization ?? '',
            $laboratory->bio ?? '',
            $laboratory->consultation_fee ?? '',
            $laboratory->messaging_fee ?? '',
            $laboratory->video_call_fee ?? '',
            $laboratory->house_visit_fee ?? '',
            $laboratory->voice_call_fee ?? '',
            $laboratory->working_hours_from ?? '',
            $laboratory->working_hours_to ?? '',
            is_array($laboratory->working_days) 
                ? implode(', ', $laboratory->working_days) 
                : $laboratory->working_days ?? '',
            $laboratory->years_of_experience ?? '',
            $laboratory->working_location ?? '',
            $laboratory->profile_image ?? '',
            is_array($laboratory->gallery_images) 
                ? implode(', ', $laboratory->gallery_images) 
                : $laboratory->gallery_images ?? '',
            $laboratory->is_verified ? 'Yes' : 'No',
            $laboratory->verification_date ? $laboratory->verification_date->format('M d, Y g:i A') : '',
            $laboratory->rejection_reason ?? '',
            $laboratory->can_video_consult ? 'Yes' : 'No',
            $laboratory->paypal_email ?? '',
            $laboratory->bank_account_number ?? '',
            $laboratory->bank_name ?? '',
            $laboratory->bank_routing_number ?? '',
            $laboratory->bank_account_holder_name ?? '',
            $laboratory->created_at ? $laboratory->created_at->setTimezone(config('app.timezone', 'UTC'))->format('M d, Y g:i A') : '',
            $laboratory->updated_at ? $laboratory->updated_at->setTimezone(config('app.timezone', 'UTC'))->format('M d, Y g:i A') : ''
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