<table width="100%" border="0" cellspacing="0" cellpadding="0">
    @php
        $header = App\Models\Header::get()->first();
    @endphp
    <tr>
        <td class="p-50 mpx-15" bgcolor="#949196" style="border-radius: 0 0 10px 10px; padding: 50px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="text-14 lh-24 a-center c-white l-white pb-20"
                        style="font-size:14px; font-family:'PT Sans', Arial, sans-serif; min-width:auto !important; line-height: 24px; text-align:center; color:#ffffff; padding-bottom: 20px;">
                        {{$header->address}}
                        <br />
                        <a href="tel:+17384796719" target="_blank" class="link c-white"
                            style="text-decoration:none; color:#ffffff;"><span class="link c-white"
                                style="text-decoration:none; color:#ffffff;">{{$header->phone}}</span></a>
                        <br />
                        <a href="mailto:{{$header->email}}" target="_blank" class="link c-white"
                            style="text-decoration:none; color:#ffffff;"><span class="link c-white"
                                style="text-decoration:none; color:#ffffff;">{{$header->email}}</span></a>
                        - <a href="https://readersfm.com" target="_blank" class="link c-white"
                            style="text-decoration:none; color:#ffffff;"><span class="link c-white"
                                style="text-decoration:none; color:#ffffff;">&copy; {{ date('Y') }} ReadersFM. All rights reserved</span></a>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <!-- Download App -->
                        <table border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="img" width="117"
                                    style="font-size:0pt; line-height:0pt; text-align:left;">
                                    <a href="{{$header->play_store_link}}" target="_blank"><img src="{{$header->play_store_logo}}"
                                            width="117" height="40" border="0" alt="play-store" /></a>
                                </td>
                                <td class="img" width="15"
                                    style="font-size:0pt; line-height:0pt; text-align:left;">
                                </td>
                                <td class="img" width="117"
                                    style="font-size:0pt; line-height:0pt; text-align:left;">
                                    <a href="{{$header->app_store_link}}" target="_blank"><img src="{{$header->app_store_logo}}"
                                            width="117" height="40" border="0" alt="app-store" /></a>
                                </td>
                            </tr>
                        </table>
                        <!-- END Download App -->
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table> <!-- END Footer -->
<!-- Bottom -->
