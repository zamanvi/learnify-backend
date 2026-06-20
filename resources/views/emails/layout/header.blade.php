@php
    $header = App\Models\Header::get()->first();
@endphp
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="img-center p-30 px-15"
            style="font-size:0pt; line-height:0pt; text-align:center; padding: 30px; padding-left: 15px; padding-right: 15px;">
            <a href="#" target="_blank"><img src="{{$header->header_logo}}" width="112" height="43" border="0"
                    alt="" /></a>
        </td>
    </tr>
</table>
