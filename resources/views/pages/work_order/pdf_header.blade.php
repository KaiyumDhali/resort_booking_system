@php
    $imagePath = !empty($data['company_logo_one'])
        ? public_path(\Illuminate\Support\Facades\Storage::url($data['company_logo_one']))
        : '';

    $imageDataUri = '';

    if (!empty($data['company_logo_one']) && file_exists($imagePath)) {
        $imageDataUri = 'data:' . mime_content_type($imagePath)
            . ';base64,' . base64_encode(file_get_contents($imagePath));
    }
@endphp

<table style="width:100%; border:none; border-collapse:collapse; font-family:'Kalpurush', sans-serif;">
    <tr>
        <td style="width:20%; vertical-align:middle; border:none;">
            @if($imageDataUri)
                <img src="{{ $imageDataUri }}" style="height:60px;" alt="logo">
            @endif
        </td>
        <td style="width:80%; text-align:center; vertical-align:middle; border:none;">
            <div style="font-size:28px; font-weight:bold; font-style:italic; color:#015892; text-transform:uppercase;">
                {{ $data['company_name'] }}
            </div>
            <div style="font-size:24px; font-weight:bold; color:#46AA4D; text-transform:uppercase;">
                ওয়ান্ডার পার্ক এন্ড ইকো রিসোর্ট
            </div>
        </td>
    </tr>
</table>
<hr>