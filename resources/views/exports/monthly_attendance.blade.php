@php
    /**
     * @var \Illuminate\Support\Collection|\App\Models\User[] $users
     * @var int $year
     * @var int $month
     * @var int $daysInMonth
     * @var \App\Models\Company $company
     */
    \Carbon\Carbon::setLocale('id');
@endphp
<table style="border-collapse: collapse; width: 100%; font-size: 11px;">
    <thead>
        <tr>
            <th colspan="{{ 4 + $daysInMonth + 15 }}" style="font-weight: bold; font-size: 16px; text-align: center; height: 30px; vertical-align: middle;">
                REKAP ABSENSI KARYAWAN PT. KASAU SINAR SAMUDERA {{ $year }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ 4 + $daysInMonth + 15 }}" style="font-weight: bold; font-size: 14px; text-align: center; height: 25px; vertical-align: middle;">
                PERIODE {{ strtoupper(\Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y')) }}
            </th>
        </tr>
        <tr>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #d9d9d9; width: 5px; vertical-align: middle;">No</th>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #d9d9d9; width: 15px; vertical-align: middle;">Dept</th>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #d9d9d9; width: 15px; vertical-align: middle;">Account No.</th>
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
                <td style="border: 1px solid #000000; text-align: center;">{{ $user->id }}</td>
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
    <tfoot>
        <tr>
            <td colspan="{{ 4 + $daysInMonth + 15 }}"></td>
        </tr>
        
        {{-- Complex Footer: Legend on Left (Rows 1-6), Deduction Table on Right (All Rows) --}}
        
        {{-- Header Row for Deduction Table (Row 1 of Footer) --}}
        <tr>
            {{-- Legend Header --}}
            <td colspan="5" style="font-weight: bold;">Keterangan:</td>
            
            <td colspan="1"></td> {{-- Small Spacer --}}

            {{-- Deduction Table Headers --}}
            <td rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; width: 5px; vertical-align: middle;">No</td>
            <td rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; width: 30px; vertical-align: middle;">Nama</td>
            <th colspan="4" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #d9d9d9; vertical-align: middle;">Terlambat</th>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #d9d9d9; vertical-align: middle; width: 15px;">Sub Total I</th>
            <th colspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #d9d9d9; vertical-align: middle;">Izin / Sakit</th>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 10px;">Kehadiran</th>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #b4c6e7; vertical-align: middle; width: 15px;">Sub Total II</th>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #f8cbad; vertical-align: middle; width: 15px;">Uniform Deduction</th>
            <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #ffff00; vertical-align: middle; width: 15px;">Total Deduction</th>

            {{-- Trailing Spacer to fill row --}}
            <td colspan="{{ (4 + $daysInMonth + 15) - (5 + 1 + 13) }}"></td>
        </tr>

        {{-- Row 2 of Footer (Legend Item 1, Deduction Sub-Headers) --}}
        <tr>
            <td style="background-color: #ff0000; width: 20px;"></td>
            <td colspan="4">Hari Libur</td>
            
            <td colspan="1"></td> {{-- Small Spacer --}}

            {{-- Deduction Sub Headers --}}
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #548235; color: #000000; width: 10px;">I</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #FFC000; color: #000000; width: 10px;">II</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #FFFF00; color: #000000; width: 10px;">III</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #A9D08E; color: #000000; width: 10px;">IV</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #757171; width: 10px;">Izin</th>
            <th style="border: 1px solid #000000; font-weight: bold; text-align: center; background-color: #BFBFBF; width: 10px;">Sakit</th>

            {{-- Trailing Spacer --}}
            <td colspan="{{ (4 + $daysInMonth + 15) - (5 + 1 + 13) }}"></td>
        </tr>

        {{-- Loop for Users (Deduction Body) combined with Legend Items --}}
        @foreach($users as $index => $user)
            @php
                 // Recalculate Logic for this user (unfortunately necessary unless we cached it)
                 // We will just do a simplified check or copy logic.
                 // Ideally we should have pre-calculated this in Controller, but for now we repeat logic.
                 $late1Count = 0; $late2Count = 0; $late3Count = 0; $late4Count = 0;
                 $izinCount = 0; $sakitCount = 0; $alpaCount = 0;
                 // ... (Repeat loop over days for this user to get counts) ...
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
                                // On Time - No Deduction
                            } elseif ($timeIn->gt($tTimeIn) && $timeIn->lte($tLate1)) $late1Count++;
                            elseif ($timeIn->gt($tLate1) && $timeIn->lte($tLate2)) $late2Count++;
                            elseif ($timeIn->gt($tLate2) && $timeIn->lte($tLate3)) $late3Count++;
                            elseif ($timeIn->gt($tLate3)) $late4Count++;
                        } elseif ($attendance && $attendance->status && $attendance->status != 'present') {
                             if ($attendance->status == 'permission') $izinCount++;
                             elseif ($attendance->status == 'alpha') $alpaCount++;
                             elseif ($attendance->status == 'sick') $sakitCount++;
                             // Note: 'leave' (cuti) and 'out_of_town' (DLK) usually do not trigger monetary deductions in this context,
                             // or if they do, variables might need to be added. 
                             // Based on current calc:
                             // $deductionIzin = $izinCount * ...
                             // $deductionSakit = $sakitCount * ...
                             // $deductionAlpa = $alpaCount * ...
                             // So we only update these counters.
                        } else {
                            if (!$isSunday && \Carbon\Carbon::parse($user->created_at)->lte($currentDate) && $currentDate->lte(now())) {
                                $alpaCount++;
                            }
                        }
                  }



                $p1 = $company->gph_late_1_percent ?? 75; // Late 1
                $p2 = $company->gph_late_2_percent ?? 70; // Late 2
                $p3 = $company->gph_late_3_percent ?? 65; // Late 3
                $p4 = $company->gph_late_4_percent ?? 0;  // Late 4 (Setengah Hari)

                $deductionLate1 = $late1Count * ((100 - $p1) / 100) * ($user->gaji_pokok ?? 0);
                $deductionLate2 = $late2Count * ((100 - $p2) / 100) * ($user->gaji_pokok ?? 0);
                $deductionLate3 = $late3Count * ((100 - $p3) / 100) * ($user->gaji_pokok ?? 0);
                $deductionLate4 = $late4Count * ((100 - $p4) / 100) * ($user->gaji_pokok ?? 0);

                $subTotalDeduction1 = $deductionLate1 + $deductionLate2 + $deductionLate3 + $deductionLate4;
                $deductionIzin = $izinCount * 1.00 * ($user->gaji_pokok ?? 0);
                $deductionSakit = $sakitCount * 1.00 * ($user->gaji_pokok ?? 0);
                $deductionAlpa = $alpaCount * 1.00 * ($user->gaji_pokok ?? 0);
                $subTotalDeduction2 = $deductionIzin + $deductionSakit + $deductionAlpa;
                $uniformDeduction = 0;
                $totalDeduction = $subTotalDeduction1 + $subTotalDeduction2 + $uniformDeduction;
            @endphp
            
            <tr>
                {{-- Legend Items --}}
                @if($index == 0)
                     <td style="background-color: #548235;"></td>
                    <td colspan="4">Terlambat 1 (08:01-08:30) [Potong {{ 100 - ($company->gph_late_1_percent ?? 75) }}%]</td>
                @elseif($index == 1)
                    <td style="background-color: #FFC000;"></td>
                    <td colspan="4">Terlambat 2 (08:31-09:00) [Potong {{ 100 - ($company->gph_late_2_percent ?? 70) }}%]</td>
                @elseif($index == 2)
                    <td style="background-color: #FFFF00;"></td>
                    <td colspan="4">Terlambat 3 (09:01-12:00) [Potong {{ 100 - ($company->gph_late_3_percent ?? 65) }}%]</td>
                @elseif($index == 3)
                     <td style="background-color: #A9D08E;"></td>
                    <td colspan="4">Setengah Hari (>12:01) [Potong {{ 100 - ($company->gph_late_4_percent ?? 0) }}%]</td>
                @elseif($index == 4)
                     <td style="background-color: #757171; color: white;"></td>
                    <td colspan="4">Dinas Luar Kota (DLK) [GPH 100%]</td>
                @elseif($index == 5)
                     <td style="background-color: #757171; color: white;"></td>
                    <td colspan="4">Izin</td>
                @elseif($index == 6)
                     <td style="background-color: #BFBFBF; color: 000000;"></td>
                    <td colspan="4">Sakit</td>
                @elseif($index == 7)
                     <td style="background-color: #00B050; color: white;"></td>
                    <td colspan="4">Off/Cuti ONLY GP</td>
                @elseif($index == 8)
                     <td style="background-color: #5B9BD5; color: white;"></td>
                    <td colspan="4">Alpa (Tanpa Keterangan)</td>
                @else
                    <td colspan="5"></td>
                @endif

                <td colspan="1"></td> {{-- Small Spacer --}}

                {{-- Deduction Body Columns --}}
                <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000000;">{{ $user->name }}</td>
                <td style="border: 1px solid #000000; text-align: right; background-color: #548235;">{{ $deductionLate1 > 0 ? number_format($deductionLate1) : '-' }}</td>
                <td style="border: 1px solid #000000; text-align: right; background-color: #FFC000;">{{ $deductionLate2 > 0 ? number_format($deductionLate2) : '-' }}</td>
                <td style="border: 1px solid #000000; text-align: right; background-color: #FFFF00;">{{ $deductionLate3 > 0 ? number_format($deductionLate3) : '-' }}</td>
                <td style="border: 1px solid #000000; text-align: right; background-color: #A9D08E;">{{ $deductionLate4 > 0 ? number_format($deductionLate4) : '-' }}</td>
                <td style="border: 1px solid #000000; text-align: right; background-color: #d9d9d9;">{{ $subTotalDeduction1 > 0 ? number_format($subTotalDeduction1) : '-' }}</td>
                <td style="border: 1px solid #000000; text-align: right; background-color: #757171;">{{ $deductionIzin > 0 ? number_format($deductionIzin) : '-' }}</td>
                <td style="border: 1px solid #000000; text-align: right; background-color: #BFBFBF;">{{ $deductionSakit > 0 ? number_format($deductionSakit) : '-' }}</td>
                <td style="border: 1px solid #000000; text-align: right; background-color: #5B9BD5;">{{ $deductionAlpa > 0 ? number_format($deductionAlpa) : '-' }}</td>
                <td style="border: 1px solid #000000; text-align: right; background-color: #b4c6e7;">{{ $subTotalDeduction2 > 0 ? number_format($subTotalDeduction2) : '-' }}</td>
                <td style="border: 1px solid #000000; text-align: right; background-color: #f8cbad;">-</td>
                <td style="border: 1px solid #000000; text-align: right; background-color: #ffff00;">{{ $totalDeduction > 0 ? number_format($totalDeduction) : '-' }}</td>

                {{-- Trailing Spacer --}}
                <td colspan="{{ (4 + $daysInMonth + 15) - (5 + 1 + 13) }}"></td>
            </tr>
        @endforeach

        {{-- Fill Remaining Legend Items if Users < 11 --}}
        @for($j = $users->count(); $j <= 8; $j++)
             <tr>
                @if($j == 0)
                     <td style="background-color: #548235;"></td>
                    <td colspan="4">Terlambat 1 (08:01-08:30) [Potong {{ 100 - ($company->gph_late_1_percent ?? 75) }}%]</td>
                @elseif($j == 1)
                    <td style="background-color: #FFC000;"></td>
                    <td colspan="4">Terlambat 2 (08:31-09:00) [Potong {{ 100 - ($company->gph_late_2_percent ?? 70) }}%]</td>
                @elseif($j == 2)
                    <td style="background-color: #FFFF00;"></td>
                    <td colspan="4">Terlambat 3 (09:01-12:00) [Potong {{ 100 - ($company->gph_late_3_percent ?? 65) }}%]</td>
                @elseif($j == 3)
                     <td style="background-color: #A9D08E;"></td>
                    <td colspan="4">Setengah Hari (>12:01) [Potong {{ 100 - ($company->gph_late_4_percent ?? 0) }}%]</td>
                @elseif($j == 4)
                     <td style="background-color: #757171; color: white;"></td>
                    <td colspan="4">Dinas Luar Kota (DLK) [Gaji 100%]</td>
                @elseif($j == 5)
                     <td style="background-color: #757171; color: white;"></td>
                    <td colspan="4">Izin</td>
                @elseif($j == 6)
                     <td style="background-color: #BFBFBF; color: 000000;"></td>
                    <td colspan="4">Sakit</td>
                @elseif($j == 7)
                     <td style="background-color: #00B050; color: white;"></td>
                    <td colspan="4">Cuti</td>
                @elseif($j == 8)
                     <td style="background-color: #5B9BD5; color: white;"></td>
                    <td colspan="4">Alpa</td>
                @else
                    <td colspan="5"></td>
                @endif
                
                <td colspan="1"></td> {{-- Spacer --}}

                {{-- Empty Deduction Cells --}}
                <td style="border: 1px solid #000000; text-align: center;"> - </td>
                <td style="border: 1px solid #000000;"> - </td>
                <td style="border: 1px solid #000000; background-color: #548235;"></td>
                <td style="border: 1px solid #000000; background-color: #FFC000;"></td>
                <td style="border: 1px solid #000000; background-color: #FFFF00;"></td>
                <td style="border: 1px solid #000000; background-color: #A9D08E;"></td>
                <td style="border: 1px solid #000000; background-color: #d9d9d9;"></td>
                <td style="border: 1px solid #000000; background-color: #757171;"></td>
                <td style="border: 1px solid #000000; background-color: #BFBFBF;"></td>
                <td style="border: 1px solid #000000; background-color: #5B9BD5;"></td>
                <td style="border: 1px solid #000000; background-color: #b4c6e7;"></td>
                <td style="border: 1px solid #000000; background-color: #f8cbad;"></td>
                <td style="border: 1px solid #000000; background-color: #ffff00;"></td>

                 {{-- Trailing Spacer --}}
                <td colspan="{{ (4 + $daysInMonth + 15) - (5 + 1 + 13) }}"></td>
            </tr>
        @endfor
    </tfoot>
</table>
