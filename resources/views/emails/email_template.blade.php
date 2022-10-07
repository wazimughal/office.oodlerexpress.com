@php

if (isset($mailData['body_message']) && !empty($mailData['body_message'])) {
    $body_message = $mailData['body_message'];
} else {
    $body_message = 'Welcome To Oodler Express .You have been assigned a new password to your online portal. Your password is: _______ please use your email as your username.';
}

if (isset($mailData['button_title']) && !empty($mailData['button_title'])) {
    $button_title = $mailData['button_title'];
} else {
    $button_title = 'Login';
}

if (isset($mailData['button_link']) && !empty($mailData['button_link'])) {
    $button_link = $mailData['button_link'];
} else {
    $button_link = url('/');
}
@endphp

<!DOCTYPE HTML
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <!--[if gte mso 9]>
<xml>
  <o:OfficeDocumentSettings>
    <o:AllowPNG/>
    <o:PixelsPerInch>96</o:PixelsPerInch>
  </o:OfficeDocumentSettings>
</xml>
<![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<![endif]-->
    <title></title>
    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .u-row {
                width: 600px !important;
            }

            .u-row .u-col {
                vertical-align: top;
            }

            .u-row .u-col-100 {
                width: 600px !important;
            }

        }

        @media (max-width: 620px) {
            .u-row-container {
                max-width: 100% !important;
                padding-left: 0px !important;
                padding-right: 0px !important;
            }

            .u-row .u-col {
                min-width: 320px !important;
                max-width: 100% !important;
                display: block !important;
            }

            .u-row {
                width: calc(100% - 40px) !important;
            }

            .u-col {
                width: 100% !important;
            }

            .u-col>div {
                margin: 0 auto;
            }
        }

        body {
            margin: 0;
            padding: 0;
        }

        table,
        tr,
        td {
            vertical-align: top;
            border-collapse: collapse;
        }

        p {
            margin: 0;
        }

        .ie-container table,
        .mso-container table {
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        a[x-apple-data-detectors=\'true\'] {
            color: inherit !important;
            text-decoration: none !important;
        }

        @media (max-width: 480px) {
            .hide-mobile {
                max-height: 0px;
                overflow: hidden;
                display: none !important;
            }
        }

        #u_body a {
            color: #0000ee;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            #u_column_1 .v-col-background-color {
                background-color: #ffffff !important;
            }

            #u_content_image_1 .v-container-padding-padding {
                padding: 30px 10px 5px !important;
            }

            #u_content_image_1 .v-src-width {
                width: auto !important;
            }

            #u_content_image_1 .v-src-max-width {
                max-width: 40% !important;
            }

            #u_content_heading_1 .v-font-size {
                font-size: 26px !important;
            }

            #u_content_text_1 .v-container-padding-padding {
                padding: 60px 10px 10px !important;
            }

            #u_content_text_1 .v-color {
                color: #ffffff !important;
            }

            #u_content_text_1 .v-line-height {
                line-height: 180% !important;
            }

            #u_content_button_1 .v-container-padding-padding {
                padding: 10px 10px 30px !important;
            }

            #u_content_button_1 .v-size-width {
                width: 75% !important;
            }

            #u_content_text_3 .v-container-padding-padding {
                padding: 40px 10px 10px !important;
            }

            #u_content_menu_1 .v-padding {
                padding: 5px 10px !important;
            }
        }
    </style>
    <!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,700&display=swap" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet"
        type="text/css">
    <!--<![endif]-->
</head>

<body class="clean-body u_body"
    style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #fcf8f5;color: #ffffff">
    <!--[if IE]><div class="ie-container"><![endif]-->
    <!--[if mso]><div class="mso-container"><![endif]-->
    <table id="u_body"
        style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #fcf8f5;width:100%"
        cellpadding="0" cellspacing="0">
        <tbody>
            <tr style="vertical-align: top">
                <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                    <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color: #fcf8f5;"><![endif]-->


                    <div class="u-row-container"
                        style="padding: 0px;background-image: url('{{ url('adminpanel/dist/img/image-5.png') }});background-repeat: no-repeat;background-position: center top;background-color: transparent">
                        <div class="u-row"
                            style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-image: url('{{ url('adminpanel/dist/img/image-5.png') }}');background-repeat: no-repeat;background-position: center top;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: transparent;"><![endif]-->

                                <!--[if (mso)|(IE)]><td align="center" width="600" class="v-col-background-color" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
                                <div id="u_column_1" class="u-col u-col-100"
                                    style=" background:#fff;max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div class="v-col-background-color" style="height: 100%;width: 100% !important;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div
                                            style="height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">
                                            <!--<![endif]-->

                                            <table id="u_content_image_1" style="font-family:Open Sans,sans-serif;"
                                                role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                                border="0">
                                                <tbody>
                                                    <tr>
                                                        <td class="v-container-padding-padding"
                                                            style="overflow-wrap:break-word;word-break:break-word;padding:50px 10px 5px;font-family:Open Sans,sans-serif;"
                                                            align="left">

                                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                                border="0">
                                                                <tr>
                                                                    <td style="padding-right: 0px;padding-left: 0px;"
                                                                        align="center">
                                                                        <a href="{{url('/')}}"
                                                                            target="_blank">
                                                                            <img align="center" border="0"
                                                                                src="{{ url('adminpanel/dist/img/logo_oodler.png') }}"
                                                                                alt="image" title="image"
                                                                                style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 20%;max-width: 116px;"
                                                                                width="116"
                                                                                class="v-src-width v-src-max-width" />
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table id="u_content_heading_1" style="font-family:Open Sans,sans-serif;"
                                                role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                                border="0">
                                                <tbody>
                                                    <tr>
                                                        <td class="v-container-padding-padding"
                                                            style="overflow-wrap:break-word;word-break:break-word;padding:0px 10px;font-family:Open Sans,sans-serif;"
                                                            align="left">

                                                            <h1 class="v-color v-line-height v-font-size"
                                                                style="margin: 0px; color: #000; line-height: 140%; text-align:center; word-wrap: break-word; font-weight: normal; font-family: 'Cabin',sans-serif; font-size: 40px;">
                                                                Oodler Express
                                                            </h1>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table id="u_content_text_1"
                                                style=" color:#000 !important;font-family:Open Sans,sans-serif;"
                                                role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                                border="0">
                                                <tbody>
                                                    <tr>
                                                        <td class="v-container-padding-padding"
                                                            style="overflow-wrap:break-word;word-break:break-word;padding:60px 30px 15px;font-family:Open Sans,sans-serif;"
                                                            align="left">

                                                            <div class="v-color v-line-height"
                                                                style="line-height: 180%; text-align: center; word-wrap: break-word;">
                                                                <p style="font-size: 14px; line-height: 180%;">
                                                                    {!! $body_message !!}</p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            @if ($button_title != '')
                                                <table id="u_content_button_1"
                                                    style="font-family:Open Sans,sans-serif;" role="presentation"
                                                    cellpadding="0" cellspacing="0" width="100%" border="0">
                                                    <tbody>
                                                        <tr>
                                                            <td class="v-container-padding-padding"
                                                                style="overflow-wrap:break-word;word-break:break-word;padding:10px 10px 60px;font-family:Open Sans,sans-serif;"
                                                                align="left">

                                                                <!--[if mso]><style>.v-button {background: transparent !important;}</style><![endif]-->
                                                                <div align="center">
                                                                    <!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="https://thephotographicmemories.com" style="height:37px; v-text-anchor:middle; width:203px;" arcsize="13.5%"  stroke="f" fillcolor="#cf0818"><w:anchorlock/><center style="color:#FFFFFF;font-family:Open Sans,sans-serif;"><![endif]-->
                                                                    <a href="{{ $button_link }}" target="_blank"
                                                                        class="v-button v-size-width" style="">
                                                                        <span class="v-line-height v-padding"
                                                                            style="box-sizing: border-box;display: inline-block;font-family:Open Sans,sans-serif;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #FFFFFF; background-color: #cf0818; border-radius: 5px;-webkit-border-radius: 5px; -moz-border-radius: 5px; width:35%;padding:10px; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;"><strong><span
                                                                                    style="font-size: 14px; line-height: 16.8px;">{{ $button_title }}</span></strong></span>
                                                                    </a>

                                                                    @if (isset($mailData['button_title2']))
                                                                        <a href="{{ $mailData['button_link2'] }}"
                                                                            target="_blank"
                                                                            class="v-button v-size-width"
                                                                            style="">
                                                                            <span class="v-line-height v-padding"
                                                                                style="ox-sizing: border-box;display: inline-block;font-family:Open Sans,sans-serif;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #FFFFFF; background-color: #cf0818; border-radius: 5px;-webkit-border-radius: 5px; -moz-border-radius: 5px; width:35%;padding:10px; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;"><strong><span
                                                                                        style="font-size: 14px; line-height: 16.8px;">{{ $mailData['button_title2'] }}</span></strong></span>
                                                                        </a>
                                                                    @endif
                                                                    <!--[if mso]></center></v:roundrect><![endif]-->
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            @endif

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: transparent;"><![endif]-->

                                <!--[if (mso)|(IE)]><td align="center" width="600" class="v-col-background-color" style="background-color: #ffffff;width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;" valign="top"><![endif]-->
                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div class="v-col-background-color"
                                        style="background-color: #ffffff;height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div
                                            style="height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table id="u_content_text_3" style="font-family:Open Sans,sans-serif;"
                                                role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                                border="0">
                                                <tbody>
                                                    <tr>
                                                        <td class="v-container-padding-padding"
                                                            style="overflow-wrap:break-word;word-break:break-word;padding:40px 30px 10px;font-family:Open Sans,sans-serif;"
                                                            align="left">

                                                            <div class="v-color v-line-height"
                                                                style="color: #000000; line-height: 140%; text-align: center; word-wrap: break-word;">
                                                                <p style="font-size: 14px; line-height: 140%;">if you
                                                                    have any questions, please email us at
                                                                    joel@thephotographicmemories.com or visit our FAQS.     </p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table style="font-family:Open Sans,sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td class="v-container-padding-padding"
                                                            style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:Open Sans,sans-serif;"
                                                            align="left">

                                                            <div align="center">
                                                                <div style="display: table; max-width:187px;">
                                                                    <!--[if (mso)|(IE)]><table width="187" cellpadding="0" cellspacing="0" border="0"><tr><td style="border-collapse:collapse;" align="center"><table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; mso-table-lspace: 0pt;mso-table-rspace: 0pt; width:187px;"><tr><![endif]-->


                                                                    <!--[if (mso)|(IE)]><td width="32" style="width:32px; padding-right: 15px;" valign="top"><![endif]-->
                                                                    <table align="left" border="0"
                                                                        cellspacing="0" cellpadding="0"
                                                                        width="32" height="32"
                                                                        style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 15px">
                                                                        <tbody>
                                                                            <tr style="vertical-align: top">
                                                                                <td align="left" valign="middle"
                                                                                    style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                                    <a href="https://facebook.com/"
                                                                                        title="Facebook"
                                                                                        target="_blank">
                                                                                        <img src="{{ url('adminpanel/dist/img/fb.png') }}"
                                                                                            alt="Facebook"
                                                                                            title="Facebook"
                                                                                            width="32"
                                                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    <!--[if (mso)|(IE)]></td><![endif]-->

                                                                    <!--[if (mso)|(IE)]><td width="32" style="width:32px; padding-right: 15px;" valign="top"><![endif]-->
                                                                    <table align="left" border="0"
                                                                        cellspacing="0" cellpadding="0"
                                                                        width="32" height="32"
                                                                        style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 15px">
                                                                        <tbody>
                                                                            <tr style="vertical-align: top">
                                                                                <td align="left" valign="middle"
                                                                                    style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                                    <a href="https://twitter.com/"
                                                                                        title="Twitter"
                                                                                        target="_blank">
                                                                                        <img src="{{ url('adminpanel/dist/img/twitter.png') }}"
                                                                                            alt="Twitter"
                                                                                            title="Twitter"
                                                                                            width="32"
                                                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    <!--[if (mso)|(IE)]></td><![endif]-->

                                                                    <!--[if (mso)|(IE)]><td width="32" style="width:32px; padding-right: 15px;" valign="top"><![endif]-->
                                                                    <table align="left" border="0"
                                                                        cellspacing="0" cellpadding="0"
                                                                        width="32" height="32"
                                                                        style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 15px">
                                                                        <tbody>
                                                                            <tr style="vertical-align: top">
                                                                                <td align="left" valign="middle"
                                                                                    style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                                    <a href="https://linkedin.com/"
                                                                                        title="LinkedIn"
                                                                                        target="_blank">
                                                                                        <img src="{{ url('adminpanel/dist/img/linkedin.png') }}"
                                                                                            alt="LinkedIn"
                                                                                            title="LinkedIn"
                                                                                            width="32"
                                                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    <!--[if (mso)|(IE)]></td><![endif]-->

                                                                    <!--[if (mso)|(IE)]><td width="32" style="width:32px; padding-right: 0px;" valign="top"><![endif]-->
                                                                    <table align="left" border="0"
                                                                        cellspacing="0" cellpadding="0"
                                                                        width="32" height="32"
                                                                        style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 0px">
                                                                        <tbody>
                                                                            <tr style="vertical-align: top">
                                                                                <td align="left" valign="middle"
                                                                                    style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                                    <a href="https://instagram.com/"
                                                                                        title="Instagram"
                                                                                        target="_blank">
                                                                                        <img src="{{ url('adminpanel/dist/img/insta.png') }}"
                                                                                            alt="Instagram"
                                                                                            title="Instagram"
                                                                                            width="32"
                                                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    <!--[if (mso)|(IE)]></td><![endif]-->


                                                                    <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                                                                </div>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table id="u_content_menu_1" style="font-family:Open Sans,sans-serif;"
                                                role="presentation" cellpadding="0" cellspacing="0" width="100%"
                                                border="0">
                                                <tbody>
                                                    <tr>
                                                        <td class="v-container-padding-padding"
                                                            style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:Open Sans,sans-serif;"
                                                            align="left">

                                                            <div class="menu" style="text-align:center">
                                                                <!--[if (mso)|(IE)]><table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center"><tr><![endif]-->

                                                                <!--[if (mso)|(IE)]><td style="padding:5px 15px"><![endif]-->

                                                                <a href="https://thephotographicmemories.com"
                                                                    target="_self"
                                                                    style="padding:5px 15px;display:inline-block;color:#000000;font-family:arial,helvetica,sans-serif;font-size:14px;text-decoration:none"
                                                                    class="v-padding v-font-size">
                                                                    Home
                                                                </a>

                                                                <!--[if (mso)|(IE)]></td><![endif]-->

                                                                <!--[if (mso)|(IE)]><td style="padding:5px 15px"><![endif]-->
                                                                <span
                                                                    style="padding:5px 15px;display:inline-block;color:#000000;font-family:arial,helvetica,sans-serif;font-size:14px"
                                                                    class="v-padding v-font-size hide-mobile">
                                                                    |
                                                                </span>
                                                                <!--[if (mso)|(IE)]></td><![endif]-->


                                                                <!--[if (mso)|(IE)]><td style="padding:5px 15px"><![endif]-->



                                                                <!--[if (mso)|(IE)]><td style="padding:5px 15px"><![endif]-->

                                                                <a href="https://thephotographicmemories.com"
                                                                    target="_self"
                                                                    style="padding:5px 15px;display:inline-block;color:#000000;font-family:arial,helvetica,sans-serif;font-size:14px;text-decoration:none"
                                                                    class="v-padding v-font-size">
                                                                    About Us
                                                                </a>

                                                                <!--[if (mso)|(IE)]></td><![endif]-->

                                                                <!--[if (mso)|(IE)]><td style="padding:5px 15px"><![endif]-->
                                                                <span
                                                                    style="padding:5px 15px;display:inline-block;color:#000000;font-family:arial,helvetica,sans-serif;font-size:14px"
                                                                    class="v-padding v-font-size hide-mobile">
                                                                    |
                                                                </span>
                                                                <!--[if (mso)|(IE)]></td><![endif]-->


                                                                <!--[if (mso)|(IE)]><td style="padding:5px 15px"><![endif]-->

                                                                <a href="https://thephotographicmemories.com"
                                                                    target="_self"
                                                                    style="padding:5px 15px;display:inline-block;color:#000000;font-family:arial,helvetica,sans-serif;font-size:14px;text-decoration:none"
                                                                    class="v-padding v-font-size">
                                                                    Contact Us
                                                                </a>

                                                                <!--[if (mso)|(IE)]></td><![endif]-->


                                                                <!--[if (mso)|(IE)]></tr></table><![endif]-->
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table style="font-family:Open Sans,sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td class="v-container-padding-padding"
                                                            style="overflow-wrap:break-word;word-break:break-word;padding:10px 10px 40px;font-family:Open Sans,sans-serif;"
                                                            align="left">

                                                            <div class="v-color v-line-height"
                                                                style="color: #000000; line-height: 140%; text-align: center; word-wrap: break-word;">
                                                                <p style="font-size: 14px; line-height: 140%;">you have
                                                                    received this email as a registered user of <a
                                                                        rel="noopener"
                                                                        href="{{url('/')}}"
                                                                        target="_blank">{{config('app.name')}}</a>
                                                                </p>
                                                                <p style="font-size: 14px; line-height: 140%;">can <a
                                                                        rel="noopener"
                                                                        href="{{url('/')}}"
                                                                        target="_blank">unsubscribe</a> from these
                                                                    emails here.</p>
                                                                <p style="font-size: 14px; line-height: 140%;"> </p>
                                                                <p style="font-size: 14px; line-height: 140%;">2261
                                                                    Market Street #4667 San Francisco, CA 94114 All
                                                                    rights reserved</p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>


                    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                </td>
            </tr>
        </tbody>
    </table>
    <!--[if mso]></div><![endif]-->
    <!--[if IE]></div><![endif]-->
</body>

</html>;
