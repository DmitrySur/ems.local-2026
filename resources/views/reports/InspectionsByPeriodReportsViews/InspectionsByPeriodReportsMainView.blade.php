<table style="font-family: 'Times New Roman'; font-size: 12pt;">
    <thead>
    <tr>
        <td colspan="10" height="74" align="center" valign="top">
            Отчет о выполнении нормативов личного участия<br>
            в организации безопасности движения руководителями<br>
            подразделений Электромеханической службы Дирекции инфраструктуры<br>
            {{$datePeriod}}
        </td>
    </tr>
    <tr>
        <td rowspan="2" width="7" align="center" valign="center" style="border: 1px solid #000000">№ п/п</td>
        <td rowspan="2" width="22" align="center" valign="center" style="border: 1px solid #000000">Ф.И.О.</td>
        <td rowspan="2" width="15" align="center" valign="center" style="border: 1px solid #000000">Должность</td>
        <td colspan="2" align="center" valign="center" height="52" style="border: 1px solid #000000">«День
            безопасности»
        </td>
        <td colspan="2" align="center" valign="center" height="52" style="border: 1px solid #000000">Проверка работы<br>подразделений
            в ночное время
        </td>
        <td colspan="2" align="center" valign="center" height="52" style="border: 1px solid #000000">Внеплановая
            проверка работы<br>подразделений<br>в дневное/ночное время
        </td>
        <td rowspan="2" width="16" align="center" valign="center" style="border: 1px solid #000000">Примечание<br>(отпуск,
            больничный<br>и т.п.)
        </td>
    </tr>
    <tr>
        <td width="15" align="center" valign="center" style="border: 1px solid #000000">Дата\Время</td>
        <td width="15" align="center" valign="center" style="border: 1px solid #000000">Подразделение</td>
        <td width="15" align="center" valign="center" style="border: 1px solid #000000">Дата\Время</td>
        <td width="15" align="center" valign="center" style="border: 1px solid #000000">Подразделение</td>
        <td width="15" align="center" valign="center" style="border: 1px solid #000000">Дата\Время</td>
        <td width="15" align="center" valign="center" style="border: 1px solid #000000">Подразделение</td>
    </tr>
    </thead>
    <tbody>
    @foreach($listInspectionArray as $inspectionKey => $inspectList)
        <tr align="center" valign="top">
            <td rowspan="{{$inspectList['maxCount'] ?? 1}}" style="border: 1px solid #000000" align="center"
                valign="top">
                {{$loop->iteration}}
            </td>
            <td rowspan="{{$inspectList['maxCount'] ?? 1}}" style="border: 1px solid #000000" align="center"
                valign="top">
                @if(isset($uniqueInspector[$inspectionKey][0]['short_name']))
                    {{$uniqueInspector[$inspectionKey][0]['short_name']}}
                @endif
            </td>
            <td rowspan="{{$inspectList['maxCount'] ?? 1}}" style="border: 1px solid #000000" align="center"
                valign="top">
                @if(isset($uniqueInspector[$inspectionKey][0]['division_short_name']))
                    {{$uniqueInspector[$inspectionKey][0]['position']}}<br>
                    {{$uniqueInspector[$inspectionKey][0]['division_short_name']}}<br>
                @endif
            </td>
            <!-- День безопасности 1 строка -->
            <td style="border: 1px solid #000000" align="center" valign="top">
                @if(isset($inspectList['day_security'][0]['date_start']))
                    {{date('d.m.Y', strtotime($inspectList['day_security'][0]['date_start']))}}
                @endif
                @if(isset($inspectList['day_security'][0]['start_time']) && $inspectList['day_security'][0]['end_time'])
                    <br>
                    {{date('H:i', strtotime($inspectList['day_security'][0]['start_time']))}}
                    -{{date('H:i', strtotime($inspectList['day_security'][0]['end_time']))}}
                @endif
            </td>
            <td style="border: 1px solid #000000" align="center" valign="top">
                @if(isset($inspectList['day_security'][0]['subdivisions']))
                    {{implode('; ', json_decode($inspectList['day_security'][0]['subdivisions']))}}
                @endif
            </td>
            <!-- Ночная проверка 1 строка -->
            <td style="border: 1px solid #000000" align="center" valign="top">
                @if(isset($inspectList['night_inspection'][0]['date_start']))
                    {{date('d.m.Y', strtotime($inspectList['night_inspection'][0]['date_start']))}}
                @endif
                @if(isset($inspectList['night_inspection'][0]['start_time']) && $inspectList['night_inspection'][0]['end_time'])
                    <br>
                    {{date('H:i', strtotime($inspectList['night_inspection'][0]['start_time']))}}
                    -{{date('H:i', strtotime($inspectList['night_inspection'][0]['end_time']))}}
                @endif
            </td>
            <td style="border: 1px solid #000000" align="center" valign="top">
                @if(isset($inspectList['night_inspection'][0]['subdivisions']))
                    {{implode('; ', json_decode($inspectList['night_inspection'][0]['subdivisions']))}}
                @endif
            </td>
            <!-- Внезапная проверка 1 строка surprise_inspection-->
            <td style="border: 1px solid #000000" align="center" valign="top">
                @if(isset($inspectList['surprise_inspection'][0]['date_start']))
                    {{date('d.m.Y', strtotime($inspectList['surprise_inspection'][0]['date_start']))}}
                @endif
                @if(isset($inspectList['surprise_inspection'][0]['start_time']) && $inspectList['surprise_inspection'][0]['end_time'])
                    <br>
                    {{date('H:i', strtotime($inspectList['surprise_inspection'][0]['start_time']))}}
                    -{{date('H:i', strtotime($inspectList['surprise_inspection'][0]['end_time']))}}
                @endif
            </td>
            <td style="border: 1px solid #000000" align="center" valign="top">
                @if(isset($inspectList['surprise_inspection'][0]['subdivisions']))
                    {{implode('; ', json_decode($inspectList['surprise_inspection'][0]['subdivisions']))}}
                @endif
            </td>
            <td style="border: 1px solid #000000" rowspan="{{$inspectList['maxCount'] ?? 1}}"></td>
        </tr>
        @if(count($inspectList) > 1)
            @for($i = 1; $i <= $inspectList['maxCount'] - 1; $i++)
                <tr>

                    <!-- День безопасности остальные строки -->
                    <td style="border: 1px solid #000000" align="center" valign="top">
                        @if(isset($inspectList['day_security'][$i]['date_start']))
                            {{date('d.m.Y', strtotime($inspectList['day_security'][$i]['date_start']))}}
                        @endif
                        @if(isset($inspectList['day_security'][$i]['start_time']) && $inspectList['day_security'][$i]['end_time'])
                            <br>
                            {{date('H:i', strtotime($inspectList['day_security'][$i]['start_time']))}}
                            -{{date('H:i', strtotime($inspectList['day_security'][$i]['end_time']))}}
                        @endif
                    </td>
                    <td style="border: 1px solid #000000" align="center" valign="top">
                        @if(isset($inspectList['day_security'][$i]['subdivisions']))
                            {{implode('; ', json_decode($inspectList['day_security'][$i]['subdivisions']))}}
                        @endif
                    </td>
                    <!-- Ночная проверка остальные строки -->
                    <td style="border: 1px solid #000000" align="center" valign="top">
                        @if(isset($inspectList['night_inspection'][$i]['date_start']))
                            {{date('d.m.Y', strtotime($inspectList['night_inspection'][$i]['date_start']))}}
                        @endif
                        @if(isset($inspectList['night_inspection'][$i]['start_time']) && $inspectList['night_inspection'][$i]['end_time'])
                            <br>
                            {{date('H:i', strtotime($inspectList['night_inspection'][$i]['start_time']))}}
                            -{{date('H:i', strtotime($inspectList['night_inspection'][$i]['end_time']))}}
                        @endif
                    </td>
                    <td style="border: 1px solid #000000" align="center" valign="top">
                        @if(isset($inspectList['night_inspection'][$i]['subdivisions']))
                            {{implode('; ', json_decode($inspectList['night_inspection'][$i]['subdivisions']))}}
                        @endif
                    </td>
                    <!-- Внезапная проверка остальные строки surprise_inspection-->
                    <td style="border: 1px solid #000000" align="center" valign="top">
                        @if(isset($inspectList['surprise_inspection'][$i]['date_start']))
                            {{date('d.m.Y', strtotime($inspectList['surprise_inspection'][$i]['date_start']))}}
                        @endif
                        @if(isset($inspectList['surprise_inspection'][$i]['start_time']) && $inspectList['surprise_inspection'][$i]['end_time'])
                            <br>
                            {{date('H:i', strtotime($inspectList['surprise_inspection'][$i]['start_time']))}}
                            -{{date('H:i', strtotime($inspectList['surprise_inspection'][$i]['end_time']))}}
                        @endif
                    </td>
                    <td style="border: 1px solid #000000" align="center" valign="top">
                        @if(isset($inspectList['surprise_inspection'][$i]['subdivisions']))
                            {{implode('; ', json_decode($inspectList['surprise_inspection'][$i]['subdivisions']))}}
                        @endif
                    </td>
                    <td></td>
                </tr>
            @endfor
        @endif
    @endforeach
    <tr>
        <td style="border: 1px solid #000000" align="center" valign="center"></td>
        <td style="border: 1px solid #000000" align="center" valign="center" colspan="3"></td>
        <td style="border: 1px solid #000000" align="center" valign="center">
            @if(isset($inspectorCounter['day_security']))
                {{$inspectorCounter['day_security']}}
            @endif
        </td>
        <td style="border: 1px solid #000000" align="center" valign="center"></td>
        <td style="border: 1px solid #000000" align="center" valign="center">
            @if(isset($inspectorCounter['night_inspection']))
                {{$inspectorCounter['night_inspection']}}
            @endif
        </td>
        <td style="border: 1px solid #000000" align="center" valign="center"></td>
        <td style="border: 1px solid #000000" align="center" valign="center">
            @if(isset($inspectorCounter['surprise_inspection']))
                {{$inspectorCounter['surprise_inspection']}}
            @endif
        </td>
        <td style="border: 1px solid #000000" align="center" valign="center"></td>
    </tr>
    </tbody>
</table>
