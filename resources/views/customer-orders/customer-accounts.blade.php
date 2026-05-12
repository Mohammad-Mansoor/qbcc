@extends('dsh.master')
@section('content')
    <!-- navbar -->

    <!-- form -->

    <div class="row" style="direction: ltr;">
        <div class="col-sm-12" style="direction: ltr;">
            <div class="card" style="direction: ltr !important;">
                <div class="card-header" style="direction: rtl;">
                    @if(!$accountEdit)
                        <h4 style="direction: ltr !important;float: left;">ثبت مشتری جدید (Add New Customer)</h4>
                    @else
                        <h4>ویرایش معلومات مشتری (Edit Customer)</h4>
                    @endif
                </div>

                <!-- Dari Explanation Section -->
                <div class="card-body bg-light-info border-bottom mb-3" style="direction: rtl; text-align: right; background: #e3f2fd; border-radius: 10px; margin: 15px; padding: 20px;">
                    <h5 class="text-primary"><i class="fa fa-info-circle mr-2"></i> راهنمای مدیریت مشتریان فرمایشی</h5>
                    <p class="mb-2">این بخش مخصوص مدیریت مشتریانی است که فرمایشات خاص (Custom Orders) دارند. اطلاعات ثبت شده در اینجا برای پیگیری مراحل تولید و تسویه حساب‌های مالی استفاده می‌شود.</p>
                    <ul class="pr-4 mt-2" style="list-style-type: square;">
                        <li><strong>نام مشتری:</strong> نام شخص یا شرکت فرمایش دهنده را وارد کنید.</li>
                        <li><strong>کشور:</strong> کشور محل اقامت مشتری را برای تنظیمات گمرکی و حمل و نقل انتخاب کنید.</li>
                        <li><strong>دکمه فرمایشات (Orders):</strong> با کلیک بر روی این دکمه در جدول پایین، شما به صفحه اختصاصی فرمایشات این مشتری هدایت می‌شوید تا بتوانید قراردادهای جدید ثبت کنید.</li>
                    </ul>
                </div>

                <div class="card-body">
                    @if(!$accountEdit)
                        <form method="post" id="" action="/dashboard/customer-account-for-orders">
                            @csrf
                            <div class="row" style="direction: ltr !important;;">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="date-label" style="float: right;">نام مشتری (Customer Name)</span>
                                    <input type="text" name="customer_name" class="form-control" style="direction: rtl;">
                                    <small class="text-muted" style="direction: rtl; display: block; margin-top: 5px;">نام کامل مشتری یا نام شرکت را اینجا وارد کنید.</small>
                                    @error('customer_name') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>
                                    @enderror
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="date-label" style="float: right;">کشور (Customer Country)</span>
                                    <select name="customer_country" id="customer_country" class="form-control" style="direction: rtl;">
                                        <option>Afghanistan</option>
                                        <option>United Arab Emirates</option>
                                        <option>United Kingdom</option>
                                        <option>United States</option>
                                        <option>Germany</option>
                                        <option>Turkey</option>
                                        <option>Pakistan</option>
                                        <option>Iran</option>
                                        <!-- ... other countries ... -->
                                    </select>
                                    <small class="text-muted" style="direction: rtl; display: block; margin-top: 5px;">کشور محل فعالیت مشتری را انتخاب کنید.</small>
                                </div>
                                        <option>&Aring;land Islands</option>
                                        <option>Albania</option>
                                        <option>Algeria</option>
                                        <option>American Samoa</option>
                                        <option>Andorra</option>
                                        <option>Angola</option>
                                        <option>Anguilla</option>
                                        <option>Antarctica</option>
                                        <option>Antigua and Barbuda</option>
                                        <option>Argentina</option>
                                        <option>Armenia</option>
                                        <option>Aruba</option>
                                        <option>Australia</option>
                                        <option>Austria</option>
                                        <option>Azerbaijan</option>
                                        <option>Bahamas</option>
                                        <option>Bahrain</option>
                                        <option>Bangladesh</option>
                                        <option>Barbados</option>
                                        <option>Belarus</option>
                                        <option>Belgium</option>
                                        <option>Belize</option>
                                        <option>Benin</option>
                                        <option>Bermuda</option>
                                        <option>Bhutan</option>
                                        <option>Bolivia, Plurinational State of</option>
                                        <option>Bosnia and Herzegovina</option>
                                        <option>Botswana</option>
                                        <option>Bouvet Island</option>
                                        <option>Brazil</option>
                                        <option>British Indian Ocean Territory</option>
                                        <option>Brunei Darussalam</option>
                                        <option>Bulgaria</option>
                                        <option>Burkina Faso</option>
                                        <option>Burundi</option>
                                        <option>Cambodia</option>
                                        <option>Cameroon</option>
                                        <option>Canada</option>
                                        <option>Cape Verde</option>
                                        <option>Cayman Islands</option>
                                        <option>Central African Republic</option>
                                        <option>Chad</option>
                                        <option>Chile</option>
                                        <option>China</option>
                                        <option>Christmas Island</option>
                                        <option>Cocos (Keeling) Islands</option>
                                        <option>Colombia</option>
                                        <option>Comoros</option>
                                        <option>Congo</option>
                                        <option>Congo, the Democratic Republic of the</option>
                                        <option>Cook Islands</option>
                                        <option>Costa Rica</option>
                                        <option>C&ocirc;te d'Ivoire</option>
                                        <option>Croatia</option>
                                        <option>Cuba</option>
                                        <option>Cyprus</option>
                                        <option>Czech Republic</option>
                                        <option>Denmark</option>
                                        <option>Djibouti</option>
                                        <option>Dominica</option>
                                        <option>Dominican Republic</option>
                                        <option>Ecuador</option>
                                        <option>Egypt</option>
                                        <option>El Salvador</option>
                                        <option>Equatorial Guinea</option>
                                        <option>Eritrea</option>
                                        <option>Estonia</option>
                                        <option>Ethiopia</option>
                                        <option>Falkland Islands (Malvinas)</option>
                                        <option>Faroe Islands</option>
                                        <option>Fiji</option>
                                        <option>Finland</option>
                                        <option>France</option>
                                        <option>French Guiana</option>
                                        <option>French Polynesia</option>
                                        <option>French Southern Territories</option>
                                        <option>Gabon</option>
                                        <option>Gambia</option>
                                        <option>Georgia</option>
                                        <option>Germany</option>
                                        <option>Ghana</option>
                                        <option>Gibraltar</option>
                                        <option>Greece</option>
                                        <option>Greenland</option>
                                        <option>Grenada</option>
                                        <option>Guadeloupe</option>
                                        <option>Guam</option>
                                        <option>Guatemala</option>
                                        <option>Guernsey</option>
                                        <option>Guinea</option>
                                        <option>Guinea-Bissau</option>
                                        <option>Guyana</option>
                                        <option>Haiti</option>
                                        <option>Heard Island and McDonald Islands</option>
                                        <option>Holy See (Vatican City State)</option>
                                        <option>Honduras</option>
                                        <option>Hong Kong</option>
                                        <option>Hungary</option>
                                        <option>Iceland</option>
                                        <option>India</option>
                                        <option>Indonesia</option>
                                        <option>Iran, Islamic Republic of</option>
                                        <option>Iraq</option>
                                        <option>Ireland</option>
                                        <option>Isle of Man</option>
                                        <option>Israel</option>
                                        <option>Italy</option>
                                        <option>Jamaica</option>
                                        <option>Japan</option>
                                        <option>Jersey</option>
                                        <option>Jordan</option>
                                        <option>Kazakhstan</option>
                                        <option>Kenya</option>
                                        <option>Kiribati</option>
                                        <option>Korea, Democratic People's Republic of</option>
                                        <option>Korea, Republic of</option>
                                        <option>Kuwait</option>
                                        <option>Kyrgyzstan</option>
                                        <option>Lao People's Democratic Republic</option>
                                        <option>Latvia</option>
                                        <option>Lebanon</option>
                                        <option>Lesotho</option>
                                        <option>Liberia</option>
                                        <option>Libyan Arab Jamahiriya</option>
                                        <option>Liechtenstein</option>
                                        <option>Lithuania</option>
                                        <option>Luxembourg</option>
                                        <option>Macao</option>
                                        <option>Macedonia, the former Yugoslav Republic of</option>
                                        <option>Madagascar</option>
                                        <option>Malawi</option>
                                        <option>Malaysia</option>
                                        <option>Maldives</option>
                                        <option>Mali</option>
                                        <option>Malta</option>
                                        <option>Marshall Islands</option>
                                        <option>Martinique</option>
                                        <option>Mauritania</option>
                                        <option>Mauritius</option>
                                        <option>Mayotte</option>
                                        <option>Mexico</option>
                                        <option>Micronesia, Federated States of</option>
                                        <option>Moldova, Republic of</option>
                                        <option>Monaco</option>
                                        <option>Mongolia</option>
                                        <option>Montenegro</option>
                                        <option>Montserrat</option>
                                        <option>Morocco</option>
                                        <option>Mozambique</option>
                                        <option>Myanmar</option>
                                        <option>Namibia</option>
                                        <option>Nauru</option>
                                        <option>Nepal</option>
                                        <option>Netherlands</option>
                                        <option>Netherlands Antilles</option>
                                        <option>New Caledonia</option>
                                        <option>New Zealand</option>
                                        <option>Nicaragua</option>
                                        <option>Niger</option>
                                        <option>Nigeria</option>
                                        <option>Niue</option>
                                        <option>Norfolk Island</option>
                                        <option>Northern Mariana Islands</option>
                                        <option>Norway</option>
                                        <option>Oman</option>
                                        <option>Pakistan</option>
                                        <option>Palau</option>
                                        <option>Palestinian Territory, Occupied</option>
                                        <option>Panama</option>
                                        <option>Papua New Guinea</option>
                                        <option>Paraguay</option>
                                        <option>Peru</option>
                                        <option>Philippines</option>
                                        <option>Pitcairn</option>
                                        <option>Poland</option>
                                        <option>Portugal</option>
                                        <option>Puerto Rico</option>
                                        <option>Qatar</option>
                                        <option>R&eacute;union</option>
                                        <option>Romania</option>
                                        <option>Russian Federation</option>
                                        <option>Rwanda</option>
                                        <option>Saint Barth&eacute;lemy</option>
                                        <option>Saint Helena, Ascension and Tristan da Cunha</option>
                                        <option>Saint Kitts and Nevis</option>
                                        <option>Saint Lucia</option>
                                        <option>Saint Martin (French part)</option>
                                        <option>Saint Pierre and Miquelon</option>
                                        <option>Saint Vincent and the Grenadines</option>
                                        <option>Samoa</option>
                                        <option>San Marino</option>
                                        <option>Sao Tome and Principe</option>
                                        <option>Saudi Arabia</option>
                                        <option>Senegal</option>
                                        <option>Serbia</option>
                                        <option>Seychelles</option>
                                        <option>Sierra Leone</option>
                                        <option>Singapore</option>
                                        <option>Slovakia</option>
                                        <option>Slovenia</option>
                                        <option>Solomon Islands</option>
                                        <option>Somalia</option>
                                        <option>South Africa</option>
                                        <option>South Georgia and the South Sandwich Islands</option>
                                        <option>Spain</option>
                                        <option>Sri Lanka</option>
                                        <option>Sudan</option>
                                        <option>Suriname</option>
                                        <option>Svalbard and Jan Mayen</option>
                                        <option>Swaziland</option>
                                        <option>Sweden</option>
                                        <option>Switzerland</option>
                                        <option>Syrian Arab Republic</option>
                                        <option>Taiwan, Province of China</option>
                                        <option>Tajikistan</option>
                                        <option>Tanzania, United Republic of</option>
                                        <option>Thailand</option>
                                        <option>Timor-Leste</option>
                                        <option>Togo</option>
                                        <option>Tokelau</option>
                                        <option>Tonga</option>
                                        <option>Trinidad and Tobago</option>
                                        <option>Tunisia</option>
                                        <option>Turkey</option>
                                        <option>Turkmenistan</option>
                                        <option>Turks and Caicos Islands</option>
                                        <option>Tuvalu</option>
                                        <option>Uganda</option>
                                        <option>Ukraine</option>
                                        <option>United Arab Emirates</option>
                                        <option>United Kingdom</option>
                                        <option>United States</option>
                                        <option>United States Minor Outlying Islands</option>
                                        <option>Uruguay</option>
                                        <option>Uzbekistan</option>
                                        <option>Vanuatu</option>
                                        <option>Venezuela, Bolivarian Republic of</option>
                                        <option>Viet Nam</option>
                                        <option>Virgin Islands, British</option>
                                        <option>Virgin Islands, U.S.</option>
                                        <option>Wallis and Futuna</option>
                                        <option>Western Sahara</option>
                                        <option>Yemen</option>
                                        <option>Zambia</option>
                                        <option>Zimbabwe</option>

                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top: 20px">
                                    <button class="btn btn-block btn-primary submit-btn" type="submit">Save</button>
                                </div>
                            </div>


                        </form>
                    @else
                        <form method="post" id=""
                               action="/dashboard/customer-account-for-orders/{{$accountEdit->c_id}}">
                            {{method_field('patch')}}
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top: 15px">
                                    <span class="date-label" style="float: left;">Customer Name</span>
                                    <input type="text" style="direction: rtl" name="customer_name"
                                           value="{{$accountEdit->customer_name}}" class="form-control">
                                    @error('customer_name') <p
                                        class="text-danger">{{trans('message.'.$message)}}</p>
                                    @enderror
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <span class="date-label" style="float: left;">Customer Country</span>
                                    <select name="customer_country" id="customer_country" class="form-control">
                                        <option>Afghanistan</option>
                                        <option>&Aring;land Islands</option>
                                        <option>Albania</option>
                                        <option>Algeria</option>
                                        <option>American Samoa</option>
                                        <option>Andorra</option>
                                        <option>Angola</option>
                                        <option>Anguilla</option>
                                        <option>Antarctica</option>
                                        <option>Antigua and Barbuda</option>
                                        <option>Argentina</option>
                                        <option>Armenia</option>
                                        <option>Aruba</option>
                                        <option>Australia</option>
                                        <option>Austria</option>
                                        <option>Azerbaijan</option>
                                        <option>Bahamas</option>
                                        <option>Bahrain</option>
                                        <option>Bangladesh</option>
                                        <option>Barbados</option>
                                        <option>Belarus</option>
                                        <option>Belgium</option>
                                        <option>Belize</option>
                                        <option>Benin</option>
                                        <option>Bermuda</option>
                                        <option>Bhutan</option>
                                        <option>Bolivia, Plurinational State of</option>
                                        <option>Bosnia and Herzegovina</option>
                                        <option>Botswana</option>
                                        <option>Bouvet Island</option>
                                        <option>Brazil</option>
                                        <option>British Indian Ocean Territory</option>
                                        <option>Brunei Darussalam</option>
                                        <option>Bulgaria</option>
                                        <option>Burkina Faso</option>
                                        <option>Burundi</option>
                                        <option>Cambodia</option>
                                        <option>Cameroon</option>
                                        <option>Canada</option>
                                        <option>Cape Verde</option>
                                        <option>Cayman Islands</option>
                                        <option>Central African Republic</option>
                                        <option>Chad</option>
                                        <option>Chile</option>
                                        <option>China</option>
                                        <option>Christmas Island</option>
                                        <option>Cocos (Keeling) Islands</option>
                                        <option>Colombia</option>
                                        <option>Comoros</option>
                                        <option>Congo</option>
                                        <option>Congo, the Democratic Republic of the</option>
                                        <option>Cook Islands</option>
                                        <option>Costa Rica</option>
                                        <option>C&ocirc;te d'Ivoire</option>
                                        <option>Croatia</option>
                                        <option>Cuba</option>
                                        <option>Cyprus</option>
                                        <option>Czech Republic</option>
                                        <option>Denmark</option>
                                        <option>Djibouti</option>
                                        <option>Dominica</option>
                                        <option>Dominican Republic</option>
                                        <option>Ecuador</option>
                                        <option>Egypt</option>
                                        <option>El Salvador</option>
                                        <option>Equatorial Guinea</option>
                                        <option>Eritrea</option>
                                        <option>Estonia</option>
                                        <option>Ethiopia</option>
                                        <option>Falkland Islands (Malvinas)</option>
                                        <option>Faroe Islands</option>
                                        <option>Fiji</option>
                                        <option>Finland</option>
                                        <option>France</option>
                                        <option>French Guiana</option>
                                        <option>French Polynesia</option>
                                        <option>French Southern Territories</option>
                                        <option>Gabon</option>
                                        <option>Gambia</option>
                                        <option>Georgia</option>
                                        <option>Germany</option>
                                        <option>Ghana</option>
                                        <option>Gibraltar</option>
                                        <option>Greece</option>
                                        <option>Greenland</option>
                                        <option>Grenada</option>
                                        <option>Guadeloupe</option>
                                        <option>Guam</option>
                                        <option>Guatemala</option>
                                        <option>Guernsey</option>
                                        <option>Guinea</option>
                                        <option>Guinea-Bissau</option>
                                        <option>Guyana</option>
                                        <option>Haiti</option>
                                        <option>Heard Island and McDonald Islands</option>
                                        <option>Holy See (Vatican City State)</option>
                                        <option>Honduras</option>
                                        <option>Hong Kong</option>
                                        <option>Hungary</option>
                                        <option>Iceland</option>
                                        <option>India</option>
                                        <option>Indonesia</option>
                                        <option>Iran, Islamic Republic of</option>
                                        <option>Iraq</option>
                                        <option>Ireland</option>
                                        <option>Isle of Man</option>
                                        <option>Israel</option>
                                        <option>Italy</option>
                                        <option>Jamaica</option>
                                        <option>Japan</option>
                                        <option>Jersey</option>
                                        <option>Jordan</option>
                                        <option>Kazakhstan</option>
                                        <option>Kenya</option>
                                        <option>Kiribati</option>
                                        <option>Korea, Democratic People's Republic of</option>
                                        <option>Korea, Republic of</option>
                                        <option>Kuwait</option>
                                        <option>Kyrgyzstan</option>
                                        <option>Lao People's Democratic Republic</option>
                                        <option>Latvia</option>
                                        <option>Lebanon</option>
                                        <option>Lesotho</option>
                                        <option>Liberia</option>
                                        <option>Libyan Arab Jamahiriya</option>
                                        <option>Liechtenstein</option>
                                        <option>Lithuania</option>
                                        <option>Luxembourg</option>
                                        <option>Macao</option>
                                        <option>Macedonia, the former Yugoslav Republic of</option>
                                        <option>Madagascar</option>
                                        <option>Malawi</option>
                                        <option>Malaysia</option>
                                        <option>Maldives</option>
                                        <option>Mali</option>
                                        <option>Malta</option>
                                        <option>Marshall Islands</option>
                                        <option>Martinique</option>
                                        <option>Mauritania</option>
                                        <option>Mauritius</option>
                                        <option>Mayotte</option>
                                        <option>Mexico</option>
                                        <option>Micronesia, Federated States of</option>
                                        <option>Moldova, Republic of</option>
                                        <option>Monaco</option>
                                        <option>Mongolia</option>
                                        <option>Montenegro</option>
                                        <option>Montserrat</option>
                                        <option>Morocco</option>
                                        <option>Mozambique</option>
                                        <option>Myanmar</option>
                                        <option>Namibia</option>
                                        <option>Nauru</option>
                                        <option>Nepal</option>
                                        <option>Netherlands</option>
                                        <option>Netherlands Antilles</option>
                                        <option>New Caledonia</option>
                                        <option>New Zealand</option>
                                        <option>Nicaragua</option>
                                        <option>Niger</option>
                                        <option>Nigeria</option>
                                        <option>Niue</option>
                                        <option>Norfolk Island</option>
                                        <option>Northern Mariana Islands</option>
                                        <option>Norway</option>
                                        <option>Oman</option>
                                        <option>Pakistan</option>
                                        <option>Palau</option>
                                        <option>Palestinian Territory, Occupied</option>
                                        <option>Panama</option>
                                        <option>Papua New Guinea</option>
                                        <option>Paraguay</option>
                                        <option>Peru</option>
                                        <option>Philippines</option>
                                        <option>Pitcairn</option>
                                        <option>Poland</option>
                                        <option>Portugal</option>
                                        <option>Puerto Rico</option>
                                        <option>Qatar</option>
                                        <option>R&eacute;union</option>
                                        <option>Romania</option>
                                        <option>Russian Federation</option>
                                        <option>Rwanda</option>
                                        <option>Saint Barth&eacute;lemy</option>
                                        <option>Saint Helena, Ascension and Tristan da Cunha</option>
                                        <option>Saint Kitts and Nevis</option>
                                        <option>Saint Lucia</option>
                                        <option>Saint Martin (French part)</option>
                                        <option>Saint Pierre and Miquelon</option>
                                        <option>Saint Vincent and the Grenadines</option>
                                        <option>Samoa</option>
                                        <option>San Marino</option>
                                        <option>Sao Tome and Principe</option>
                                        <option>Saudi Arabia</option>
                                        <option>Senegal</option>
                                        <option>Serbia</option>
                                        <option>Seychelles</option>
                                        <option>Sierra Leone</option>
                                        <option>Singapore</option>
                                        <option>Slovakia</option>
                                        <option>Slovenia</option>
                                        <option>Solomon Islands</option>
                                        <option>Somalia</option>
                                        <option>South Africa</option>
                                        <option>South Georgia and the South Sandwich Islands</option>
                                        <option>Spain</option>
                                        <option>Sri Lanka</option>
                                        <option>Sudan</option>
                                        <option>Suriname</option>
                                        <option>Svalbard and Jan Mayen</option>
                                        <option>Swaziland</option>
                                        <option>Sweden</option>
                                        <option>Switzerland</option>
                                        <option>Syrian Arab Republic</option>
                                        <option>Taiwan, Province of China</option>
                                        <option>Tajikistan</option>
                                        <option>Tanzania, United Republic of</option>
                                        <option>Thailand</option>
                                        <option>Timor-Leste</option>
                                        <option>Togo</option>
                                        <option>Tokelau</option>
                                        <option>Tonga</option>
                                        <option>Trinidad and Tobago</option>
                                        <option>Tunisia</option>
                                        <option>Turkey</option>
                                        <option>Turkmenistan</option>
                                        <option>Turks and Caicos Islands</option>
                                        <option>Tuvalu</option>
                                        <option>Uganda</option>
                                        <option>Ukraine</option>
                                        <option>United Arab Emirates</option>
                                        <option>United Kingdom</option>
                                        <option>United States</option>
                                        <option>United States Minor Outlying Islands</option>
                                        <option>Uruguay</option>
                                        <option>Uzbekistan</option>
                                        <option>Vanuatu</option>
                                        <option>Venezuela, Bolivarian Republic of</option>
                                        <option>Viet Nam</option>
                                        <option>Virgin Islands, British</option>
                                        <option>Virgin Islands, U.S.</option>
                                        <option>Wallis and Futuna</option>
                                        <option>Western Sahara</option>
                                        <option>Yemen</option>
                                        <option>Zambia</option>
                                        <option>Zimbabwe</option>

                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="margin-top: 20px">
                                    <button class="btn btn-block btn-primary submit-btn" type="submit">Save</button>
                                </div>
                            </div>

                        </form>
                    @endif

                </div>
            </div>
        </div>

    </div>

    <div class="row" id="accounts">
        <!-- Extra small table start-->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 style="float: right; direction: rtl;">لیست مشتریان (Customer List)</h5>
                    @if(session("status"))
                        <div class="alert alert-success status" style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            {{session('status')}}
                        </div>

                    @endif
                    @if(session("error"))

                        <div class="alert alert-danger error" style="display:none;" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            {{session('error')}}
                        </div>

                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-xs table-hover" style="direction: ltr;">
                            <thead>
                             <tr>
                                <th><span style="float: right">#</span></th>
                                <th><span style="float: right;">نام مشتری (Customer Name)</span></th>
                                <th><span style="float: right;">کشور (Country)</span></th>
                                <th class="hideOnPrint"><span style="float: right;">فرمایشات (Orders)</span></th>
                                <th class="hideOnPrint"><span style="float: right;">عملیات (Action)</span></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($customers  as $m)
                                <tr>
                                    <td><span style="float:left;">{{$m->c_id}}</span></td>
                                    <td><span style="float:left;">{{$m->customer_name}}</span></td>
                                    <td><span style="float:left;">{{$m->customer_country}}</span></td>
                                    <td class="hideOnPrint"> <span style="float:right;"><a
                                                href="/dashboard/customer-account-for-orders/{{$m->c_id}}"
                                                class="btn btn-sm btn-warning">&nbsp;
                                            فرمایشات (Orders)</a></span></td>
                                    <td class="hideOnPrint">
                                        <span style="float:right;"><a
                                                href="/dashboard/customer-account-for-orders/{{$m->c_id}}/edit"
                                                class="btn btn-sm btn-info">&nbsp;ویرایش</a></span>
                                        @if(auth()->user()->role == 'SP')
                                            <span style="float:right;"> <button onclick="deleteCustomer({{$m->c_id}})"
                                                                               class="btn btn-danger btn-sm ">حذف</button></span>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Extra small table start-->
    </div>
@endsection

@section('scripts')
    <script>

        $('#customer_country').select2();


        function deleteCustomer(id) {

            swal({
                text: "مطمعین هستید ؟",
                buttons: true,
                dangerMode: true,
                buttons: {
                    confirm: {text: 'بلی', className: 'btn-danger'},
                    cancel: 'نخیر'
                },
            })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'DELETE',
                            data: {
                                '_token': '{{csrf_token()}}',
                            },
                            url: '/dashboard/customer-account-for-orders/' + id,
                            success: function (res) {

                                if (res.status == 'success') {
                                    $('.alert-success').show();
                                    $('.ur' + id).hide();
                                    window.location = '/dashboard/customer-account-for-orders'

                                } else {
                                    $('.alert-danger').show();
                                }
                                window.setTimeout(function () {
                                    $(".alert-success").fadeTo(500, 0).slideUp(500, function () {

                                        $(this).remove();
                                    });
                                }, 5000);


                            },

                        })
                    }
                });
        }


    </script>
@endsection
