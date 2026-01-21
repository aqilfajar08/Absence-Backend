@php
    /**
     * @var \Illuminate\Support\Collection|\App\Models\User[] $users
     * @var int $year
     * @var int $month
     * @var int $daysInMonth
     * @var \App\Models\Company $company
     */
    \Carbon\Carbon::setLocale('id');

    // Prepare dynamic time ranges for Legend
    $cTimeIn = \Carbon\Carbon::parse($company->time_in ?? '08:00:00');
    $cLate1  = \Carbon\Carbon::parse($company->late_threshold_1 ?? '08:30:00');
    $cLate2  = \Carbon\Carbon::parse($company->late_threshold_2 ?? '09:00:00');
    $cLate3  = \Carbon\Carbon::parse($company->late_threshold_3 ?? '12:00:00');

    $range1Start = $cTimeIn->copy()->addMinute()->format('H:i');
    $range1End   = $cLate1->format('H:i');

    $range2Start = $cLate1->copy()->addMinute()->format('H:i');
    $range2End   = $cLate2->format('H:i');

    $range3Start = $cLate2->copy()->addMinute()->format('H:i');
    $range3End   = $cLate3->format('H:i');

    $range4Start = $cLate3->copy()->addMinute()->format('H:i');
@endphp
<table style="border-collapse: collapse; width: 100%; font-size: 11px;">
    <thead>
        <tr>
            <th colspan="{{ 3 + $daysInMonth + 15 }}" style="font-weight: bold; font-size: 16px; text-align: center; height: 30px; vertical-align: middle;">
                REKAP ABSENSI KARYAWAN PT. KASAU SINAR SAMUDERA {{ $year }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ 3 + $daysInMonth + 15 }}" style="font-weight: bold; font-size: 14px; text-align: center; height: 25px; vertical-align: middle;">
                PERIODE {{ strtoupper(\Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y')) }}
            </th>
        </tr>
        <tr>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #d9d9d9; width: 5px; vertical-align: middle;">No</th>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #d9d9d9; width: 15px; vertical-align: middle;">Dept</th>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #d9d9d9; width: 30px; vertical-align: middle;">Nama</th>
            
            <th colspan="{{ $daysInMonth }}" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #d9d9d9; vertical-align: middle;">Tanggal</th>
            
            {{-- Group Jumlah --}}
            <th colspan="3" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #00b050; color: #000000; vertical-align: middle;">Jumlah</th>
            
            {{-- Group Sub Total --}}
            <th colspan="6" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #0070c0; color: #000000; vertical-align: middle;">Sub Total</th>
            
            {{-- Group Total --}}
            <th colspan="6" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #ffff00; color: #000000; vertical-align: middle;">Total</th>
        </tr>
        <tr>
            @for($i = 1; $i <= $daysInMonth; $i++)
                @php
                    $currentDate = \Carbon\Carbon::createFromDate($year, $month, $i);
                    $isSunday = $currentDate->isSunday();
                    $headerColor = $isSunday ? '#ff0000' : '#d9d9d9';
                    $textColor = $isSunday ? '#ffffff' : '#000000';
                @endphp
                <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: {{ $headerColor}}; color: {{ $textColor }}; width: 8px;">{{ $i }}</th>
            @endfor

            {{-- Headers under Jumlah --}}
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #00b050; width: 15px;">GPH</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #00b050; width: 15px;">TJ</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #ffff00; width: 15px;">CUTI</th>

            {{-- Headers under Sub Total --}}
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #0070c0; width: 8px;">Cuti</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #0070c0; width: 8px;">Terlambat</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #0070c0; width: 8px;">Izin</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #0070c0; width: 8px;">Sakit</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #0070c0; width: 8px;">Alfa</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #0070c0; width: 8px;">Hadir</th>

            {{-- Headers under Total --}}
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #ffff00; width: 8px;">Cuti</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #ffff00; width: 8px;">Terlambat</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #ffff00; width: 8px;">Izin</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #ffff00; width: 8px;">Sakit</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #ffff00; width: 8px;">Alfa</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #ffff00; width: 8px;">Hadir</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $index => $user)
            @php
                $gphCount = 0; // Total multiplier for GP
                $tjCount = 0;  // Count for Tunjangan
                $cutiCount = 0;
                $izinCount = 0;
                $sakitCount = 0;
                $alpaCount = 0;
                $telatCount = 0; 
                $hadirCount = 0;
                $dlkCount = 0;
            @endphp
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000000;">{{ $user->department ?? '-' }}</td>
                <td style="border: 1px solid #000000;">{{ $user->name }}</td>

                @for($i = 1; $i <= $daysInMonth; $i++)
                    @php
                        $currentDate = \Carbon\Carbon::createFromDate($year, $month, $i);
                        $dateString = $currentDate->format('Y-m-d');
                        $isSunday = $currentDate->isSunday();
                        
                        // Check Attendance
                        $attendance = $user->attendance->first(function($att) use ($dateString) {
                            return \Carbon\Carbon::parse($att->date_attendance)->format('Y-m-d') === $dateString;
                        });
                        // Check Permit
                        $permit = $user->permits->firstWhere('date_permission', $dateString);
                        
                        $cellContent = '';
                        $bgColor = $isSunday ? '#ff0000' : '#ffffff'; 
                        $textColor = '#000000';

                        // Logic Priorities
                        if ($permit && $permit->is_approved == 'approved') {
                            if ($permit->permit_type == 'dlk') {
                                $cellContent = 'DLK';
                                $bgColor = '#757171'; 
                                $textColor = '#ffffff';
                                $gphCount += 1;
                                $tjCount += 1;
                                $dlkCount += 1;
                                $hadirCount += 1; 
                            } elseif ($permit->permit_type == 'sakit') {
                                $cellContent = 'Sakit';
                                $bgColor = '#BFBFBF';
                                $sakitCount++;
                            } elseif ($permit->permit_type == 'cuti') {
                                $cellContent = 'CUTI';
                                $bgColor = '#00B050';
                                $textColor = '#ffffff';
                                $cutiCount++;
                            } else {
                                $cellContent = 'IZIN';
                                $bgColor = '#757171';
                                $textColor = '#ffffff';
                                $izinCount++;
                            }
                        } elseif ($attendance && $attendance->time_in) {
                            $timeIn = \Carbon\Carbon::parse($attendance->time_in)->setDate($year, $month, $i);
                            $cellContent = $timeIn->format('H:i');
                            
                            // Check Lateness
                            $tTimeIn = \Carbon\Carbon::parse($company->time_in ?? '08:00:00')->setDate($year, $month, $i)->addSeconds(59);
                            $tLate1  = \Carbon\Carbon::parse($company->late_threshold_1 ?? '08:30:00')->setDate($year, $month, $i)->addSeconds(59);
                            $tLate2  = \Carbon\Carbon::parse($company->late_threshold_2 ?? '09:00:00')->setDate($year, $month, $i)->addSeconds(59);
                            $tLate3  = \Carbon\Carbon::parse($company->late_threshold_3 ?? '12:00:00')->setDate($year, $month, $i)->addSeconds(59);

                            if ($timeIn->lte($tTimeIn)) {
                                // Tepat Waktu
                                $bgColor = '#ffffff';
                                $gphCount += 1;
                                $tjCount += 1;
                                $hadirCount++;
                            } elseif ($timeIn->gt($tTimeIn) && $timeIn->lte($tLate1)) {
                                // Telat 1
                                $bgColor = '#548235';
                                $gphCount += ($company->gph_late_1_percent ?? 75) / 100;
                                $tjCount += 1;
                                $telatCount++;
                                $hadirCount++;
                            } elseif ($timeIn->gt($tLate1) && $timeIn->lte($tLate2)) {
                                // Telat 2
                                $bgColor = '#FFC000';
                                $gphCount += ($company->gph_late_2_percent ?? 70) / 100;
                                $tjCount += 1;
                                $telatCount++;
                                $hadirCount++;
                            } elseif ($timeIn->gt($tLate2) && $timeIn->lte($tLate3)) {
                                // Telat 3
                                $bgColor = '#FFFF00';
                                $gphCount += ($company->gph_late_3_percent ?? 65) / 100;
                                $tjCount += 1;
                                $telatCount++;
                                $hadirCount++;
                            } else {
                                // Setengah Hari (Level 4/Late > 12:00)
                                $bgColor = '#A9D08E';
                                $textColor = '#000000';
                                $cellContent = $timeIn->format('H:i');
                                $gphCount += ($company->gph_late_4_percent ?? 0) / 100;
                                $tjCount += 0;
                                $telatCount++; 
                            }
                        } elseif ($attendance && $attendance->status && $attendance->status != 'present') {
                            // Logic untuk status manual (tanpa time_in)
                            if ($attendance->status == 'permission') { 
                                $cellContent = 'IZIN';
                                $bgColor = '#757171';
                                $textColor = '#ffffff';
                                $izinCount++;
                            } elseif ($attendance->status == 'alpha') {
                                $cellContent = 'ALPHA';
                                $bgColor = '#5B9BD5';
                                $textColor = '#ffffff';
                                $alpaCount++;
                            } elseif ($attendance->status == 'leave') {
                                $cellContent = 'CUTI';
                                $bgColor = '#00B050';
                                $textColor = '#ffffff';
                                $cutiCount++;
                            } elseif ($attendance->status == 'sick') {
                                $cellContent = 'Sakit';
                                $bgColor = '#BFBFBF';
                                $textColor = '#000000';
                                $sakitCount++;
                            } elseif ($attendance->status == 'out_of_town') {
                                $cellContent = 'DLK';
                                $bgColor = '#757171'; 
                                $textColor = '#ffffff';
                                $gphCount += 1;
                                $tjCount += 1;
                                $dlkCount += 1;
                                $hadirCount += 1; 
                            }
                        } else {
                            if (!$isSunday) {
                                if (\Carbon\Carbon::parse($user->created_at)->lte($currentDate)) {
                                     if ($currentDate->lte(now())) {
                                         $alpaCount++;
                                         $cellContent = 'ALPHA';
                                         $bgColor = '#5B9BD5'; // Alpa Color
                                         $textColor = '#ffffff';
                                     }
                                }
                            }
                        }
                    @endphp
                    <td style="border: 1px solid #000000; text-align: center; background-color: {{ $bgColor }}; color: {{ $textColor }};">
                        {{ $cellContent }}
                    </td>
                @endfor

                @php
                    // Display Static Values only (as per user request)
                    $displayGPH = $user->gaji_pokok ?? 0;
                    $displayTJ = $user->tunjangan ?? 0;
                @endphp

                {{-- Group Jumlah (Show Static Rate) --}}
                <td style="border: 1px solid #000000; text-align: right;">{{ $displayGPH }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ $displayTJ }}</td>
                <td style="border: 1px solid #000000; text-align: center; background-color: #ffff00;">{{ $cutiCount }}</td>

                {{-- Group Sub Total --}}
                <td style="border: 1px solid #000000; text-align: center; background-color: #0070c0; color: #ffffff;">{{ $cutiCount }}</td>
                <td style="border: 1px solid #000000; text-align: center; background-color: #ffffff;">{{ $telatCount }}</td>
                <td style="border: 1px solid #000000; text-align: center; background-color: #ffffff;">{{ $izinCount }}</td>
                <td style="border: 1px solid #000000; text-align: center; background-color: #ffffff;">{{ $sakitCount }}</td>
                <td style="border: 1px solid #000000; text-align: center; background-color: #ffffff;">{{ $alpaCount }}</td>
                <td style="border: 1px solid #000000; text-align: center; background-color: #ffffff;">{{ $hadirCount }}</td>

                 {{-- Group Total --}}
                <td style="border: 1px solid #000000; text-align: center; background-color: #ffff00;">{{ $cutiCount }}</td>
                <td style="border: 1px solid #000000; text-align: center; background-color: #ffff00;">{{ $telatCount }}</td>
                <td style="border: 1px solid #000000; text-align: center; background-color: #ffff00;">{{ $izinCount }}</td>
                <td style="border: 1px solid #000000; text-align: center; background-color: #ffff00;">{{ $sakitCount }}</td>
                <td style="border: 1px solid #000000; text-align: center; background-color: #ffff00;">{{ $alpaCount }}</td>
                <td style="border: 1px solid #000000; text-align: center; background-color: #ffff00;">{{ $hadirCount }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- SPACER ROW --}}
<table style="border-collapse: collapse; width: 100%; font-size: 10px;">
    <tr><td colspan="30" style="height: 20px;"></td></tr>
    
    {{-- COMBINED LEGEND + DEDUCTION TABLE --}}
    {{-- Row 1: Legend Header + Deduction Header Row 1 --}}
    <tr style="height: 35px;">
        {{-- Legend Column --}}
        <td style="width: 25px;"></td>
        <td style="font-weight: bold; width: 200px;">Keterangan:</td>
        <td style="width: 20px;"></td>
        {{-- Deduction Headers --}}
        <th colspan="2" rowspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #d9d9d9; vertical-align: middle;">No</th>
        <th colspan="2" rowspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #d9d9d9; vertical-align: middle;">Nama</th>
        <th colspan="8" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #d9d9d9;">Terlambat</th>
        <th colspan="2" rowspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #d9d9d9; vertical-align: middle;">Sub Total I</th>
        <th colspan="6" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #d9d9d9;">Izin/Sakit/Alfa</th>
        <th colspan="2" rowspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #b4c6e7; vertical-align: middle;">Sub Total II</th>
        <th colspan="2" rowspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #f8cbad; vertical-align: middle;">Uniform Ded.</th>
        <th colspan="2" rowspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #ffff00; vertical-align: middle;">Total Deduction</th>
    </tr>
    
    {{-- Row 2: Legend Item 1 + Deduction Header Row 2 --}}
    <tr style="height: 30px;">
        <td style="background-color: #ff0000; border: 1px solid #000;"></td>
        <td>Hari Libur</td>
        <td></td>
        {{-- Deduction Sub Headers --}}
        <th colspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #548235;">I</th>
        <th colspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #FFC000;">II</th>
        <th colspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #FFFF00;">III</th>
        <th colspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #A9D08E;">IV</th>
        <th colspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #757171; color: #fff;">Izin</th>
        <th colspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #BFBFBF;">Sakit</th>
        <th colspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #5B9BD5; color: #fff;">Alfa</th>
    </tr>
    
    {{-- Data Rows: Legend Items + Deduction Data --}}
    @php
        $legendItems = [
            ['color' => '#548235', 'text' => 'Terlambat 1 ('.$range1Start.'-'.$range1End.') [Potong '.(100 - ($company->gph_late_1_percent ?? 75)).'%]'],
            ['color' => '#FFC000', 'text' => 'Terlambat 2 ('.$range2Start.'-'.$range2End.') [Potong '.(100 - ($company->gph_late_2_percent ?? 70)).'%]'],
            ['color' => '#FFFF00', 'text' => 'Terlambat 3 ('.$range3Start.'-'.$range3End.') [Potong '.(100 - ($company->gph_late_3_percent ?? 65)).'%]'],
            ['color' => '#A9D08E', 'text' => 'Setengah Hari (>'.$range4Start.') [Potong '.(100 - ($company->gph_late_4_percent ?? 0)).'%]'],
            ['color' => '#757171', 'text' => 'Dinas Luar Kota (DLK) / Izin'],
            ['color' => '#BFBFBF', 'text' => 'Sakit'],
            ['color' => '#00B050', 'text' => 'Off/Cuti'],
            ['color' => '#5B9BD5', 'text' => 'Alpa (Tanpa Keterangan)'],
        ];
    @endphp
    
    @foreach($users as $index => $user)
        @php
            $late1Count = 0; $late2Count = 0; $late3Count = 0; $late4Count = 0;
            $izinCount = 0; $sakitCount = 0; $alpaCount = 0;
            
            for($i = 1; $i <= $daysInMonth; $i++) {
                $currentDate = \Carbon\Carbon::createFromDate($year, $month, $i);
                $dateString = $currentDate->format('Y-m-d');
                $isSunday = $currentDate->isSunday();
                $attendance = $user->attendance->first(function($att) use ($dateString) {
                    return \Carbon\Carbon::parse($att->date_attendance)->format('Y-m-d') === $dateString;
                });
                $permit = $user->permits->firstWhere('date_permission', $dateString);
                
                if ($permit && $permit->is_approved == 'approved') {
                    if ($permit->permit_type == 'sakit') $sakitCount++;
                    elseif ($permit->permit_type == 'izin') $izinCount++;
                } elseif ($attendance && $attendance->time_in) {
                    $timeIn = \Carbon\Carbon::parse($attendance->time_in)->setDate($year, $month, $i);
                    $tTimeIn = \Carbon\Carbon::parse($company->time_in ?? '08:00:00')->setDate($year, $month, $i)->addSeconds(59);
                    $tLate1  = \Carbon\Carbon::parse($company->late_threshold_1 ?? '08:30:00')->setDate($year, $month, $i)->addSeconds(59);
                    $tLate2  = \Carbon\Carbon::parse($company->late_threshold_2 ?? '09:00:00')->setDate($year, $month, $i)->addSeconds(59);
                    $tLate3  = \Carbon\Carbon::parse($company->late_threshold_3 ?? '12:00:00')->setDate($year, $month, $i)->addSeconds(59);

                    if ($timeIn->lte($tTimeIn)) {
                        // On Time
                    } elseif ($timeIn->gt($tTimeIn) && $timeIn->lte($tLate1)) $late1Count++;
                    elseif ($timeIn->gt($tLate1) && $timeIn->lte($tLate2)) $late2Count++;
                    elseif ($timeIn->gt($tLate2) && $timeIn->lte($tLate3)) $late3Count++;
                    elseif ($timeIn->gt($tLate3)) $late4Count++;
                } elseif ($attendance && $attendance->status && $attendance->status != 'present') {
                    if ($attendance->status == 'permission') $izinCount++;
                    elseif ($attendance->status == 'alpha') $alpaCount++;
                    elseif ($attendance->status == 'sick') $sakitCount++;
                } else {
                    if (!$isSunday && \Carbon\Carbon::parse($user->created_at)->lte($currentDate) && $currentDate->lte(now())) {
                        $alpaCount++;
                    }
                }
            }

            $p1 = $company->gph_late_1_percent ?? 75;
            $p2 = $company->gph_late_2_percent ?? 70;
            $p3 = $company->gph_late_3_percent ?? 65;
            $p4 = $company->gph_late_4_percent ?? 0;

            $deductionLate1 = $late1Count * ((100 - $p1) / 100) * ($user->gaji_pokok ?? 0);
            $deductionLate2 = $late2Count * ((100 - $p2) / 100) * ($user->gaji_pokok ?? 0);
            $deductionLate3 = $late3Count * ((100 - $p3) / 100) * ($user->gaji_pokok ?? 0);
            $deductionLate4 = $late4Count * ((100 - $p4) / 100) * ($user->gaji_pokok ?? 0);

            $subTotalDeduction1 = $deductionLate1 + $deductionLate2 + $deductionLate3 + $deductionLate4;
            $deductionIzin = 0;
            $deductionSakit = 0;
            $deductionAlpa = 0;
            $subTotalDeduction2 = $deductionIzin + $deductionSakit + $deductionAlpa;
            $uniformDeduction = 0;
            $totalDeduction = $subTotalDeduction1 + $subTotalDeduction2 + $uniformDeduction;
        @endphp
        <tr>
            {{-- Legend Column --}}
            @if($index < count($legendItems))
                <td style="background-color: {{ $legendItems[$index]['color'] }}; border: 1px solid #000;"></td>
                <td>{{ $legendItems[$index]['text'] }}</td>
            @else
                <td></td>
                <td></td>
            @endif
            <td></td>
            {{-- Deduction Data --}}
            <td colspan="2" style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
            <td colspan="2" style="border: 1px solid #000; padding-left: 4px;">{{ $user->name }}</td>
            <td colspan="2" style="border: 1px solid #000; text-align: right; padding-right: 4px; background-color: #548235;">{{ $deductionLate1 > 0 ? number_format($deductionLate1) : '-' }}</td>
            <td colspan="2" style="border: 1px solid #000; text-align: right; padding-right: 4px; background-color: #FFC000;">{{ $deductionLate2 > 0 ? number_format($deductionLate2) : '-' }}</td>
            <td colspan="2" style="border: 1px solid #000; text-align: right; padding-right: 4px; background-color: #FFFF00;">{{ $deductionLate3 > 0 ? number_format($deductionLate3) : '-' }}</td>
            <td colspan="2" style="border: 1px solid #000; text-align: right; padding-right: 4px; background-color: #A9D08E;">{{ $deductionLate4 > 0 ? number_format($deductionLate4) : '-' }}</td>
            <td colspan="2" style="border: 1px solid #000; text-align: right; padding-right: 4px; background-color: #d9d9d9; font-weight: bold;">{{ $subTotalDeduction1 > 0 ? number_format($subTotalDeduction1) : '-' }}</td>
            <td colspan="2" style="border: 1px solid #000; text-align: right; padding-right: 4px; background-color: #757171; color: #fff;">{{ $deductionIzin > 0 ? number_format($deductionIzin) : '-' }}</td>
            <td colspan="2" style="border: 1px solid #000; text-align: right; padding-right: 4px; background-color: #BFBFBF;">{{ $deductionSakit > 0 ? number_format($deductionSakit) : '-' }}</td>
            <td colspan="2" style="border: 1px solid #000; text-align: right; padding-right: 4px; background-color: #5B9BD5; color: #fff;">{{ $deductionAlpa > 0 ? number_format($deductionAlpa) : '-' }}</td>
            <td colspan="2" style="border: 1px solid #000; text-align: right; padding-right: 4px; background-color: #b4c6e7; font-weight: bold;">{{ $subTotalDeduction2 > 0 ? number_format($subTotalDeduction2) : '-' }}</td>
            <td colspan="2" style="border: 1px solid #000; text-align: right; padding-right: 4px; background-color: #f8cbad;">-</td>
            <td colspan="2" style="border: 1px solid #000; text-align: right; padding-right: 4px; background-color: #ffff00; font-weight: bold;">{{ $totalDeduction > 0 ? number_format($totalDeduction) : '-' }}</td>
        </tr>
    @endforeach
    
    {{-- Remaining legend items if users < legend items --}}
    @for($j = count($users); $j < count($legendItems); $j++)
        <tr>
            <td style="background-color: {{ $legendItems[$j]['color'] }}; border: 1px solid #000;"></td>
            <td>{{ $legendItems[$j]['text'] }}</td>
            <td colspan="28"></td>
        </tr>
    @endfor
</table>

