@props(['percent' => 0])

<div class="h-2 rounded-full bg-cream-200 overflow-hidden">
    <div class="h-full rounded-full"
         style="width: {{ max(2, min(100, (int) $percent)) }}%; background: linear-gradient(90deg, #C58A1B, #E8A92B, #E88753);">
    </div>
</div>
