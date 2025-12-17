<table>
    <thead>
        <!-- Month and Year Header -->
        <tr style="height: 28px;">
            <th colspan="{{ 7 + count($monthDays) }}"
                style="background-color: #1f4e79; color: white; font-weight: bold; text-align: center; font-size: 16px; padding: 8px; vertical-align: middle;">
                LAPORAN ABSENSI KARYAWAN - {{ \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('F Y') }}
            </th>
        </tr>
        <!-- Column Headers -->
        <tr style="height: 32px;">
            <th style="background-color: #4472C4; color: white; font-weight: bold; text-align: center; width: 35px; padding: 6px; vertical-align: middle;">No</th>
            <th style="background-color: #4472C4; color: white; font-weight: bold; text-align: center; min-width: 150px; padding: 6px; vertical-align: middle;">Nama Karyawan</th>
            <th style="background-color: #4472C4; color: white; font-weight: bold; text-align: center; min-width: 100px; padding: 6px; vertical-align: middle;">Departemen</th>
            <th style="background-color: #4472C4; color: white; font-weight: bold; text-align: center; min-width: 100px; padding: 6px; vertical-align: middle;">Jabatan</th>
            <th style="background-color: #4472C4; color: white; font-weight: bold; text-align: center; width: 60px; padding: 6px; vertical-align: middle;">Izin</th>
            <th style="background-color: #4472C4; color: white; font-weight: bold; text-align: center; width: 80px; padding: 6px; vertical-align: middle;">GP<br><small>(Gaji Pokok)</small></th>
            <th style="background-color: #4472C4; color: white; font-weight: bold; text-align: center; width: 80px; padding: 6px; vertical-align: middle;">TJ<br><small>(Tunjangan)</small></th>
            @foreach ($monthDays as $day)
                @php
                    $dayNumber = \Carbon\Carbon::parse($day)->format('d');
                    $dayName = \Carbon\Carbon::parse($day)->locale('id')->translatedFormat('D');
                @endphp
                <th style="background-color: #4472C4; color: white; font-weight: bold; text-align: center; width: 55px; font-size: 11px; padding: 4px; vertical-align: middle;">
                    {{ $dayNumber }}<br><small>{{ $dayName }}</small>
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach ($userAttendanceData as $userData)
            <tr style="height: 24px;">
                <td style="text-align: center; padding: 5px; vertical-align: middle; font-size: 11px;">{{ $no++ }}</td>
                <td style="padding: 5px; vertical-align: middle; font-size: 11px;">{{ $userData['user']->name }}</td>
                <td style="padding: 5px; vertical-align: middle; font-size: 11px;">{{ $userData['user']->department }}</td>
                <td style="padding: 5px; vertical-align: middle; font-size: 11px;">{{ $userData['user']->position }}</td>
                <td style="text-align: center; padding: 5px; vertical-align: middle; font-size: 11px;">{{ $userData['permit_count'] }}</td>
                <td style="text-align: center; padding: 5px; vertical-align: middle; font-size: 10px;">{{ $userData['user']->gaji_pokok ? 'Rp ' . number_format($userData['user']->gaji_pokok, 0, ',', '.') : '-' }}</td>
                <td style="text-align: center; padding: 5px; vertical-align: middle; font-size: 10px;">{{ $userData['user']->tunjangan ? 'Rp ' . number_format($userData['user']->tunjangan, 0, ',', '.') : '-' }}</td>
                @foreach ($monthDays as $day)
                    @php
                        $attendance = $userData['attendance'][$day];
                        $timeIn = $attendance ? $attendance->time_in_formatted : '';

                        // Determine background color and display text based on attendance
                        $bgColor = '';
                        $displayText = '';

                        if ($attendance && $timeIn) {
                            $timeInCarbon = \Carbon\Carbon::parse($attendance->time_in);
                            $earlyTime = \Carbon\Carbon::createFromTime(8, 0, 0); // 08:00 AM
                            $onTimeEnd = \Carbon\Carbon::createFromTime(8, 30, 0); // 08:30 AM
                            $lateEnd = \Carbon\Carbon::createFromTime(9, 0, 0); // 09:00 AM

                            if ($timeInCarbon->lte($earlyTime)) {
                                $bgColor = 'background-color: #d4edda; border: 1px solid #c3e6cb;'; // Light Green - Datang Awal
                            } elseif ($timeInCarbon->lte($onTimeEnd)) {
                                $bgColor = 'background-color: #d4edda; border: 1px solid #c3e6cb;'; // Light Green - Tepat Waktu
                            } elseif ($timeInCarbon->lte($lateEnd)) {
                                $bgColor = 'background-color: #fff3cd; border: 1px solid #fdbf47;'; // Light Orange - Terlambat
                            } else {
                                $bgColor = 'background-color: #f8d7da; border: 1px solid #f5c6cb;'; // Light Red - Sangat Terlambat
                            }
                            $displayText = $timeInCarbon->format('H:i');
                        } else {
                            // Check if it's a weekend or if user was absent
                            $dayOfWeek = \Carbon\Carbon::parse($day)->dayOfWeek;
                            if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                                // Sunday = 0, Saturday = 6
                                $bgColor = 'background-color: #e2e3e5; border: 1px solid #d6d8db;'; // Gray - Weekend
                                $displayText = 'L';
                            } else {
                                $bgColor = 'background-color: #dc3545; border: 1px solid #b02a37; color: white;'; // Bold Red - Tidak Hadir
                                $displayText = 'T';
                            }
                        }
                    @endphp
                    <td style="{{ $bgColor }} text-align: center; font-size: 11px; padding: 4px; vertical-align: middle; font-weight: bold;">{{ $displayText }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>

    <!-- Legend/Description Section -->
    <tbody>
        <tr style="height: 15px;"><td colspan="{{ 7 + count($monthDays) }}">&nbsp;</td></tr>
        <tr style="height: 28px;">
            <td colspan="12" style="font-weight: bold; font-size: 14px; background-color: #f8f9fa; padding: 8px; text-align: center; vertical-align: middle;">
                KETERANGAN STATUS ABSENSI
            </td>
        </tr>
         <tr style="height: 32px;">
             <td colspan="4" style="background-color: #d4edda; text-align: center; font-weight: bold; padding: 8px; border: 1px solid #c3e6cb; font-size: 11px; vertical-align: middle;">
                 Tepat Waktu<br><small>(08:01 - 08:30)</small>
             </td>
             <td colspan="4" style="background-color: #fff3cd; text-align: center; font-weight: bold; padding: 8px; border: 1px solid #fdbf47; font-size: 11px; vertical-align: middle;">
                 Terlambat<br><small>(08:31 - 09:00)</small>
             </td>
             <td colspan="4" style="background-color: #f8d7da; text-align: center; font-weight: bold; padding: 8px; border: 1px solid #f5c6cb; font-size: 11px; vertical-align: middle;">
                 Sangat Terlambat<br><small>(09:01 - 12:00)</small>
             </td>
         </tr>
         <tr style="height: 26px;">
             <td colspan="4" style="background-color: #e2e3e5; text-align: center; font-weight: bold; padding: 6px; border: 1px solid #d6d8db; font-size: 11px; vertical-align: middle;">
                 <strong>L</strong> = Libur
             </td>
             <td colspan="4" style="background-color: #dc3545; color: white; text-align: center; font-weight: bold; padding: 6px; border: 1px solid #b02a37; font-size: 11px; vertical-align: middle;">
                 <strong>T</strong> = Tidak Hadir
             </td>
             <td colspan="4" style="background-color: #e2e3e5; text-align: center; font-weight: bold; padding: 6px; border: 1px solid #d6d8db; font-size: 11px; vertical-align: middle;">
                 (Weekend/Absent)
             </td>
         </tr>
        <tr style="height: 10px;"><td colspan="{{ 7 + count($monthDays) }}">&nbsp;</td></tr>
        <tr style="height: 22px;">
            <td colspan="{{ 7 + count($monthDays) }}" style="font-weight: bold; background-color: #f8f9fa; padding: 6px; font-size: 11px; vertical-align: middle;">
                Periode: {{ \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr style="height: 22px;">
            <td colspan="{{ 7 + count($monthDays) }}" style="font-weight: bold; background-color: #f8f9fa; padding: 6px; font-size: 11px; vertical-align: middle;">
                Dibuat: {{ \Carbon\Carbon::now('Asia/Makassar')->locale('id')->translatedFormat('d F Y, H:i') }} WITA
            </td>
        </tr>
    </tbody>
</table>
