@props([
    'name' => null,
    'alias' => null,
    'class' => '',
    'size' => 'w-5 h-5',
])

@php
    // If alias is provided, look up the icon name from config
    if ($alias) {
        // Parse alias like 'actions.edit' or 'navigation.dashboard'
        $parts = explode('.', $alias);
        $category = $parts[0] ?? null;
        $key = $parts[1] ?? null;
        
        if ($category && $key) {
            $iconName = config("icons.{$category}.{$key}");
            if ($iconName !== null) {
                $name = $iconName;
            }
        }
    }
    
    // Build the full path to the SVG file
    // The name can now be either:
    // 1. A flat filename: 'dashboard-square10' -> public/vendor/lineicons/dashboard-square10.svg
    // 2. A path: 'pro/business/outlined/dashboard-square10' -> public/vendor/lineicons/pro/business/outlined/dashboard-square10.svg
    $svgPath = public_path("vendor/lineicons/{$name}.svg");
    
    if (!file_exists($svgPath)) {
        $svg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" fill="currentColor" opacity="0.1"/></svg>';
    } else {
        $svg = file_get_contents($svgPath);
        
        // Remove existing fill attributes from the SVG content
        $svg = preg_replace('/fill="[^"]*"/', '', $svg);
        
        // Add dark/light mode fill using currentColor
        $svg = str_replace('<path ', '<path fill="currentColor" ', $svg);
        $svg = str_replace('<circle ', '<circle fill="currentColor" ', $svg);
        $svg = str_replace('<rect ', '<rect fill="currentColor" ', $svg);
        $svg = str_replace('<polygon ', '<polygon fill="currentColor" ', $svg);
        
        // Remove width and height attributes to allow CSS sizing
        $svg = preg_replace('/width="[^"]*"/', '', $svg);
        $svg = preg_replace('/height="[^"]*"/', '', $svg);
        
        // Add class attribute to SVG tag
        $classes = trim("{$size} {$class}");
        $svg = str_replace('<svg ', "<svg class=\"{$classes}\" ", $svg);
    }
@endphp

{!! $svg !!}
