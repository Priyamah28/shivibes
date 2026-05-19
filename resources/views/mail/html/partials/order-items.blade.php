<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:16px 0;border:1px solid #e3f0ea;border-radius:8px;overflow:hidden;">
    <tr>
        <td style="background-color:#f4f9f6;padding:10px 14px;font-size:12px;font-weight:600;color:#3a735c;text-transform:uppercase;letter-spacing:0.06em;">Item</td>
        <td align="center" style="background-color:#f4f9f6;padding:10px 8px;font-size:12px;font-weight:600;color:#3a735c;text-transform:uppercase;">Qty</td>
        <td align="right" style="background-color:#f4f9f6;padding:10px 14px;font-size:12px;font-weight:600;color:#3a735c;text-transform:uppercase;">Amount</td>
    </tr>
    @foreach ($items as $item)
        <tr>
            <td style="padding:12px 14px;border-top:1px solid #e3f0ea;font-size:14px;color:#274a3d;">
                {{ $item->product?->name ?? 'Product' }}
            </td>
            <td align="center" style="padding:12px 8px;border-top:1px solid #e3f0ea;font-size:14px;color:#6aab8f;">{{ $item->quantity }}</td>
            <td align="right" style="padding:12px 14px;border-top:1px solid #e3f0ea;font-size:14px;color:#274a3d;">₹{{ number_format($item->line_total, 2) }}</td>
        </tr>
    @endforeach
</table>
