<!DOCTYPE html>
<html>

<head>

    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="format-detection" content="date=no" />
    <meta name="format-detection" content="address=no" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="x-apple-disable-message-reformatting" />
    <link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&display=swap" rel="stylesheet" />

    <title>RedRoseBD</title>
    @include('emails.layout.script')
</head>

<body class="body"
    style="padding:0 !important; margin:0 auto !important; display:block !important; min-width:100% !important; width:100% !important; background:#f4ecfa; -webkit-text-size-adjust:none;">
    <center>
        <table width="100%" border="0" cellspacing="0" cellpadding="0"
            style="margin: 0; padding: 0; width: 100%; height: 100%;" bgcolor="#f4ecfa" class="gwfw">
            <tr>
                <td style="margin: 0; padding: 0; width: 100%; height: 100%;" align="center" valign="top">
                    <table width="600" border="0" cellspacing="0" cellpadding="0" class="m-shell">
                        <tr>
                            <td class="td"
                                style="width:600px; min-width:600px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="mpx-10">
                                            <!-- Container -->
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td class="gradient pt-10"
                                                        style="border-radius: 10px 10px 0 0; padding-top: 10px;"
                                                        bgcolor="#f3189e">
                                                        <table width="100%" border="0" cellspacing="0"
                                                            cellpadding="0">
                                                            <tr>
                                                                <td style="border-radius: 10px 10px 0 0;"
                                                                    bgcolor="#ffffff">
                                                                    <!-- Logo -->
                                                                    {{-- @include('emails.layout.header') --}}
                                                                    <!-- Logo -->

                                                                    <!-- Main -->
                                                                    @yield('email_main')
                                                                    <!-- END Main -->
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!-- END Container -->

                                            <!-- Footer -->
                                            {{-- @include('emails.layout.footer') --}}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>
