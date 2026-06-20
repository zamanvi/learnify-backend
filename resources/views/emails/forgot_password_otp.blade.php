@extends('emails.layout.master')
@section('email_main')
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="px-50 mpx-15" style="padding-left: 50px; padding-right: 50px;">
                <!-- Section - Intro -->
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="pb-50" style="padding-bottom: 50px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="title-36 a-center pb-15"
                                        style="font-size:36px; line-height:40px; color:#282828; font-family:'PT Sans', Arial, sans-serif; min-width:auto !important; text-align:center; padding-bottom: 15px;">
                                        <p><h2>{{ $content['name'] }}</h2></p>
                                        <strong>Change Password with Code</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-16 lh-26 a-center pb-25"
                                        style="font-size:16px; color:#6e6e6e; font-family:'PT Sans', Arial, sans-serif; min-width:auto !important; line-height: 26px; text-align:center; padding-bottom: 25px;">
                                        This is your requested account key code to verify with your email address ({{ $content['name'] }})
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pb-30" style="padding-bottom: 30px;">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="title-22 a-center py-20 px-50 mpx-15"
                                                    style="border-radius: 10px; border: 1px dashed #b4b4d4; font-size:22px; line-height:26px; color:#282828; font-family:'PT Sans', Arial, sans-serif; min-width:auto !important; text-align:center; padding-top: 20px; padding-bottom: 20px; padding-left: 50px; padding-right: 50px;"
                                                    bgcolor="#f4ecfa">
                                                    <strong>USE OTP : <span class="c-purple" style="color:#9128df;">{{ $content['otp'] }}</span></strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!-- END Section - Intro -->
            </td>
        </tr>
    </table>
@endsection
