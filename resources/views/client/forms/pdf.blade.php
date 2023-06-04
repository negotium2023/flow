<html>
<head>
    <title>CRF Form</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        @font-face {
            font-family: 'Vibes';
            src: url("https://fonts.googleapis.com/css?family=Great+Vibes") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        table{
            border-collapse: collapse;
        }

        table, th, td {
            border: 0px;
            padding: 5px;
            font-size:18pt;
        }

        th{
            background:#ccc;
        }

        td{
            padding-bottom:10px;
        }

        body{
            font-size:18pt;
            color: #8d8d8d;
        }

        label{
            color:#696969;
            padding-bottom:10px;
        }

        .mh1{
            font-size: 26pt;
            line-height:50px;
            margin-bottom:15px;
            color:#000000;
        }

        .mh2{
            font-size: 21pt;
            line-height:50px;
            padding-bottom:7px;
            color:#404040;
        }

        .mh3{
            font-size: 20pt;
            line-height:50px;
            margin-bottom:7px;
            color:#696969;
        }

        thead { display: table-header-group }
        tfoot { display: table-row-group }
        tr { page-break-inside: avoid }

        hr{
            height:1px;
            color:#696969;
            background-color: #696969;
        }
    </style>
</head>
<body>
<div class="row mt-3">
    <div class="col-lg-12">
        @foreach($crf as $result)
            <strong class="mh1">Section 1</strong><br />
            <strong class="mh2">Internal Management Details</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('sec1_date', 'Date')}}<br />
                        {{($result->form_date != null ? $result->form_date : '&nbsp;' )}}
                    </td>
                    <td style="width:466px">
                        {{Form::label('client_code', 'Client Code')}}<br />
                        {{($result->client_code != null ? $result->client_code : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('client_name', 'Client Name')}}<br />
                        {{($result->client_name != null ? $result->client_name : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('director', 'Director')}}<br />
                        {{($result->director != null ? $result->director : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('manager', 'Manager')}}<br />
                        {{($result->manager != null ? $result->manager : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('form_completed_by', 'Form Completed By')}}<br />
                        {{($result->form_completed_by != null ? $result->form_completed_by : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('office', 'Office')}}<br />
                        {{($result->office != null ? $result->office : '&nbsp;')}}
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <hr />
            <strong class="mh1">Section 2</strong><br />
            <strong class="mh2">Client Type</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('contact_type', 'Contact Type')}}<br />
                        {{($result->contact_type != null ? $result->contact_type : '&nbsp;')}}
                    </td>
                    <td style="width:466px">
                        {{Form::label('business_type', 'Business Type')}}<br />
                        {{($result->business_type != null ? $result->business_type : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('client_type', 'Client Type')}}<br />
                        {{($result->client_type != null ? $result->client_type : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <hr />
            <strong class="mh1">Section 3</strong><br />
            <strong class="mh2">General Client Information</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('sec3_country', 'Client Type')}}<br />
                        {{($result->country == "Other" ? $result->country2 : ($result->country != null ? $result->country : '&nbsp;'))}}
                    </td>
                    <td>
                        {{Form::label('sec3_industry', 'Industry')}}<br />
                        {{($result->industry == "Other" ? $result->industry2 : ($result->industry != null ? $result->industry : '&nbsp;'))}}
                    </td>
                </tr>
            </table>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td colspan="2">
                        {{Form::label('year_end', 'Year End')}}<br />
                        {{($result->year_end != null ? $result->year_end : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('provided_services', 'Tick ALL Services To Be Provided')}}
                    </td>
                </tr>
                <tr>
                    <td style="width:466px">
                        {{Form::checkbox('provided_services[]',"Accounts Preparation",(stristr($result->services,'Accounts Preparation')))}}&nbsp;&nbsp;Accounts Preparation<br />
                        {{Form::checkbox('provided_services[]',"Audit",(stristr($result->services,'Audit')))}}&nbsp;&nbsp;Audit<br />
                        {{Form::checkbox('provided_services[]',"Bookkeeping",(stristr($result->services,'Bookkeeping')))}}&nbsp;&nbsp;Bookkeeping<br />
                        {{Form::checkbox('provided_services[]',"Business Services",(stristr($result->services,'Business Services')))}}&nbsp;&nbsp;Business Services<br />
                        {{Form::checkbox('provided_services[]',"Corporate Compliance",(stristr($result->services,'Corporate Compliance')))}}&nbsp;&nbsp;Corporate Compliance<br />
                        {{Form::checkbox('provided_services[]',"Consultancy",(stristr($result->services,'Consultancy')))}}&nbsp;&nbsp;Consultancy<br />
                        {{Form::checkbox('provided_services[]',"Corporation Tax",(stristr($result->services,'Corporation Tax')))}}&nbsp;&nbsp;Corporation Tax<br />
                        {{Form::checkbox('provided_services[]',"Insolvency",(stristr($result->services,'Insolvency')))}}&nbsp;&nbsp;Insolvency<br />
                        {{Form::checkbox('provided_services[]',"Payroll",(stristr($result->services,'Payroll')))}}&nbsp;&nbsp;Payroll<br />
                        {{Form::checkbox('provided_services[]',"Personal Tax",(stristr($result->services,'Personal Tax')))}}&nbsp;&nbsp;Personal Tax<br />
                    </td>
                    <td valign="top">
                        {{Form::checkbox('provided_services[]',"VAT Services",(stristr($result->services,'VAT Services')))}}&nbsp;&nbsp;VAT Services<br />
                        {{Form::checkbox('provided_services[]',"Corporate Finance",(stristr($result->services,'Corporate Finance')))}}&nbsp;&nbsp;Corporate Finance<br />
                        {{Form::checkbox('provided_services[]',"Forensic Accounting",(stristr($result->services,'Forensic Accounting')))}}&nbsp;&nbsp;Forensic Accounting
                    </td>
                </tr>
            </table>
            <hr />
            <strong class="mh1">Section 4</strong><br />
            <strong class="mh2">Limited Company / Unlimited Company / Limited by Guarantee</strong><br />
            <strong class="mh3">4.1 Tax &amp; General Details</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td colspan="2">
                        {{Form::label('company_secretarial_department', 'Is this company to be incorporated by the Company Secretarial Department?')}}<br />
                        {{($result->company_secretarial_department != null ? $result->company_secretarial_department : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td style="width:466px">
                        {{Form::label('company_reg_no', 'Company Registration Number')}}<br />
                        {{($result->company_registration_number != null ? $result->company_registration_number : '&nbsp;')}}
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('audit_status', 'Audit Status')}}<br />
                        {{($result->audit_status != null ? $result->audit_status : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('cosec_filing', 'CoSec Filing')}}<br />
                        {{($result->cosec_filing != null ? $result->cosec_filing : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ct_number', 'CT Number / UTR')}}<br />
                        {{($result->ct_number != null ? $result->ct_number : '&nbsp;' )}}
                    </td>
                    <td>
                        {{Form::label('vat_number', 'VAT Number')}}<br />
                        {{($result->vat_number != null ? $result->vat_number : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('corp_tax_filing', 'Corporation Tax Filing')}}<br />
                        {{($result->tax_filing != null ? $result->tax_filing : '&nbsp;' )}}
                    </td>
                    <td>
                        {{Form::label('ct_filing_date', 'CT Filing Date')}}<br />
                        {{($result->ct_filing_date != null ? $result->ct_filing_date : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <hr />
            <strong class="mh3">4.2 Company Contact Details Main / Correspondence / Billing Address</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('billing_address1', 'Address 1')}}<br />
                        {{($result->billing_address1 != null ? $result->billing_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('billing_address2', 'Address 2')}}<br />
                        {{($result->billing_address2 != null ? $result->billing_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('billing_address1', 'Address 3')}}<br />
                        {{($result->billing_address3 != null ? $result->billing_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('billing_town', 'Town')}}<br />
                        {{($result->billing_town != null ? $result->billing_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('billing_county', 'County')}}<br />
                        {{($result->billing_county != null ? $result->billing_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('billing_pcode', 'Post Code')}}<br />
                        {{($result->billing_pcode != null ? $result->billing_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('billing_country', 'Country')}}<br />
                        {{($result->billing_country != null ? $result->billing_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
            <strong>Business Address</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('business_address1', 'Address 1')}}<br />
                        {{($result->business_address1 != null ? $result->business_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('business_address2', 'Address 2')}}<br />
                        {{($result->business_address2 != null ? $result->business_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('business_address3', 'Address 3')}}<br />
                        {{($result->business_address3 != null ? $result->business_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('business_town', 'Town')}}<br />
                        {{($result->business_town != null ? $result->business_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('business_county', 'County')}}<br />
                        {{($result->business_county != null ? $result->business_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('business_pcode', 'Post Code')}}<br />
                        {{($result->business_pcode != null ? $result->business_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('business_country', 'Country')}}<br />
                        {{($result->business_country != null ? $result->business_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
            <strong>Registered Address</strong><br />
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('registered_address1', 'Address 1')}}<br />
                        {{($result->registered_address1 != null ? $result->registered_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('registered_address2', 'Address 2')}}<br />
                        {{($result->registered_address2 != null ? $result->registered_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('registered_address3', 'Address 3')}}<br />
                        {{($result->registered_address3 != null ? $result->registered_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('registered_town', 'Town')}}<br />
                        {{($result->business_town != null ? $result->business_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('registered_county', 'County')}}<br />
                        {{($result->registered_county != null ? $result->registered_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('registered_pcode', 'Post Code')}}<br />
                        {{($result->registered_pcode != null ? $result->registered_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('registered_country', 'Country')}}<br />
                        {{($result->registered_country != null ? $result->registered_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('company_telephone_numbers', 'Company Telephone Numbers')}}
                        {{($result->company_tel_number1 != null ? ($result->company_tel_number1 == "N/A" ? 'N/A' : $result->company_tel_number1) : '&nbsp;')}}<br />
                        {{($result->company_tel_number2 != null ? ($result->company_tel_number2 == "N/A" ? 'N/A' : $result->company_tel_number2) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('company_fax_numbers', 'Company Fax Numbers')}}<br />
                        {{($result->company_fax_number != null ? ($result->company_fax_number == "N/A" ? 'N/A' : $result->company_fax_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('company_email_address', 'Company Email Address')}}<br />
                        {{($result->company_email_address != null ? ($result->company_email_address == "N/A" ? 'N/A' : $result->company_email_address) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('company_website', 'Company Website')}}<br />
                        {{($result->company_website != null ? ($result->company_website == "N/A" ? 'N/A' : $result->company_website) : '&nbsp' )}}
                    </td>
                </tr>
            </table>
            <strong class="mh3">4.3 Company Representative Contact Details</strong><br />
            <strong>Individual 1</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('i1_name', 'Name')}}<br />
                        {{($result->i1_name != null ? $result->i1_name : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('i1_position', 'Position')}}<br />
                        {{($result->i1_position != null ? $result->i1_position : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('i1_office_number', 'Office Number')}}<br />
                        {{($result->i1_office_number != null ? ($result->i1_office_number == "N/A" ? 'N/A' : $result->i1_office_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('i1_home_number', 'Home Number')}}<br />
                        {{($result->i1_home_number != null ? ($result->i1_home_number == "N/A" ? 'N/A' : $result->i1_home_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('i1_mobile_number', 'Mobile Number')}}<br />
                        {{($result->i1_mobile_number != null ? ($result->i1_mobile_number == "N/A" ? 'N/A' : $result->i1_mobile_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('i1_email_address', 'E-mail Address')}}<br />
                        {{($result->i1_email_address != null ? ($result->i1_email_address == "N/A" ? 'N/A' : $result->i1_email_address) : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong>Residential Address</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('i1_address1', 'Address 1')}}<br />
                        {{($result->i1_address1 != null ? $result->i1_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('i1_address2', 'Address 2')}}<br />
                        {{($result->i1_address2 != null ? $result->i1_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('i1_address3', 'Address 3')}}<br />
                        {{($result->i1_address3 != null ? $result->i1_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('i1_town', 'Town')}}<br />
                        {{($result->i1_town != null ? $result->i1_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('i1_county', 'County')}}<br />
                        {{($result->i1_county != null ? $result->i1_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('i1_pcode', 'Post Code')}}<br />
                        {{($result->i1_pcode != null ? $result->i1_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('i1_country', 'Country')}}<br />
                        {{($result->i1_country != null ? $result->i1_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
            @if($result->i2_na != null || $result->i2_na == 0)
                <strong>Individual 2</strong>
                <table class="table table-borderless" style="width:1400px">
                    <tr>
                        <td style="width:466px">
                            {{Form::label('i2_name', 'Name')}}<br />
                            {{($result->i2_name != null ? $result->i2_name : '&nbsp;')}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{Form::label('i2_position', 'Position')}}<br />
                            {{($result->i2_position != null ? $result->i2_position : '&nbsp;')}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{Form::label('i2_office_number', 'Office Number')}}<br />
                            {{($result->i2_office_number != null ? ($result->i2_office_number == "N/A" ? 'N/A' : $result->i2_office_number) : '&nbsp;')}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{Form::label('i2_home_number', 'Home Number')}}<br />
                            {{($result->i2_home_number != null ? ($result->i2_home_number == "N/A" ? 'N/A' : $result->i2_home_number) : '&nbsp;')}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{Form::label('i2_mobile_number', 'Mobile Number')}}<br />
                            {{($result->i2_mobile_number != null ? ($result->i2_mobile_number == "N/A" ? 'N/A' : $result->i2_mobile_number) : '&nbsp;')}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{Form::label('i2_email_address', 'E-mail Address')}}<br />
                            {{($result->i2_email_address != null ? ($result->i2_email_address == "N/A" ? 'N/A' : $result->i2_email_address) : '&nbsp;')}}
                        </td>
                    </tr>
                </table>
                <strong>Residential Address</strong>
                <table class="table table-borderless" style="width:1400px">
                    <tr>
                        <td style="width:466px">
                            {{Form::label('i2_address1', 'Address 1')}}<br />
                            {{($result->i2_address1 != null ? $result->i2_address1 : '&nbsp;')}}
                        </td>
                        <td>
                            {{Form::label('i2_address2', 'Address 2')}}<br />
                            {{($result->i2_address2 != null ? $result->i2_address2 : '&nbsp;')}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{Form::label('i2_address3', 'Address 3')}}<br />
                            {{($result->i2_address3 != null ? $result->i2_address3 : '&nbsp;')}}
                        </td>
                        <td>
                            {{Form::label('i2_town', 'Town')}}<br />
                            {{($result->i2_town != null ? $result->i2_town : '&nbsp;')}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{Form::label('i2_county', 'County')}}<br />
                            {{($result->i2_county != null ? $result->i2_county : '&nbsp;')}}
                        </td>
                        <td>
                            {{Form::label('i2_pcode', 'Post Code')}}<br />
                            {{($result->i2_pcode != null ? $result->i2_pcode : '&nbsp;')}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {{Form::label('i2_country', 'Country')}}<br />
                            {{($result->i2_country != null ? $result->i2_country : '&nbsp;')}}
                        </td>
                        <td>

                        </td>
                    </tr>
                </table>
            @endif
            <hr />
            <strong class="mh1">Section 5</strong><br />
            <strong class="mh2">Sole Trader / Individual</strong>
            <strong class="mh3">5.1 Tax Details</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('st_uk_tax_number', 'PPS / UK Tax Number')}}<br />
                        {{($result->st_uk_tax_number != null ? ($result->st_uk_tax_number == "N/A" ? 'N/A' : $result->st_uk_tax_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('st_ni_number', 'NI Number (UK Only)')}}<br />
                        {{($result->st_ni_number != null ? ($result->st_ni_number == "N/A" ? 'N/A' : $result->st_ni_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('st_vat_number', 'VAT Number')}}<br />
                        {{($result->st_vat_number != null ? ($result->st_vat_number == "N/A" ? 'N/A' : $result->st_vat_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('st_personal_tax', 'Personal Tax')}}<br />
                        {{($result->st_personal_tax != null ? $result->st_personal_tax : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong class="mh3">5.2 Contact Details</strong><br />
            <strong>Personal Address</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('st_personal_address1', 'Address 1')}}<br />
                        {{($result->st_personal_address1 != null ? $result->st_personal_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('st_personal_address2', 'Address 2')}}<br />
                        {{($result->st_personal_address2 != null ? $result->st_personal_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('st_personal_address3', 'Address 3')}}<br />
                        {{($result->st_personal_address3 != null ? $result->st_personal_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('st_personal_town', 'Town')}}<br />
                        {{($result->st_personal_town != null ? $result->st_personal_town : '$nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('st_personal_county', 'County')}}<br />
                        {{($result->st_personal_county != null ? $result->st_personal_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('st_personal_pcode', 'Post Code')}}<br />
                        {{($result->st_personal_pcode != null ? $result->st_personal_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('st_personal_country', 'Country')}}<br />
                        {{($result->st_personal_country != null ? $result->st_personal_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
            <strong>Main / Correspondence / Billing Address</strong><br />
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('st_billing_address1', 'Address 1')}}<br />
                        {{($result->st_billing_address1 != null ? $result->st_billing_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('st_billing_address2', 'Address 2')}}<br />
                        {{($result->st_billing_address2 != null ? $result->st_billing_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('st_billing_address3', 'Address 3')}}<br />
                        {{($result->st_billing_address3 != null ? $result->st_billing_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('st_billing_town', 'Town')}}<br />
                        {{($result->st_billing_town != null ? $result->st_billing_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('st_billing_county', 'County')}}<br />
                        {{($result->st_billing_county != null ? $result->st_billing_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('st_billing_pcode', 'Post Code')}}<br />
                        {{($result->st_billing_pcode != null ? $result->st_billing_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('st_billing_country', 'Country')}}<br />
                        {{(($result->st_billing_country != null ? $result->st_billing_country : '&nbsp;'))}}
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
            <strong>Business Address</strong><br />
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('st_business_address1', 'Address 1')}}<br />
                        {{($result->st_business_address1 != null ? $result->st_business_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('st_business_address2', 'Address 2')}}<br />
                        {{($result->st_business_address2 != null ? $result->st_business_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('st_business_address3', 'Address 3')}}<br />
                        {{($result->st_business_address3 != null ? $result->st_business_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('st_business_town', 'Town')}}<br />
                        {{($result->st_business_town != null ? $result->st_business_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('st_business_county', 'County')}}<br />
                        {{($result->st_business_county != null ? $result->st_business_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('st_business_pcode', 'Post Code')}}<br />
                        {{($result->st_business_pcode != null ? $result->st_business_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('st_business_country', 'Country')}}<br />
                        {{($result->st_business_country != null ? $result->st_business_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('st_contact_name', 'Contact Name')}}<br />
                        {{($result->st_contact_name != null ? $result->st_contact_name : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('st_office_number', 'Office Number')}}<br />
                        {{($result->st_office_number != null ? ($result->st_office_number == "N/A" ? 'N/A' : $result->st_office_number ) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('st_home_number', 'Home Number')}}<br />
                        {{($result->st_home_number != null ? ($result->st_home_number == "N/A" ? 'N/A' : $result->st_home_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('st_mobile_number', 'Mobile Number')}}<br />
                        {{($result->st_mobile_number != null ? ($result->st_mobile_number == "N/A" ? 'N/A' : $result->st_mobile_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('st_fax_number', 'Fax Number')}}<br />
                        {{($result->st_fax_number != null ? ($result->st_fax_number == "N/A" ? 'N/A' : $result->st_fax_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('st_email_address1', 'E-mail Address 1')}}<br />
                        {{($result->st_email_address1 != null ? ($result->st_email_address1 == "N/A" ? 'N/A' : $result->st_email_address1) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('st_email_address2', 'E-mail Address 2')}}<br />
                        {{($result->st_email_address2 != null ? ($result->st_email_address2 == "N/A" ? 'N/A' : $result->st_email_address2) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('st_website', 'Website')}}<br />
                        {{($result->st_website != null ? ($result->st_website == "N/A" ? 'N/A' : $result->st_website) : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <hr />
            <strong class="mh1">Section 6</strong><br />
            <strong class="mh2">Partnership / Other Organisation</strong><br />
            <strong class="mh3">6.1 Tax Details</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('p_partnership_ref_no', 'Partnership Ref No')}}<br />
                        {{($result->p_partnership_ref_no != null ? ($result->p_partnership_ref_no == "N/A" ? 'N/A' : $result->p_partnership_ref_no) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('p_uk_tax_number', 'PPS / UK Tax Number')}}<br />
                        {{($result->p_uk_tax_number != null ? ($result->p_uk_tax_number == "N/A" ? 'N/A' : $result->p_uk_tax_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('p_ni_number', 'NI Number (UK Only)')}}<br />
                        {{($result->p_ni_number != null ? ($result->p_ni_number == "N/A" ? 'N/A' : $result->p_ni_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('p_vat_number', 'VAT Number')}}<br />
                        {{($result->p_vat_number != null ? ($result->p_vat_number == "N/A" ? 'N/A' : $result->p_vat_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('p_personal_tax', 'Personal Tax')}}<br />
                        {{($result->p_personal_tax != null ? $result->p_personal_tax : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong class="mh3">6.2 Contact Details</strong><br />
            <strong>Main / Correspondence / Billing Address</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('p_billing_address1', 'Address 1')}}<br />
                        {{($result->p_billing_address1 != null ? $result->p_billing_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('p_billing_address2', 'Address 2')}}<br />
                        {{($result->p_billing_address2 != null ? $result->p_billing_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('p_billing_address3', 'Address 3')}}<br />
                        {{($result->p_billing_address3 != null ? $result->p_billing_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('p_billing_town', 'Town')}}<br />
                        {{($result->p_billing_town != null ? $result->p_billing_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('p_billing_county', 'County')}}<br />
                        {{($result->p_billing_county != null ? $result->p_billing_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('p_billing_pcode', 'Post Code')}}<br />
                        {{($result->p_billing_pcode != null ? $result->p_billing_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('p_billing_country', 'Country')}}<br />
                        {{($result->p_billing_country != null ? $result->p_billing_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
            <strong>Business Address</strong><br />
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('p_business_address1', 'Address 1')}}<br />
                        {{($result->p_business_address1 != null ? $result->p_business_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('p_business_address2', 'Address 2')}}<br />
                        {{($result->p_business_address2 != null ? $result->p_business_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('p_business_address3', 'Address 3')}}<br />
                        {{($result->p_business_address3 != null ? $result->p_business_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('p_business_town', 'Town')}}<br />
                        {{($result->p_business_town != null ? $result->p_business_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('p_business_county', 'County')}}<br />
                        {{($result->p_business_county != null ? $result->p_business_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('p_business_pcode', 'Post Code')}}<br />
                        {{($result->p_business_pcode != null ? $result->p_business_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('p_business_country', 'Country')}}<br />
                        {{($result->p_business_country != null ? $result->p_business_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('p_office_number', 'Office Number')}}<br />
                        {{($result->p_office_number != null ? ($result->p_office_number == "N/A" ? '' : $result->p_office_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('p_fax_number', 'Fax Number')}}<br />
                        {{($result->p_fax_number != null ? ($result->p_fax_number == "N/A" ? '' : $result->p_fax_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('p_email_address1', 'E-mail Address 1')}}<br />
                        {{($result->p_email_address1 != null ? ($result->p_email_address1 == "N/A" ? '' : $result->p_email_address1) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('p_email_address2', 'E-mail Address 2')}}<br />
                        {{($result->p_email_address2 != null ? ($result->p_email_address2 == "N/A" ? '' : $result->p_email_address2) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('p_website', 'Website')}}<br />
                        {{($result->p_website != null ? ($result->p_website == "N/A" ? '' : $result->p_website) : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong class="mh3">6.3 Partners / Members Contact Details</strong><br />
            <strong>Contact 1</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('m1_contact_name', 'Contact Name')}}<br />
                        {{($result->m1_contact_name != null ? $result->m1_contact_name : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('m1_address1', 'Address 1')}}<br />
                        {{($result->m1_address1 != null ? $result->m1_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('m1_address2', 'Address 2')}}<br />
                        {{($result->m1_address2 != null ? $result->m1_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('m1_address3', 'Address 3')}}<br />
                        {{($result->m1_address3 != null ? $result->m1_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('m1_town', 'Town')}}<br />
                        {{($result->m1_town != null ? $result->m1_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('m1_county', 'County')}}<br />
                        {{($result->m1_county != null ? $result->m1_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('m1_pcode', 'Post Code')}}<br />
                        {{($result->m1_pcode != null ? $result->m1_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('m1_country', 'Country')}}<br />
                        {{($result->m1_country != null ? $result->m1_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('m1_home_number', 'Home Number')}}<br />
                        {{($result->m1_home_number != null ? ($result->m1_home_number == "N/A" ? '' : $result->m1_home_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('m1_mobile_number', 'Mobile Number')}}<br />
                        {{($result->m1_mobile_number != null ? ($result->m1_mobile_number == "N/A" ? '' : $result->m1_mobile_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('m1_email_address1', 'E-mail Address 1')}}<br />
                        {{($result->m1_email_address1 != null ? ($result->m1_email_address1 == "N/A" ? '' : $result->m1_email_address1) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('m1_email_address2', 'E-mail Address 2')}}<br />
                        {{($result->m1_email_address2 != null ? ($result->m1_email_address2 == "N/A" ? '' : $result->m1_email_address2) : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong>Contact 2</strong>
            {{Form::checkbox('m2_na',null,null,array('class'=>'m2_na'))}}&nbsp;&nbsp;N/A
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('m2_contact_name', 'Contact Name')}}<br />
                        {{($result->m2_contact_name != null ? $result->m2_contact_name : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('m2_address1', 'Address 1')}}<br />
                        {{($result->m2_address1 != null ? $result->m2_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('m2_address2', 'Address 2')}}<br />
                        {{($result->m2_address2 != null ? $result->m2_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('m2_address3', 'Address 3')}}<br />
                        {{($result->m2_address3 != null ? $result->m2_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('m2_town', 'Town')}}<br />
                        {{($result->m2_town != null ? $result->m2_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('m2_county', 'County')}}<br />
                        {{($result->m2_county != null ? $result->m2_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('m2_pcode', 'Post Code')}}<br />
                        {{($result->m2_pcode != null ? $result->m2_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('m2_country', 'Country')}}<br />
                        {{($result->m2_country != null ? $result->m2_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('m2_home_number', 'Home Number')}}<br />
                        {{($result->m2_home_number != null ? ($result->m2_home_number == "N/A" ? 'N/A' : $result->m2_home_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('m2_mobile_number', 'Mobile Number')}}<br />
                        {{($result->m2_mobile_number != null ? ($result->m2_mobile_number == "N/A" ? 'N/A' : $result->m2_mobile_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('m2_email_address1', 'E-mail Address 1')}}<br />
                        {{($result->m2_email_address1 != null ? ($result->m2_email_address1 == "N/A" ? 'N/A' : $result->m2_email_address1) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('m2_email_address2', 'E-mail Address 2')}}<br />
                        {{($result->m2_email_address2 != null ? ($result->m2_email_address2 == "N/A" ? 'N/A' : $result->m2_email_address2) : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <hr />
            <strong class="mh1">Section 7</strong><br />
            <strong class="mh2">Pension Schemes &amp; Trusts</strong><br />
            <strong class="mh3">7.1 Tax Details</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px" colspan="2">
                        {{Form::label('pen_ref_no', 'Pension Scheme Ref No')}}<br />
                        {{($result->pen_ref_no != null ? ($result->pen_ref_no == "N/A" ? 'N/A' : $result->pen_ref_no) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('pen_tax_no', 'Tax Ref No')}}<br />
                        {{($result->pen_tax_no != null ? ($result->pen_tax_no == "N/A" ? 'N/A' : $result->pen_tax_no) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('pen_tax_filing', 'Tax Filing')}}<br />
                        {{($result->pen_tax_filing != null ? $result->pen_tax_filing : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('pen_tax_filing_date', 'Tax Filing Date')}}<br />
                        {{($result->pen_tax_filing_date != null ? $result->pen_tax_filing_date : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong class="mh3">7.2 Contact Details</strong><br />
            <strong>Main / Correspondence / Billing Address</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('pen_billing_address1', 'Address 1')}}<br />
                        {{($result->pen_billing_address1 != null ? $result->pen_billing_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('pen_billing_address2', 'Address 2')}}<br />
                        {{($result->pen_billing_address2 != null ? $result->pen_billing_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('pen_billing_address3', 'Address 3')}}<br />
                        {{($result->pen_billing_address3 != null ? $result->pen_billing_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('pen_billing_town', 'Town')}}<br />
                        {{($result->pen_billing_town != null ? $result->pen_billing_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('pen_billing_county', 'County')}}<br />
                        {{($result->pen_billing_county != null ? $result->pen_billing_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('pen_billing_pcode', 'Post Code')}}<br />
                        {{($result->pen_billing_pcode != null ? $result->pen_billing_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('pen_billing_country', 'Country')}}<br />
                        {{($result->pen_billing_country != null ? $result->pen_billing_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('pen_billing_contact_name', 'Contact Name')}}<br />
                        {{($result->pen_billing_contact_name != null ? $result->pen_billing_contact_name : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('pen_billing_position', 'Position')}}<br />
                        {{($result->pen_billing_position != null ? $result->pen_billing_position : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('pen_billing_office_number', 'Office Number')}}<br />
                        {{($result->pen_billing_office_number != null ? ($result->pen_billing_office_number == "N/A" ? 'N/A' : $result->pen_billing_office_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('pen_billing_home_number', 'Home Number')}}<br />
                        {{($result->pen_billing_home_number != null ? ($result->pen_billing_home_number == "N/A" ? 'N/A' : $result->pen_billing_home_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('pen_billing_mobile_number', 'Mobile Number')}}<br />
                        {{($result->pen_billing_mobile_number != null ? ($result->pen_billing_mobile_number == "N/A" ? 'N/A' : $result->pen_billing_mobile_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('pen_billing_fax_number', 'Fax Number')}}<br />
                        {{($result->pen_billing_fax_number != null ? ($result->pen_billing_fax_number == "N/A" ? 'N/A' : $result->pen_billing_fax_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        {{Form::label('pen_billing_email_address', 'E-mail Address')}}<br />
                        {{($result->pen_billing_email_address != null ? ($result->pen_billing_email_address == "N/A" ? 'N/A' : $result->pen_billing_email_address) : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong>Residential Address</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('pen_res_address1', 'Address 1')}}<br />
                        {{($result->pen_res_address1 != null ? $result->pen_res_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('pen_res_address2', 'Address 2')}}<br />
                        {{($result->pen_res_address2 != null ? $result->pen_res_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('pen_res_address3', 'Address 3')}}<br />
                        {{($result->pen_res_address3 != null ? $result->pen_res_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('pen_res_town', 'Town')}}<br />
                        {{($result->pen_res_town != null ? $result->pen_res_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('pen_res_county', 'County')}}<br />
                        {{($result->pen_res_county != null ? $result->pen_res_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('pen_res_pcode', 'Post Code')}}<br />
                        {{($result->pen_res_pcode != null ? $result->pen_res_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('pen_res_country', 'Country')}}<br />
                        {{($result->pen_res_country != null ? $result->pen_res_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
            <hr />
            <strong class="mh1">Section 8</strong><br />
            <strong class="mh2">Liquidation / Insolvency</strong><br />
            <strong class="mh3">8.1 Company Details</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td>
                        {{Form::label('liq_company_registration_number', 'Company Registration Number')}}<br />
                        {{($result->liq_company_registration_number != null ? $result->liq_company_registration_number : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_tax_ref', 'Tax Ref No')}}<br />
                        {{($result->liq_tax_ref != null ? ($result->liq_tax_ref == "N/A" ? 'N/A' : $result->liq_tax_ref) : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong class="mh3">8.2 Company Representative Contact Details</strong><br />
            <strong>Director 1</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('liq_d1_name', 'Name')}}<br />
                        {{($result->liq_d1_name != null ? $result->liq_d1_name : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d1_position', 'Position')}}<br />
                        {{($result->liq_d1_position != null ? $result->liq_d1_position : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d1_office_number', 'Office Number')}}<br />
                        {{($result->liq_d1_office_number != null ? ($result->liq_d1_office_number == "N/A" ? 'N/A' : $result->liq_d1_office_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d1_home_number', 'Home Number')}}<br />
                        {{($result->liq_d1_home_number != null ? ($result->liq_d1_home_number == "N/A" ?'N/A' : $result->liq_d1_home_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d1_mobile_number', 'Mobile Number')}}<br />
                        {{($result->liq_d1_mobile_number != null ? ($result->liq_d1_mobile_number == "N/A" ? 'N/A' : $result->liq_d1_mobile_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d1_email_address', 'E-mail Address')}}<br />
                        {{($result->liq_d1_email_address != null ? ($result->liq_d1_email_address = "N/A" ? 'N/A' : $result->liq_d1_email_address) : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong>Residential Address</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('liq_d1_address1', 'Address 1')}}<br />
                        {{($result->liq_d1_address1 != null ? $result->liq_d1_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('liq_d1_address2', 'Address 2')}}<br />
                        {{($result->liq_d1_address2 != null ? $result->liq_d1_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d1_address3', 'Address 3')}}<br />
                        {{($result->liq_d1_address3 != null ? $result->liq_d1_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('liq_d1_town', 'Town')}}<br />
                        {{($result->liq_d1_town != null ? $result->liq_d1_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d1_county', 'County')}}<br />
                        {{($result->liq_d1_county != null ? $result->liq_d1_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('liq_d1_pcode', 'Post Code')}}<br />
                        {{($result->liq_d1_pcode != null ? $result->liq_d1_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d1_country', 'Country')}}<br />
                        {{($result->liq_d1_country != null ? $result->liq_d1_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
            <strong>Director 2</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('liq_d2_name', 'Name')}}<br />
                        {{($result->liq_d2_name != null ? $result->liq_d2_name : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d2_position', 'Position')}}<br />
                        {{($result->liq_d2_position != null ? $result->liq_d2_position : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d2_office_number', 'Office Number')}}<br />
                        {{($result->liq_d2_office_number != null ? ($result->liq_d2_office_number == "N/A" ? 'N/A' : $result->liq_d2_office_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d2_home_number', 'Home Number')}}<br />
                        {{($result->liq_d2_home_number != null ? ($result->liq_d2_home_number == "N/A" ? 'N/A' : $result->liq_d2_home_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d2_mobile_number', 'Mobile Number')}}<br />
                        {{($result->liq_d2_mobile_number != null ? ($result->liq_d2_mobile_number == "N/A" ? 'N/A' : $result->liq_d2_mobile_number) : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d2_email_address', 'E-mail Address')}}<br />
                        {{($result->liq_d2_email_address != null ? ($result->liq_d2_email_address == "N/A" ? 'N/A' : $result->liq_d2_email_address) : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong>Residential Address</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('liq_d2_address1', 'Address 1')}}<br />
                        {{($result->liq_d2_address1 != null ? $result->liq_d2_address1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('liq_d2_address2', 'Address 2')}}<br />
                        {{($result->liq_d2_address2 != null ? $result->liq_d2_address2 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d2_address3', 'Address 3')}}<br />
                        {{($result->liq_d2_address3 != null ? $result->liq_d2_address3 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('liq_d2_town', 'Town')}}<br />
                        {{($result->liq_d2_town != null ? $result->liq_d2_town : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d2_county', 'County')}}<br />
                        {{($result->liq_d2_county != null ? $result->liq_d2_county : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('liq_d2_pcode', 'Post Code')}}<br />
                        {{($result->liq_d2_pcode != null ? $result->liq_d2_pcode : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('liq_d2_country', 'Country')}}<br />
                        {{($result->liq_d2_country != null ? $result->liq_d2_country : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
            <hr />
            <strong class="mh1">Section 9</strong><br />
            <strong class="mh2">Extra Info for Database</strong><br />
            <strong class="mh3">9.1 Marketing</strong><br />
            <br />
            Publications<br />
            <br />
            {{Form::checkbox('x_tailored_emails','Yes',($result->x_tailored_emails == "Yes" ? true : false))}}&nbsp;&nbsp;E-zines &amp; Tailored emails<br />
            {{Form::checkbox('x_uk_mailings','Yes',($result->x_uk_mailings == "Yes" ? true : false))}}&nbsp;&nbsp;NI / UK Specialised Mailings<br />
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('x_how_did_you_hear', 'How did you hear about us?')}}<br />
                        {{($result->x_how_did_you_hear != null ? $result->x_how_did_you_hear : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('x_referrer_name', 'Referrer Name')}}<br />
                        {{($result->x_referrer_name != null ? $result->x_referrer_name : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('x_existing_account_name', 'Existing Account Name')}}<br />
                        {{($result->x_existing_account_name != null ? $result->x_existing_account_name : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('x_staff_member_name', 'Existing Account Name')}}<br />
                        {{($result->x_staff_member_name != null ? $result->x_staff_member_name : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong class="mh3">9.2 Associated Contacts</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('x_relationship_type1', 'Existing Account Name')}}<br />
                        {{($result->x_relationship_type1 != null ? $result->x_relationship_type1 : '&nbsp;')}}
                    </td>
                    <td style="width:466px">
                        {{Form::label('x_client_name1', 'Client Name')}}<br />
                        {{($result->x_client_name1 != null ? $result->x_client_name1 : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('x_client_code1', 'Client Code')}}<br />
                        {{($result->x_client_code1 != null ? $result->x_client_code1 : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{($result->x_relationship_type2 != null ? $result->x_relationship_type2 : '&nbsp;')}}
                    </td>
                    <td>
                        {{($result->x_client_name2 != null ? $result->x_client_name2 : '&nbsp;')}}
                    </td>
                    <td>
                        {{($result->x_client_code2 != null ? $result->x_client_code2 : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong class="mh3">9.3 Notes</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td>
                        {{($result->x_notes != null ? $result->x_notes : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong class="mh3">9.4 Payments</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td style="width:466px">
                        {{Form::label('p_credit_limit', 'Credit Limit')}}<br />
                        {{($result->p_credit_limit != null ? $result->p_credit_limit : '&nbsp;')}}
                    </td>
                    <td style="width:466px">
                        {{Form::label('p_tax_type', 'Tax Type')}}<br />
                        {{($result->p_tax_type != null ? $result->p_tax_type : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::label('p_currency', 'Currency')}}<br />
                        @if($result->p_currency != null)
                            {{($result->p_currency == 'Euro' ? 'Euro (&euro;)' : 'GBP (&pound;)')}}
                        @else
                            {{$result->p_currency}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        Invoices &amp; Statements to be sent by email only&nbsp;&nbsp;{{Form::checkbox('p_invoice_email_only',null,($result->p_invoice_email_only == "Yes" ? true : false))}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('p_email', 'Send to: Email')}}<br />
                        {{($result->p_email != null ? $result->p_email : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('p_client_value', 'Fee Quote Issued:')}}<br />
                        {{($result->p_fee_quote != null ? $result->p_fee_quote : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('p_client_value', 'Client Value')}}<br />
                        {{($result->p_client_value != null ? $result->p_client_value : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                </tr>
            </table>
            <strong class="mh3">9.5 Client Rating</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td>
                        {{($result->p_client_rating != null ? $result->p_client_rating : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
            </table>
            <hr />
            <strong class="mh1">Section 10</strong><br />
            <strong class="mh2">AML / Customer Due Diligence</strong><br />
            <strong class="mh3">10.1 General Information</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td>
                        {{Form::label('cdd_source', 'Source of Introduction')}}<br />
                        {{($result->cdd_source != null ? $result->cdd_source : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('cdd_sort_of_business', 'What sort of business do we expect to undertake for the client?')}}<br />
                        {{($result->cdd_sort_of_business != null ? $result->cdd_sort_of_business : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('cdd_date_and_place', 'Date and place where prospective client was met in person')}}<br />
                        {{($result->cdd_date_and_place != null ? $result->cdd_date_and_place : '&nbsp;')}}
                    </td>
                    <td>
                        {{Form::checkbox('cdd_date_and_place_na','Yes',null)}}&nbsp;&nbsp;N/A (Existing Client)<br />
                    </td>
                </tr>
            </table>
            <strong class="mh3">10.2 Corporate Information</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td>
                        {{Form::label('cr_company_search', 'Company Search carried out, results reviewed and documents saved to CCH?')}}<br />
                        {{($result->cr_company_search != null ? $result->cr_company_search : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('cr_copy_of_certificate', 'Copy of Certificate of Incorporation and Memorandum and Articles of Association obtained and saved to CCH?')}}<br />
                        {{($result->cr_copy_of_certificate != null ? $result->cr_copy_of_certificate : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('cr_list_of_directors', 'Copy of Certificate of Incorporation and Memorandum and Articles of Association obtained and saved to CCH?')}}<br />
                        {{($result->cr_list_of_directors != null ? $result->cr_list_of_directors : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('cr_list_of_names', 'List of names, addresses, occupations and dates of birth for shareholders with a shareholding of or in excess of 25% saved to CCH? (If corporate shareholders it is necessary to look behind this again to determine the individuals who have ultimate ownership / control)')}}<br />
                        {{($result->cr_list_of_names != null ? $result->cr_list_of_names : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('cr_photo_id', 'Photo ID and proof of address for Directors, Shareholders with shares of 25% or greater and all persons authorised to represent the company saved to CCH')}}<br />
                        {{($result->cr_photo_id != null ? $result->cr_photo_id : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('cr_due_diligence', 'Are enhanced due diligence procedures required to be completed?')}}<br />
                        {{($result->cr_due_diligence != null ? $result->cr_due_diligence : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
            </table>
            <strong class="mh3">10.3 Individual Requirements</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td>
                        {{Form::label("ir_satisfied", "Are we satisfied that in general terms we understand the source of the client's funds / wealth?")}}<br />
                        {{($result->ir_satisfied != null ? $result->ir_satisfied : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ir_satisfied != null && $result->ir_satisfied == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td>
                        {{Form::label('ir_y_what_basis', 'If yes, on what basis?')}}<br />
                        {{($result->ir_y_what_basis != null ? $result->ir_y_what_basis : '&nbsp;')}}
                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ir_photo_id', 'Photo ID and proof of address for the client saved to CCH?')}}<br />
                        {{($result->ir_photo_id_saved != null ? $result->ir_photo_id_saved : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ir_due_diligence', 'Are enhanced due diligence procedures required to be completed?')}}<br />
                        {{($result->ir_due_diligence != null ? $result->ir_due_diligence : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
            </table>
            <strong class="mh3">10.4 Politacally Exposed Person (PEP)</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td>
                        {{Form::label('pep_considered', 'Are any of the individuals considered as being a PEP?')}}<br />
                        {{($result->pep_considered != null ? $result->pep_considered : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('pep_due_diligence', 'Are enhanced due diligence procedures required to be completed?')}}<br />
                        {{($result->pep_due_diligence != null ? $result->pep_due_diligence : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->pep_due_diligence != null && $result->pep_due_diligence == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td>
                        {{Form::label('pep_approved', 'If yes, has Michael Bellew, Director & MLRO, approved that we accept the client in these circumstances?')}}<br />
                        {{($result->pep_approved != null ? $result->pep_approved : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
            </table>
            <hr />
            <strong class="mh1">Section 11</strong><br />
            <strong class="mh2">Client Criteria Form</strong><br />
            <strong>Determining the integrity of the Prospective</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td>
                        {{Form::label('ccr_business_understanding', 'We confirm that we have a full understanding of the client\'s business.')}}<br />
                        {{($result->ccr_business_understanding != null ? $result->ccr_business_understanding : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_services_understanding', 'We confirm that we have a complete understanding of the services to be provided to the client.')}}<br />
                        {{($result->ccr_services_understanding != null ? $result->ccr_services_understanding : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_concerns_ownership', 'Have we any concerns identifying ownership, key management or those charged with governance?')}}<br />
                        {{($result->ccr_concerns_ownership != null ? $result->ccr_concerns_ownership : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_concerns_ownership != null && $result->ccr_concerns_ownership == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2">
                        {{Form::label('ccr_identify_concerns', 'If yes, please identify these concerns.')}}<br />
                        {{($result->ccr_identify_concerns != null ? $result->ccr_identify_concerns : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_confirm_integrity', 'We confirm that the firm does not have information that would lead it to conclude that the client lacks integrity.')}}<br />
                        {{($result->ccr_confirm_integrity != null ? $result->ccr_confirm_integrity : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_confirm_integrity != null && $result->ccr_confirm_integrity == 'No' ? 'display:block' : 'display:none')}}">
                    <td colspan="2"><strong class="alert-danger">*If no we cannot act for this client.</strong> </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_management_concerns', 'Do we have concerns regarding management\'s competence?')}}<br />
                        {{($result->ccr_management_concerns != null ? $result->ccr_management_concerns : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_management_concerns != null && $result->ccr_management_concerns == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2">
                        {{Form::label('ccr_identify_management_concerns', 'If yes, please identify these concerns.')}}<br />
                        {{($result->ccr_identify_management_concerns != null ? $result->ccr_identify_management_concerns : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_financial_concerns', 'Do we have concerns with the financial condition of the company / individual / partnership?')}}<br />
                        {{($result->ccr_financial_concerns != null ? $result->ccr_financial_concerns : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_identify_financial != null && $result->ccr_identify_financial == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2">
                        {{Form::label('ccr_identify_financial_concerns', 'If yes, please identify these concerns.')}}<br />
                        {{($result->ccr_identify_financial_concerns != null ? $result->ccr_identify_financial_concerns : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_legal_environment', 'Does the entity operate in a specialist legal or regulatory environment?')}}<br />
                        {{($result->ccr_legal_environment != null ? $result->ccr_legal_environment : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_legal_environment != null && $result->ccr_legal_environment == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2">
                        {{Form::label('ccr_confirm_legal_environment', 'If yes, please confirm exact nature of this environment.')}}<br />
                        {{($result->ccr_confirm_legal_environment != null ? $result->ccr_confirm_legal_environment : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_accountant', 'Is there any reason that the existing accountant / auditor may not wish to continue in the engagement?')}}<br />
                        {{($result->ccr_accountant != null ? $result->ccr_accountant : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_accountant != null && $result->ccr_accountant == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2">
                        {{Form::label('ccr_accountant_concerns', 'If yes, do we have any concerns in this respect.')}}<br />
                        {{($result->ccr_accountant_concerns != null ? $result->ccr_accountant_concerns : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_accountant_frequency', 'Has there been a frequency in changing accountants / auditors?')}}<br />
                        {{($result->ccr_accountant_frequency != null ? $result->ccr_accountant_frequency : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_accountant_frequency != null && $result->ccr_accountant_frequency == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2">
                        {{Form::label('ccr_accountant_frequency_concerns', 'If yes, do we have any concerns in this respect.')}}<br />
                        {{($result->ccr_accountant_frequency_concerns != null ? $result->ccr_accountant_frequency_concerns : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_firms', 'Have any other firms refused to act for this client?')}}<br />
                        {{($result->ccr_firms != null ? $result->ccr_firms : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_firms != null && $result->ccr_firms == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2">
                        {{Form::label('ccr_firms_concerns', 'If yes, do we have any concerns in this respect.')}}<br />
                        {{($result->ccr_firms_concerns != null ? $result->ccr_firms_concerns : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_risk', 'Does this client pose any risk to existing client relationships?')}}<br />
                        {{($result->ccr_risk != null ? $result->ccr_risk : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_risk != null && $result->ccr_risk == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2">
                        {{Form::label('ccr_risk_concerns', 'If yes, please give details.')}}<br />
                        {{($result->ccr_risk_concerns != null ? $result->ccr_risk_concerns : '&nbsp;')}}
                    </td>
                </tr>
            </table>
            <strong>Determining the Competency of the  Firm to Perform the Engagement</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td>
                        {{Form::label('ccr_confirm_engagement_director', 'We confirm that the engagement director has experience in the entity\'s business type')}}<br />
                        {{($result->ccr_confirm_engagement_director != null ? $result->ccr_confirm_engagement_director : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_confirm_engagement_director != null && $result->ccr_confirm_engagement_director == 'No' ? 'display:block' : 'display:none')}}">
                    <td colspan="2"><strong class="alert-danger">*If no we cannot act for this client.</strong> </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_confirm_engagement_manager', 'We confirm that the engagement manager has experience in the entity\'s business type.')}}<br />
                        {{($result->ccr_confirm_engagement_manager != null ? $result->ccr_confirm_engagement_manager : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_confirm_engagement_manager != null && $result->ccr_confirm_engagement_manager == 'No' ? 'display:block' : 'display:none')}}">
                    <td colspan="2"><strong class="alert-danger">*If no we cannot act for this client.</strong> </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_exposure_regulated_business', 'Does this client have any exposure in regulated business?')}}<br />
                        {{($result->ccr_exposure_regulated_business != null ? $result->ccr_exposure_regulated_business : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_exposure_regulated_business != null && $result->ccr_exposure_regulated_business == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2">
                        {{Form::label('ccr_exposure_regulated_business_confirm', 'If yes, confirm exact nature of this regulated business.')}}<br />
                        {{($result->ccr_exposure_regulated_business_confirm != null ? $result->ccr_exposure_regulated_business_confirm : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_availability_concerns', 'Are there any concerns regarding the availability of professional staff to enable completion of the engagement to both a quality standard and in a timely manner?')}}<br />
                        {{($result->ccr_availability_concerns != null ? $result->ccr_availability_concerns : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_availability_concerns != null && $result->ccr_availability_concerns == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2"><strong class="alert-danger">*If yes we cannot act for this client.</strong> </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_partner_concerns', 'Are there any concerns regarding the partner and staff having sufficient knowledge and experience for the engagement?')}}<br />
                        {{($result->ccr_partner_concerns != null ? $result->ccr_partner_concerns : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_partner_concerns != null && $result->ccr_partner_concerns == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2"><strong class="alert-danger">*If yes we cannot act for this client.</strong> </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_staffing_engagement', 'Are there any matters related to staffing the engagement which would indicate that the engagement should not be accepted or why such acceptance needs to be considered by a second partner?')}}<br />
                        {{($result->ccr_staffing_engagement != null ? $result->ccr_staffing_engagement : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_staffing_engagement != null && $result->ccr_staffing_engagement == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2"><strong class="alert-danger">*If yes we cannot act for this client.</strong> </td>
                </tr>
            </table>
            <strong>Compliance with Ethical Requirements</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td>
                        {{Form::label('ccr_client_connection', 'Has the firm any existing connection with the new client?')}}<br />
                        {{($result->ccr_client_connection != null ? $result->ccr_client_connection : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_client_connection != null && $result->ccr_client_connection == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2">
                        {{Form::label('ccr_client_connection_details', 'If yes, please give details.')}}<br />
                        {{($result->ccr_client_connection_details != null ? $result->ccr_client_connection_details : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_already_service', 'Do we already provide any services to the client?')}}<br />
                        {{($result->ccr_already_service != null ? $result->ccr_already_service : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_already_service != null && $result->ccr_already_service == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2">
                        {{Form::label('ccr_already_service_details', 'If yes, please give details.')}}<br />
                        {{($result->ccr_already_service_details != null ? $result->ccr_already_service_details : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_staff_connection', 'Has any member of staff any connection with the new client?')}}<br />
                        {{($result->ccr_staff_connection != null ? $result->ccr_staff_connection : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_staff_connection != null && $result->ccr_staff_connection == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2">
                        {{Form::label('ccr_staff_connection_details', 'If yes, please give details.')}}<br />
                        {{($result->ccr_staff_connection_details != null ? $result->ccr_staff_connection_details : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_previously_acted', 'Have we previously acted for this client in any respect?')}}<br />
                        {{($result->ccr_previously_acted != null ? $result->ccr_previously_acted : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_previously_acted != null && $result->ccr_previously_acted == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2">
                        {{Form::label('ccr_previously_acted_details', 'If yes, please give details.')}}<br />
                        {{($result->ccr_previously_acted_details != null ? $result->ccr_previously_acted_details : '&nbsp;')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{Form::label('ccr_potential_risk', 'Is there any potential conflict of risk?')}}<br />
                        {{($result->ccr_potential_risk != null ? $result->ccr_potential_risk : '&nbsp;')}}
                    </td>
                    <td>
                    </td>
                </tr>
                <tr style="{{($result->ccr_potential_risk != null && $result->ccr_potential_risk == 'Yes' ? 'display:block' : 'display:none')}}">
                    <td colspan="2"><strong class="alert-danger">*If yes we cannot act for this client.</strong> </td>
                </tr>
            </table>
            {{--<hr />--}}
            {{--<strong>Section 12</strong><br />
            <strong>Client Authorisation</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td>
                        {{Form::label('client_authorization', 'The client has signed a letter of authorisation to authorise UHY FDW to act on the instructions of the named directors / partners / members of staff / individuals and this document has been saved to file.')}}<br />
                        {{$result->client_authorization}}
                    </td>
                    <td>
                    </td>
                </tr>
            </table>
            <hr />
            <strong>Section 13</strong><br />
            <strong>Process for New Clients Spreadsheet</strong>
            <table class="table table-borderless" style="width:1400px">
                <tr>
                    <td>
                        {{Form::label('client_process_completed', 'Process for New Clients spreadsheet completed.')}}<br />
                        {{$result->client_process_completed}}
                    </td>
                    <td>
                    </td>
                </tr>
            </table>--}}
        @endforeach
    </div>
    @if(isset($auth))
        <div class="col-lg-12 pull-right" style="text-align:right;">
            <div style="float: right;display: block;font-size:12pt;padding:60px 5px 5px;">Signed by</div>
            <div style="clear: both;"></div>
            <div style="min-width:400px;text-align:center;float: right;display: block;border-bottom:1px solid #000000;font-size:24pt;padding:10px 5px 5px;font-family: 'Brush Script MT', 'Brush Script Std', cursive;">{{(isset($auth) && $auth != '' ? $auth->first_name.' '.$auth->last_name : '')}}</div>
            <div style="clear: both;"></div>
            <div style="float: right;display: block;font-size:.75rem;padding:10px 5px 5px;">{{(isset($date_signed) && $date_signed != '' ? $date_signed : '')}}</div>
        </div>
    @endif
</div>

</body>
</html>