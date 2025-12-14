<?php

namespace App\Exports;

use App\Models\Translator;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TranslatorsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Translator::with(['user'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'id',
            'user_id',
            'name',
            'email',
            'phone',
            'country_code',
            // Location Information
            'country',
            'city',
            'address',
            // Other fields
            'languages',
            'specializations',
            'bio',
            'hourly_rate',
            'availability',
            'experience_years',
            'profile_image',
            'is_verified',
            'verification_date',
            'rejection_reason',
            'is_available',
            'rating',
            'total_jobs',
            'paypal_email',
            'bank_account_number',
            'bank_name',
            'bank_routing_number',
            'bank_account_holder_name',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @param mixed $translator
     * @return array
     */
    public function map($translator): array
    {
        // Ensure JSON fields are exported as JSON strings to mirror the database representation
        $languagesJson = is_array($translator->languages) ? json_encode($translator->languages, JSON_UNESCAPED_UNICODE) : (string) $translator->languages;
        $specializationsJson = is_array($translator->specializations) ? json_encode($translator->specializations, JSON_UNESCAPED_UNICODE) : (string) $translator->specializations;
        $availabilityJson = is_array($translator->availability) ? json_encode($translator->availability, JSON_UNESCAPED_UNICODE) : (string) $translator->availability;

        return [
            $translator->id,
            $translator->user_id,
            $translator->name,
            $translator->email,
            $translator->phone,
            $translator->country_code,
            // Location Information
            $translator->country,
            $translator->city,
            $translator->address,
            // Other fields
            $languagesJson,
            $specializationsJson,
            $translator->bio,
            $translator->hourly_rate,
            $availabilityJson,
            $translator->experience_years,
            $translator->profile_image,
            (int) $translator->is_verified,
            $translator->verification_date ? $translator->verification_date->toDateTimeString() : null,
            $translator->rejection_reason,
            (int) $translator->is_available,
            $translator->rating,
            $translator->total_jobs,
            $translator->paypal_email,
            $translator->bank_account_number,
            $translator->bank_name,
            $translator->bank_routing_number,
            $translator->bank_account_holder_name,
            $translator->created_at ? $translator->created_at->toDateTimeString() : null,
            $translator->updated_at ? $translator->updated_at->toDateTimeString() : null,
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