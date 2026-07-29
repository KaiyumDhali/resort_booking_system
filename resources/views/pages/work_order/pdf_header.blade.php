@php
    use Illuminate\Support\Facades\Storage;

    $logoPath = '';

    if (!empty($data['company_logo_one'])) {
        $logoPath = public_path(Storage::url($data['company_logo_one']));
    }
@endphp

<table style="width:100%; border:none; border-collapse:collapse; font-family:'Kalpurush', sans-serif;">
    <tr>
        <td style="width:20%; vertical-align:middle; border:none;">
            @if(!empty($logoPath) && file_exists($logoPath))
                <img src="{{ $logoPath }}" style="height:60px;" alt="Company Logo">
            @endif
        </td>

        <td style="width:80%; text-align:center; vertical-align:middle; border:none;">
            <div style="font-size:28px; font-weight:bold; font-style:italic; color:#015892; text-transform:uppercase;">
                {{ $data['company_name'] }}
            </div>

            <div style="font-size:24px; font-weight:bold; color:#46AA4D;">
                আপন ভুবন পিকনিক এবং শুটিং স্পট
            </div>
        </td>
    </tr>
</table>

<hr style="border:0;border-top:1px solid #000;margin-top:5px;">