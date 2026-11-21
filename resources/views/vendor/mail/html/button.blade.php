@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
<?php
$buttonColor = match ($color) {
    'success' => '#10b981',
    'error' => '#ef4444',
    default => '#ff5500', // Orange for primary
};
?>
<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<a href="{{ $url }}" class="button button-{{ $color }}" target="_blank" rel="noopener" style="background-color: {{ $buttonColor }}; border-color: {{ $buttonColor }};">{{ $slot }}</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
