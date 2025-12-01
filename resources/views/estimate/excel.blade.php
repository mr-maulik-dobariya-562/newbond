<table>
    <tbody>
        @foreach ($estimate as $data)
        <tr>
            <td style="background-color: #f2f2f2; font-size: 12pt; border-bottom: 1px solid #ddd;">
                {{ $data->bill_title }}-{{ $data->rate }}
                <?= $data->discount > 9 && $data->discount > 0 ? '  ('.round($data->discount).'%)' : '' ?>
                <?= $data->discount < 10 && $data->discount > 0 ? '  (0'.round($data->discount).'%)' : '' ?>
            </td>
            <td style="background-color: #f2f2f2; font-size: 12pt; border-bottom: 1px solid #ddd;">{{ $data->quantity }}</td>
            <td style="background-color: #f2f2f2; font-size: 12pt; border-bottom: 1px solid #ddd;"><?= $data->discount > 0 ? $data->rate - (($data->rate * $data->discount) / 100) : $data->rate ?></td>
            <td style="background-color: #f2f2f2; font-size: 12pt; border-bottom: 1px solid #ddd;">{{ $data->bill_title }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
